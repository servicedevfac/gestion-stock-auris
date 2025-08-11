<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Detail_vente;
use App\Models\Horaire;
use App\Models\MouvementStock;
use App\Models\Produit;
use App\Models\User;
use App\Models\Vente;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VenteController extends Controller
{
    public function index(Request $request)
    {
        if(Auth::user()->hasRole('vendeur')) {

            $ventes = Vente::where('user_id', Auth::user()->id)->paginate(15);
        } else {

        $ventes = Vente::with(['client', 'user'])->orderByDesc('created_at')->paginate(15);
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
            $ventes = $ventes->where(function($query) use ($q) {
                $query->where('code_recu', 'like', "%$q%")
                      ->orWhereHas('client', function($sub) use ($q) {
                          $sub->where('nom', 'like', "%$q%")
                               ->orWhere('prenom', 'like', "%$q%")
                               ->orWhere('email', 'like', "%$q%") ;
                      })
                      ->orWhereHas('user', function($sub) use ($q) {
                          $sub->where('nom', 'like', "%$q%")
                               ->orWhere('prenom', 'like', "%$q%")
                               ->orWhere('email', 'like', "%$q%") ;
                      });
            });
        }

        return view('admin.ventes.index', compact('ventes'));
    }

    public function create()
    {
        $clients = Client::all();
        $utilisateurs = User::all();
        $produits = Produit::all();
        return view('admin.ventes.create', compact('clients', 'utilisateurs', 'produits'));
    }
public function store(Request $request)
{

    $request->validate([
        'client_id' => 'required|exists:clients,id',
        'montant_total' => 'required|numeric|min:0',
        'remise' => 'nullable|numeric|min:0',
        'date_vente' => 'required|date',
        'mode_paiement' => 'required|string|max:50',
    ]);

            $date = now();
            $annee = $date->format('Y');
            $mois = $date->format('m');
            $jour= $date->format('d');
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
                // Créer la vente principale
                $vente = Vente::create([
            'client_id' => $request->client_id,
            'user_id' => Auth::user()->id,
            'date_vente' => $request->date_vente,
            'montant_total' => $request->montant_total,
            'remise' => $request->remise,
            'mode_paiement' => $request->mode_paiement,
            'code_recu' => isset($code_recu) ? $code_recu : '',
        ]);


        // Vérification du stock pour chaque produit
        foreach ($request->produits as $produit) {
            $produitModel = Produit::find($produit['produit_id']);
            $stockActuel = $produitModel->mouvements()->where('type_mouvement', 'entree')->sum('quantite')
                - $produitModel->mouvements()->where('type_mouvement', 'sortie')->sum('quantite');
            if ($produit['quantite'] > $stockActuel) {
            return redirect()->back()->with('error', 'Stock insuffisant pour le produit : ' . $produitModel->nom)->withInput();

            }
        }

        // Générer le PDF après la création des détails
        foreach ($request->produits as $produit) {
            $total = $produit['quantite'] * $produit['prix'];
            Detail_Vente::create([
                'vente_id' => $vente->id,
                'produit_id' => $produit['produit_id'],
                'quantite' => $produit['quantite'],
                'prix' => $produit['prix'],
                'total' => $total,
            ]);
            $stock=MouvementStock::create([
                'produit_id' => $produit['produit_id'],
                'user_id' => Auth::user()->id,
                'quantite' => $produit['quantite'],
                'motif' => 'Vente',
                'type_mouvement' => 'sortie',
                'date_mouvement' => $request->date_vente,
            ]);

        }

        $vente->load(['client', 'user', 'details.produit']);
        $pdf = Pdf::loadView('admin.ventes.recu_pdf', ['vente' => $vente]);
        $filename = 'recu_vente_'.$vente->client->nom.'_'.$vente->code_recu.'.pdf';
        Storage::put('public/recus/' . $filename, $pdf->output());
        $vente->update(['pdf_recu' => 'recus/' . $filename]);
        DB::commit();
        return redirect()->route('ventes.index')->with('success', 'Vente enregistrée avec succès.');

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





}







