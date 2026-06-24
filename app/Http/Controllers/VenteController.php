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
            $ventes = Vente::where('user_id', Auth::user()->id)->orderByDesc('date_vente');

        } else {
            $ventes = Vente::with(['client', 'user'])->orderByDesc('date_vente');
        }

        // Filtrage par période
        if ($request->periode && $request->date_debut && $request->date_fin) {
            $dateDebut = $request->date_debut;
            $dateFin = $request->date_fin;
            $ventes = $ventes->whereBetween('date_vente', [$dateDebut, $dateFin]);
        }

        // Recherche textuelle
        if ($request->q) {
            $q = $request->q;
            $ventes = $ventes->where(function ($query) use ($q) {
                $query->where('code_recu', 'like', "%$q%")
                    ->orWhereHas('client', function ($sub) use ($q) {
                        $sub->where('nom', 'like', "%$q%")
                            ->orWhere('prenom', 'like', "%$q%");
                            
                    })
                    ->orWhereHas('user', function ($sub) use ($q) {
                        $sub->where('nom', 'like', "%$q%")
                            ->orWhere('prenom', 'like', "%$q%");
                
                    });
            });
        }

        // ⚡ On termine par la pagination
        $ventes = $ventes->paginate(20);

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
           /* if (!empty($itemsAlerte)) {
                // Spatie roles (tu l’utilises déjà avec hasRole)
                $admins = User::role('Administrateur')->get(); // ou ->where('is_admin', true)->get();
                Notification::sendNow($admins, new StockAlerte($itemsAlerte));
                // Marquer les produits comme alertés (anti-spam)
                Produit::whereIn('id', $produitsAFlag)->update([
                    'alerte_envoyee' => true,
                    'last_alerted_at' => now(),
                ]);
            }*/

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

$pdf = PDF::loadView('admin.ventes.recu_ticket', compact('vente'));
   // ->setPaper([0, 0, 360.77, 600], 'portrait');


            return $pdf->stream('vente_' . $vente->code_recu . '.pdf');
    }



public function ventesFiltrees(Request $request)
{
    $user = Auth::user();
    $userId = $user->id;

    $dateDebut = $request->date_debut ? Carbon::parse($request->date_debut)->startOfDay() : Carbon::now()->subMonth()->startOfDay();
    $dateFin = $request->date_fin ? Carbon::parse($request->date_fin)->endOfDay() : Carbon::now()->endOfDay();

    $periode = $request->periode ?? 'jour';
    $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();

    // ✅ Déterminer l'expression SQL correcte selon le SGBD (MySQL / SQLite)
    switch ($periode) {
        case 'jour':
            $dateExpr = "DATE(ventes.date_vente)";
            break;
        case 'semaine':
            $dateExpr = $driver === 'sqlite' ? "strftime('%Y-%W', ventes.date_vente)" : "YEARWEEK(ventes.date_vente, 1)";
            break;
        case 'mois':
            $dateExpr = $driver === 'sqlite' ? "strftime('%Y-%m', ventes.date_vente)" : "DATE_FORMAT(ventes.date_vente, '%Y-%m')";
            break;
        case 'annee':
            $dateExpr = $driver === 'sqlite' ? "strftime('%Y', ventes.date_vente)" : "YEAR(ventes.date_vente)";
            break;
        default:
            $dateExpr = "DATE(ventes.date_vente)";
    }

    // 1️⃣ Ventes
    $ventesQuery = DB::table('ventes')
        ->select(DB::raw("$dateExpr as periode"), DB::raw("SUM(ventes.montant_total) as total"))
        ->whereBetween('ventes.date_vente', [$dateDebut, $dateFin]);

    if ($user->hasRole('Gestionnaire')) {
        $ventesQuery->where('ventes.user_id', $userId);
    }

    $ventes = $ventesQuery->groupBy('periode')->orderBy('periode')->get()->keyBy('periode');

    // 2️⃣ Paiements
    $paiementsQuery = DB::table('paiements')
        ->join('ventes', 'paiements.vente_id', '=', 'ventes.id')
        ->select(DB::raw("$dateExpr as periode"), DB::raw("SUM(paiements.montant) as total"))
        ->whereBetween('paiements.created_at', [$dateDebut, $dateFin]);

    if ($user->hasRole('Gestionnaire')) {
        $paiementsQuery->where('ventes.user_id', $userId);
    }

    $paiements = $paiementsQuery->groupBy('periode')->orderBy('periode')->get()->keyBy('periode');

    // 3️⃣ Calculs finaux
    $allPeriods = $ventes->keys()->merge($paiements->keys())->unique()->sort();

    $data = [
        'labels' => [],
        'ventes' => [],
        'paiements' => [],
        'reste' => []
    ];

    foreach ($allPeriods as $periodeKey) {
    $totalVentes = $ventes->has($periodeKey) ? $ventes[$periodeKey]->total : 0;
    $totalPaiements = $paiements->has($periodeKey) ? $paiements[$periodeKey]->total : 0;

    $reste = max(0, $totalVentes - $totalPaiements);

    $data['labels'][] = $periodeKey;
    $data['ventes'][] = round($totalVentes, 2);
    $data['paiements'][] = round($totalPaiements, 2);
    $data['reste'][] = round($reste, 2);
}


    return response()->json($data);
}




public function exportPdflist(Request $request)
{
    $user = Auth::user();

    // Query de base
    $ventes = Vente::with(['client', 'user'])
        ->where('statut', 'valide');

    // Restriction gestionnaire
    if ($user->hasRole('Gestionnaire')) {
        $ventes->where('user_id', $user->id);
    }

    // 📅 Filtre période
    if ($request->filled(['date_debut', 'date_fin'])) {
        $ventes->whereBetween('date_vente', [
            $request->date_debut,
            $request->date_fin
        ]);
    }

    // 🔍 Recherche
    if ($request->filled('q')) {
        $q = $request->q;

        $ventes->where(function ($query) use ($q) {
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

    $ventes = $ventes
        ->orderByDesc('created_at')
        ->get();

    // 📄 Génération PDF
    $pdf = PDF::loadView('admin.ventes.pdf', [
        'ventes' => $ventes,
        'request' => $request
    ]);

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







