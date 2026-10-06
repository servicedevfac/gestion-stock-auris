<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProduitController extends Controller
{
    /**
     * Vérification des droits administrateur
     */
    private function checkAdminPermission(string $action = 'manage'): void
    {
        $user = Auth::user();
        if (!$user->hasAnyRole(['Administrateur', 'super admin']) && !$user->can("{$action} produit")) {
            abort(403, 'Accès refusé. Seul un administrateur est autorisé à effectuer cette action.');
        }
    }

    /**
     * Liste paginée des produits
     */
    public function index()
    {
        $produits = Produit::with('mouvements')->orderBy('nom')->paginate(15);
        return view('admin.produits.index', compact('produits'));
    }

    /**
     * Formulaire de création d'un produit
     */
    public function create()
    {
        $this->checkAdminPermission('create');
        return view('admin.produits.create');
    }

    /**
     * Enregistrement d'un produit
     */
    public function store(Request $request)
    {
        $this->checkAdminPermission('create');

        $validated = $request->validate([
            'nom'          => 'required|string|max:255',
            'prix'         => 'required|numeric|min:0',
            'seuil_alerte' => 'required|integer|min:0',
        ]);

        Produit::create([
            'nom'             => $validated['nom'],
            'prix'            => $validated['prix'],
            'seuil_alerte'    => $validated['seuil_alerte'],
            'alerte_envoyee'  => false,
        ]);

        return redirect()->route('produits.index')->with('success', 'Produit créé avec succès.');
    }

    /**
     * Affichage d'un produit
     */
    public function show(Produit $produit)
    {
        $produit->load(['mouvements.user', 'details']);
        return view('admin.produits.show', compact('produit'));
    }

    /**
     * Formulaire de modification d'un produit
     */
    public function edit(Produit $produit)
    {
        $this->checkAdminPermission('edit');
        return view('admin.produits.edit', compact('produit'));
    }

    /**
     * Mise à jour d'un produit
     */
    public function update(Request $request, Produit $produit)
    {
        $this->checkAdminPermission('edit');

        $validated = $request->validate([
            'nom'          => 'required|string|max:255',
            'prix'         => 'required|numeric|min:0',
            'seuil_alerte' => 'required|integer|min:0',
        ]);

        $produit->update([
            'nom'          => $validated['nom'],
            'prix'         => $validated['prix'],
            'seuil_alerte' => $validated['seuil_alerte'],
        ]);

        return redirect()->route('produits.index')->with('success', 'Produit mis à jour avec succès.');
    }

    /**
     * Suppression sécurisée d'un produit
     */
    public function destroy(Produit $produit)
    {
        $this->checkAdminPermission('delete');

        // Intégrité référentielle : empêcher la suppression si le produit a un historique
        if ($produit->details()->count() > 0) {
            return redirect()->route('produits.index')->with(
                'error',
                'Impossible de supprimer ce produit : il figure dans des factures ou ventes existantes.'
            );
        }

        if ($produit->mouvements()->count() > 0) {
            return redirect()->route('produits.index')->with(
                'error',
                'Impossible de supprimer ce produit : des mouvements de stock y sont rattachés.'
            );
        }

        $produit->delete();

        return redirect()->route('produits.index')->with('success', 'Produit supprimé avec succès.');
    }

    /**
     * Liste des produits en alerte de stock
     */
    public function indexAlerte()
    {
        $produits = Produit::with('mouvements')->get();

        $produitsAlerte = $produits->filter(function ($produit) {
            return $produit->stock_actuel <= $produit->seuil_alerte;
        });

        return view('produits.alertes', compact('produitsAlerte'));
    }
}
