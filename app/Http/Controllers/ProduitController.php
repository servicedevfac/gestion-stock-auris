<?php

namespace App\Http\Controllers;

use App\Models\MouvementStock;
use App\Models\Produit;
use Illuminate\Http\Request;

class ProduitController extends Controller
{

public function index()
{
    $produits = Produit::with('mouvements')->paginate(15);
    return view('admin.produits.index', compact('produits'));
}


    public function create()
    {
        return view('admin.produits.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prix' => 'required|numeric|min:0',
            'seuil_alerte' => 'required|integer|min:0',
        ]);

        Produit::create($request->all());

        return redirect()->route('produits.index')->with('success', 'Produit créé avec succès.');
    }


    public function show(Produit $produit)
    {
        return view("admin.produits.show", compact('produit'));
    }

    public function edit(Produit $produit)
    {
        return view('admin.produits.edit', compact('produit'));
    }


    public function update(Request $request, Produit $produit)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prix' => 'required|numeric|min:0',
            'seuil_alerte' => 'required|integer|min:0',
        ]);

        $produit->update($request->all());

        return redirect()->route('produits.index')->with('success', 'Produit mis à jour avec succès.');
    }

    public function destroy(Produit $produit)
    {
        $produit->delete();

        return redirect()->route('produits.index')->with('success', 'Produit supprimé avec succès.');
    }
    public function indexAlerte()
{
    // Charge tous les produits avec leurs mouvements
    $produits = Produit::with('mouvements')->get();

    // Filtre ceux dont le stock est inférieur ou égal au seuil
    $produitsAlerte = $produits->filter(function ($produit) {
        $entree = $produit->mouvements->where('type_mouvement', 'entrée')->sum('quantite');
        $sortie = $produit->mouvements->where('type_mouvement', 'sortie')->sum('quantite');
        $stock = $entree - $sortie;

        return $stock <= $produit->seuil_d_alerte;
    });

    return view('produits.alertes', compact('produitsAlerte'));
}

}
