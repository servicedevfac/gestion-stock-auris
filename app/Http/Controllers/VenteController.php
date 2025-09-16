<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Detail_vente;
use App\Models\MouvementStock;
use App\Models\Produit;
use App\Models\User;
use App\Models\Vente;
use App\Notifications\StockAlerte;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use App\Models\Paiement;


class VenteController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->hasRole('Gestionnaire')) {
            $ventes = Vente::where('user_id', Auth::user()->id);

        } else {
            $ventes = Vente::with(['client', 'user'])->orderByDesc('created_at');
        }

        // Filtrage par période
        if ($request->periode && $request->date_debut && $request->date_fin) {
            $dateDebut = $request->date_debut;
            $dateFin = $request->date_fin;
            $ventes = $ventes->whereBetween('created_at', [$dateDebut, $dateFin]);
        }

        // Recherche textuelle
        if ($request->q) {
            $q = $request->q;
            $ventes = $ventes->where(function ($query) use ($q) {
                $query->where('code_recu', 'like', "%$q%")
                    ->orWhereHas('client', function ($sub) use ($q) {
                        $sub->where('nom', 'like', "%$q%")
                            ->orWhere('prenom', 'like', "%$q%")
                            ->orWhere('email', 'like', "%$q%");
                    })
                    ->orWhereHas('user', function ($sub) use ($q) {
                        $sub->where('nom', 'like', "%$q%")
                            ->orWhere('prenom', 'like', "%$q%")
                            ->orWhere('email', 'like', "%$q%");
                    });
            });
        }

        // ⚡ On termine par la pagination
        $ventes = $ventes->paginate(10);

        return view('admin.ventes.index', compact('ventes'));
    }




    public function create()
    {
        $clients = Client::all();
        $utilisateurs = User::all();
        $produits = Produit::all();
        return view('admin.ventes.create', compact('clients', 'utilisateurs', 'produits'));
    }


    private function stockActuel(Produit $produit): int
    {
        $entrees = $produit->mouvements()->where('type_mouvement', 'entree')->sum('quantite');
        $sorties = $produit->mouvements()->where('type_mouvement', 'sortie')->sum('quantite');
        return (int) ($entrees - $sorties);
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'montant_total' => 'required|numeric|min:0',
            'remise' => 'nullable|numeric|min:0',
            'date_vente' => 'required|date',
            'mode_paiement' => 'required|string|max:50',
            'produits' => 'required|array|min:1',
            'produits.*.produit_id' => 'required|exists:produits,id',
            'produits.*.quantite' => 'required|integer|min:1',
            'produits.*.prix' => 'required|numeric|min:0',

        ]);

        $date = now();
        $annee = $date->format('Y');
        $mois = $date->format('m');
        $jour = $date->format('d');
        // Vérifier le dernier code reçu pour générer le nouveau code
        $dernierVente = Vente::whereYear('created_at', $annee)
            ->whereMonth('created_at', $mois)
            ->orderByDesc('id')
            ->first();
        $numero = 1;
        if ($dernierVente && preg_match('/RECU_\\d{6}_(\\d{4})/', $dernierVente->code_recu, $matches)) {
            $numero = intval($matches[1]) + 1;
        }
        $code_recu = 'RECU_' . $annee . $mois . '_' . str_pad($numero, 4, '0', STR_PAD_LEFT);

        // Vérifier l'unicité du code_recu
        while (Vente::where('code_recu', $code_recu)->exists()) {
            $numero++;
            $code_recu = 'RECU_' . $annee . $mois . '_' . str_pad($numero, 4, '0', STR_PAD_LEFT);
        }


        DB::beginTransaction();
        try {
                                // Calcul du montant payé et reste à payer
                    $montantTotal = $request->montant_total;
                    $montantPaye = $request->montant_paye ?? 0; // Valeur envoyée ou 0 par défaut
                    $resteAPayer = $montantTotal - $montantPaye;

                    // Sécurité : éviter les valeurs négatives
                    if ($resteAPayer < 0) {
                        $resteAPayer = 0;
                    }
                    $estPaye = ($resteAPayer == 0);

                    $vente = Vente::create([
                        'client_id' => $request->client_id,
                        'user_id' => Auth::id(),
                        'date_vente' => $request->date_vente,
                        'montant_total' => $montantTotal,
                        'remise' => $request->remise,
                        'mode_paiement' => $request->mode_paiement,
                        'code_recu' => $code_recu,
                        'est_paye' => $estPaye,
                        'montant_paye' => $montantPaye,
                        'reste_a_payer' => $resteAPayer,
                    ]);
                    // Création du paiement
                    Paiement::create([
                        'vente_id' => $vente->id,
                        'montant' => $montantPaye,
                        'mode_paiement' => $request->mode_paiement,
                        'date_paiement' => $request->date_vente,
                        'reste_a_payer'=>$resteAPayer,
                    ]);

            // 1) Vérif stock AVANT (tu avais déjà, je garde et fiabilise un peu)
            $erreurs = [];
            foreach ($request->produits as $p) {
                $prod = Produit::findOrFail($p['produit_id']);
                $stockActuel = $this->stockActuel($prod);
                if ((int) $p['quantite'] > $stockActuel) {
                    $erreurs[] = "Stock insuffisant pour le produit : {$prod->nom} stock disponible:{$prod->stock_actuel}";
                }
            }
            if (!empty($erreurs)) {
                DB::rollBack();
                return back()->withInput()->withErrors($erreurs);
            }

            // 2) Création lignes + mouvements + collecte des produits à alerter
            $itemsAlerte = [];   // pour le mail groupé
            $produitsAFlag = []; // pour mettre alerte_envoyee = true

            foreach ($request->produits as $p) {
                $total = (int) $p['quantite'] * (float) $p['prix'];

                Detail_Vente::create([
                    'vente_id' => $vente->id,
                    'produit_id' => $p['produit_id'],
                    'quantite' => $p['quantite'],
                    'prix' => $p['prix'],
                    'total' => $total,
                    'est_paye' => $request->est_paye,
                ]);
                MouvementStock::create([
                    'produit_id' => $p['produit_id'],
                    'user_id' => Auth::id(),
                    'quantite' => $p['quantite'],
                    'motif' => 'Vente',
                    'type_mouvement' => 'sortie',
                    'date_mouvement' => $request->date_vente,
                ]);

                // Recalculer le stock APRES sortie
                $prod = Produit::findOrFail($p['produit_id']);
                $stockActuel = $this->stockActuel($prod);

                // Si seuil franchi et pas déjà alerté → on programme l’alerte
                if (isset($prod->seuil_alerte) && $stockActuel <= (int) $prod->seuil_alerte) {
                    $itemsAlerte[] = [
                        'id' => $prod->id,  // 👈 clé manquante
                        'nom' => $prod->nom,
                        'stock' => $stockActuel,
                        'seuil' => (int) $prod->seuil_alerte,
                        'url' => url('/produits/' . $prod->id),
                    ];
                    $produitsAFlag[] = $prod->id;
                }
            }

            // 3) PDF
            $vente->load(['client', 'user', 'details.produit']);
            $pdf = Pdf::loadView('admin.ventes.recu_pdf', ['vente' => $vente]);
            $filename = 'recu_vente_'. $vente->client->nom . '_' . $vente->code_recu . '.pdf';
            Storage::put('public/recus/' . $filename, $pdf->output());
            $vente->update(['pdf_recu' => 'recus/' . $filename]);
            // 4) Envoi mail groupé aux admins si nécessaire
            if (!empty($itemsAlerte)) {
                // Spatie roles (tu l’utilises déjà avec hasRole)
                $admins = User::role('Administrateur')->get(); // ou ->where('is_admin', true)->get();
                Notification::sendNow($admins, new StockAlerte($itemsAlerte));
                // Marquer les produits comme alertés (anti-spam)
                Produit::whereIn('id', $produitsAFlag)->update([
                    'alerte_envoyee' => true,
                    'last_alerted_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('ventes.index')
                ->with('success', 'Vente enregistrée avec succès.')
                // chemin public/storage → ne pas doubler "public"
                ->with('recu_url', asset('storage/recus/' . $filename));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }


    public function show(Vente $vente)
    {
        $vente->load(['client', 'user', 'details.produit']);
        return view('admin.ventes.detail_vente', compact('vente'));
    }
    public function edit(Vente $vente)
    {
        $clients = Client::all();
        $utilisateurs = User::all();
        $produits = Produit::all();

        return view('admin.ventes.edit', compact('vente', 'clients', 'utilisateurs', 'produits'));
    }
    public function update(Request $request, Vente $vente)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'utilisateur_id' => 'required|exists:users,id',
            'montant_total' => 'required|numeric|min:0',
            'remise' => 'nullable|numeric|min:0',
            'date_vente' => 'required|date',
            'mode_paiement' => 'required|string|max:50',
        ]);

        $vente->update($request->all());

        return redirect()->route('admin.ventes.index')->with('success', 'Vente mise à jour avec succès');
    }
    public function destroy(Vente $vente)
    {
        // Supprimer le PDF associé
        if ($vente->pdf_recu) {
            Storage::delete('public/' . $vente->pdf_recu);
        }
        // Supprimer la vente
        $vente->delete();
        $vente->details()->delete();
        return redirect()->route('admin.ventes.index')->with('success', 'Vente supprimée avec succès');
    }
    public function annulerVente($id, Request $request)
    {
        $vente = Vente::with('details')->findOrFail($id);


        if ($vente->statut === 'annulee') {
            return back()->with('error', 'Cette vente est déjà annulée.');
        }

        DB::beginTransaction();

        try {

            $vente->update([
                'statut' => 'annulee',
            ]);


            foreach ($vente->details as $ligne) {
                MouvementStock::create([
                    'produit_id' => $ligne->produit_id,
                    'user_id' => Auth::user()->id,
                    'quantite' => $ligne->quantite,
                    'motif' => 'Annulation vente #' . $vente->id,
                    'type_mouvement' => 'entree',
                    'date_mouvement' => now(),
                ]);
            }
            DB::commit();

            return redirect()->route('ventes.index')->with('success', 'Vente annulée et stock restauré.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l’annulation : ' . $e->getMessage());
        }
    }
    public function imprimerTicket($id)
    {
        $vente = Vente::with(['client', 'details.produit', 'user'])->findOrFail($id);

$pdf = PDF::loadView('admin.ventes.recu_ticket', compact('vente'))
    ->setPaper([0, 0, 200.77, 600], 'portrait')
    ->setOptions([
        'isRemoteEnabled' => true,
        'isHtml5ParserEnabled' => true,
        'defaultFont' => 'Courier',
        'margin-right' => 0,
        'margin-left' => 0,
    ]);

            return $pdf->stream('vente_' . $vente->code_recu . '.pdf');
    }

    public function ventesFiltrees(Request $request)
    {
        // Filtrage par période
        $dateDebut = $request->date_debut ? Carbon::parse($request->date_debut)->startOfDay() : Carbon::now()->subMonth()->startOfDay();
        $dateFin = $request->date_fin ? Carbon::parse($request->date_fin)->endOfDay() : Carbon::now()->endOfDay();

        // Calcul de la différence en jours
        $diffDays = $dateDebut->diffInDays($dateFin);
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // SQLite n'a pas DATE_FORMAT, on utilise strftime
            if ($diffDays <= 31) {
                $dateExpression = "strftime('%Y-%m-%d', created_at)"; // par jour
            } elseif ($diffDays <= 365) {
                $dateExpression = "strftime('%Y-%m', created_at)";    // par mois
            } else {
                $dateExpression = "strftime('%Y', created_at)";       // par année
            }
        } else {
            // MySQL / MariaDB
            if ($diffDays <= 31) {
                $dateExpression = "DATE_FORMAT(created_at, '%Y-%m-%d')";
            } elseif ($diffDays <= 365) {
                $dateExpression = "DATE_FORMAT(created_at, '%Y-%m')";
            } else {
                $dateExpression = "DATE_FORMAT(created_at, '%Y')";
            }
        }

        if (Auth::user()->hasRole('Gestionnaire')) {
            $ventes = DB::table('ventes')->select(
                DB::raw("$dateExpression as periode"),
                DB::raw("SUM(montant_total) as total")
                )
                ->where('user_id', Auth::user()->id)
                ->where('statut', 'valide')
                ->where('est_paye', true)
            ->whereBetween('created_at', [$dateDebut, $dateFin]);

        } else {

            $ventes = DB::table('ventes')->select(
                DB::raw("$dateExpression as periode"),
                DB::raw("SUM(montant_total) as total")
            )
                ->where('statut', 'valide')
                ->where('est_paye', true)
                ->whereBetween('created_at', [$dateDebut, $dateFin]);
        }

        // Recherche textuelle (avec jointures)
        if ($request->q) {
            $q = $request->q;
            $ventes->join('clients', 'ventes.client_id', '=', 'clients.id')
                ->join('users', 'ventes.user_id', '=', 'users.id')
                ->where(function ($query) use ($q) {
                    $query->where('code_recu', 'like', "%$q%")
                        ->orWhere('clients.nom', 'like', "%$q%")
                        ->orWhere('clients.prenom', 'like', "%$q%")
                        ->orWhere('clients.email', 'like', "%$q%")
                        ->orWhere('users.nom', 'like', "%$q%")
                        ->orWhere('users.prenom', 'like', "%$q%")
                        ->orWhere('users.email', 'like', "%$q%");
                });
        }

        $ventes = $ventes
            ->groupBy(DB::raw("$dateExpression"))
            ->orderBy('periode', 'asc')
            ->get();

        // Convertir les données pour le graphique
        $labels = $ventes->pluck('periode')->toArray();
        $data = $ventes->pluck('total')->toArray();

        // Déboguer les données
        \Illuminate\Support\Facades\Log::info('Données filtrées:', ['labels' => $labels, 'data' => $data]);

        return response()->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }

    public function exportPDF(Request $request)
    {
        // Filtrage par période
        $dateDebut = $request->date_debut ? Carbon::parse($request->date_debut)->startOfDay() : Carbon::now()->subMonth()->startOfDay();
        $dateFin = $request->date_fin ? Carbon::parse($request->date_fin)->endOfDay() : Carbon::now()->endOfDay();

        // Calcul de la différence en jours
        $diffDays = $dateDebut->diffInDays($dateFin);
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // SQLite n'a pas DATE_FORMAT, on utilise strftime
            if ($diffDays <= 31) {
                $dateExpression = "strftime('%Y-%m-%d', created_at)"; // par jour
            } elseif ($diffDays <= 365) {
                $dateExpression = "strftime('%Y-%m', created_at)";    // par mois
            } else {
                $dateExpression = "strftime('%Y', created_at)";       // par année
            }
        } else {
            // MySQL / MariaDB
            if ($diffDays <= 31) {
                $dateExpression = "DATE_FORMAT(created_at, '%Y-%m-%d')";
            } elseif ($diffDays <= 365) {
                $dateExpression = "DATE_FORMAT(created_at, '%Y-%m')";
            } else {
                $dateExpression = "DATE_FORMAT(created_at, '%Y')";
            }
        }
        if (Auth::user()->hasRole('Gestionnaire')) {
            $ventes = DB::table('ventes')->select(
                DB::raw("$dateExpression as periode"),
                DB::raw("SUM(montant_total) as total")
                )
                ->where('user_id', Auth::user()->id)
                ->where('statut', 'valide')
                ->where('est_paye', true)
            ->whereBetween('created_at', [$dateDebut, $dateFin]);

        } else {

            $ventes = DB::table('ventes')->select(
                DB::raw("$dateExpression as periode"),
                DB::raw("SUM(montant_total) as total")
            )
                ->where('statut', 'valide')
                ->where('est_paye', true)
                ->whereBetween('created_at', [$dateDebut, $dateFin]);
        }

        // Recherche textuelle (avec jointures)
        if ($request->q) {
            $q = $request->q;
            $ventes->join('clients', 'ventes.client_id', '=', 'clients.id')
                ->join('users', 'ventes.user_id', '=', 'users.id')
                ->where(function ($query) use ($q) {
                    $query->where('code_recu', 'like', "%$q%")
                        ->orWhere('clients.nom', 'like', "%$q%")
                        ->orWhere('clients.prenom', 'like', "%$q%")
                        ->orWhere('clients.email', 'like', "%$q%")
                        ->orWhere('users.nom', 'like', "%$q%")
                        ->orWhere('users.prenom', 'like', "%$q%")
                        ->orWhere('users.email', 'like', "%$q%");
                });
        }

        $ventes = $ventes
            ->groupBy(DB::raw("$dateExpression"))
            ->orderBy('periode', 'asc')
            ->get();

        // Convertir les données pour le PDF
        $labels = $ventes->pluck('periode')->toArray();
        $data = $ventes->pluck('total')->toArray();

        // Déboguer les données
        \Illuminate\Support\Facades\Log::info('Données PDF:', ['labels' => $labels, 'data' => $data]);

        $viewData = [
            'labels' => $labels,
            'data' => $data,
            'dateDebut' => $dateDebut->format('d/m/Y'),
            'dateFin' => $dateFin->format('d/m/Y'),
            'recherche' => $request->q
        ];

        try {
            $pdf = Pdf::loadView('admin.ventes.graphique_pdf', $viewData);
            return $pdf->download('graphique_ventes_' . now()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur génération PDF: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la génération du PDF: ' . $e->getMessage()], 500);
        }
    }

    public function exportPdflist(Request $request)
{
    if (Auth::user()->hasRole('Gestionnaire')) {
        $ventes = Vente::where('user_id', Auth::user()->id)->where('statut', 'valide')->where('est_paye', true);
    } else {
        $ventes = Vente::with(['client', 'user'])->orderByDesc('created_at')->where('statut','valide')->where('est_paye', true);
    }

    // Filtrage par période
    if ($request->periode && $request->date_debut && $request->date_fin) {
        $ventes = $ventes->whereBetween('created_at', [$request->date_debut, $request->date_fin]);
    }

    // Recherche textuelle
    if ($request->q) {
        $q = $request->q;
        $ventes = $ventes->where(function ($query) use ($q) {
            $query->where('code_recu', 'like', "%$q%")
                ->orWhereHas('client', function ($sub) use ($q) {
                    $sub->where('nom', 'like', "%$q%")
                        ->orWhere('prenom', 'like', "%$q%")
                        ->orWhere('email', 'like', "%$q%");
                })
                ->orWhereHas('user', function ($sub) use ($q) {
                    $sub->where('nom', 'like', "%$q%")
                        ->orWhere('prenom', 'like', "%$q%")
                        ->orWhere('email', 'like', "%$q%");
                });
        });
    }

    $ventes = $ventes->get();
     // ⚡ pas de paginate ici
     //

    $pdf = PDF::loadView('admin.ventes.pdf', compact('ventes'));
    return $pdf->download('ventes_recherche.pdf');
}
public function payer(Request $request, $id)
{
    $vente = Vente::findOrFail($id);

    $request->validate([
        'mode_paiement' => 'required|string|max:50',
        'montant_paye' => 'required|numeric|min:0',
    ]);

    $montant = $request->montant_paye;

    // Calcul du reste
    $reste = $vente->montant_total - ($vente->montant_paye + $montant);

    // Mise à jour
    $vente->montant_paye += $montant;
    $vente->reste_a_payer = max($reste, 0);
    $vente->mode_paiement = $request->mode_paiement;

    // Si le client a tout payé → marquer comme payé
    if ($vente->montant_paye >= $vente->montant_total) {
        $vente->est_paye = true;
    }

    $vente->save();

    return redirect()->back()->with('success', "Paiement enregistré : {$montant} FCFA. Reste à payer : {$vente->reste_a_payer} FCFA.");
}



}







