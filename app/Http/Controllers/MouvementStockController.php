<?php

namespace App\Http\Controllers;

use App\Models\MouvementStock;
use App\Models\Produit;
use App\Models\User;
use App\Notifications\StockAlerte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class MouvementStockController extends Controller
{
    /**
     * Affiche la liste des mouvements de stock.
     */
    public function index()
    {

        $produits = Produit::all(); // Récupère tous les produits pour le champ
        $users = User::all(); // Récupère tous les utilisateurs pour le champ

        $mouvements = MouvementStock::orderBy('created_at', 'desc')->take(200)->get();
        return view('admin.stocks.index', compact('mouvements', 'produits', 'users'));

    }

    /**
     * Affiche le formulaire de création d'un mouvement de stock.
     */
    public function create()
    {
        $produits = Produit::all();
        $users = User::all(); // Récupère tous les utilisateurs pour le champ utilisateur
        return view('admin.stocks.create', compact('produits', 'users'));
    }
public function store(Request $request)
{
    // Validation
    $request->validate([
        'produit_id' => 'required|exists:produits,id',
        'type_mouvement' => 'required|in:entree,sortie',
        'quantite' => 'required|integer|min:1',
        'motif' => 'required|string|max:255',
        'date_mouvement' => 'required|date',
    ]);

    // Récupérer le produit
    $produit = Produit::find($request->produit_id);
    if (!$produit) {
        return redirect()->back()->withErrors(['produit_id' => 'Produit non trouvé.']);
    }
   // dd($produit);

    // Créer le mouvement
    $mouvement = new MouvementStock();
    $mouvement->produit_id = $produit->id;
    $mouvement->type_mouvement = $request->type_mouvement;
    $mouvement->quantite = $request->quantite;
    $mouvement->motif = $request->motif;
    $mouvement->date_mouvement = $request->date_mouvement;
    $mouvement->user_id = Auth::id();
    $mouvement->vente_id = null; // seulement pour ventes
    $mouvement->save();

    // ⚡ Recharger le produit et sa relation pour recalcul correct du stock
    $produit->load('mouvements');

    //Vérifier le stock et envoyer la notification si seuil atteint
    if ( $produit->stockActuel < $produit->seuil_alerte && $produit->alerte_envoyee==false) {
        $admins = User::role('Administrateur')->get();
        Notification::send($admins, new StockAlerte($produit));

        $produit->alerte_envoyee = true;
        $produit->last_alerted_at = now();
        $produit->save();

    } else {
        $produit->alerte_envoyee = false;
        $produit->last_alerted_at = null;
        $produit->save();
    }
   // dd($produit->alerte_envoyee);
    return redirect()->route('mouvementStocks.index')
                     ->with('success', 'Mouvement enregistré avec succès.');
}


    public function show($id)
    {
        $mouvementStock = MouvementStock::find($id);
        return view('admin.stocks.show', compact('mouvementStock'));
    }

public function edit(MouvementStock $mouvementStock)
{
    $produits = Produit::all();
    $users = User::all();

    return view('admin.stocks.edit', compact('mouvementStock', 'produits', 'users'));
}


    /**
     * Met à jour un mouvement de stock existant en base de données.
     */
    public function update(Request $request, MouvementStock $mouvementStock)
    {
        $request->validate([
    'produit_id' => 'required|exists:produits,id',
    'user_id' => 'required|exists:users,id',
    'type_mouvement' => 'required|in:entree,sortie',
    'quantite' => 'required|numeric|min:1',
    'motif' => 'required|string|max:255',
    'date_mouvement' => 'required|date',
]);


        $mouvementStock->update($request->all());

        return redirect()->route('mouvementStocks.index')->with('success', 'Mouvement de stock mis à jour avec succès.');
    }

    /**
     * Supprime un mouvement de stock de la base de données.
     */
    public function destroy(MouvementStock $mouvementStock)
{
    return redirect()->route('mouvementStocks.index')
        ->with('error', 'La suppression des mouvements de stock est interdite.');
}


    /**
     * Affiche une liste filtrée des mouvements de stock selon les critères.
     */// fin de la méthode filter
}
