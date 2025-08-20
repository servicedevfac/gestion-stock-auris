<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportationEcontroller;
use App\Http\Controllers\HoraireController;
use App\Http\Controllers\MouvementStockController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserContoller; // ✅ Correction du nom
use App\Http\Controllers\VenteController;
use Illuminate\Support\Facades\Route;

// Page de connexion par défaut
Route::get('/', fn () => view('auth.login'));
// Groupe pour utilisateurs authentifiés
Route::middleware('auth')->group(function () {
    Route::get('/clients/search', [ClientController::class, 'search'])->name('clients.search');
    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/ventes/{id}/ticket', [VenteController::class, 'imprimerTicket'])->name('ventes.ticket');


    // Tableau de bord
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Clients (hors suppression)
    Route::resource('clients', ClientController::class)->except('destroy');

    // Produits (uniquement index)
    Route::get('produits', [ProduitController::class, 'index'])->name('produits.index');

    // Mouvements de stock (uniquement index)
    Route::get('mouvementStocks', [MouvementStockController::class, 'index'])->name('mouvementStocks.index');
    // Exportation Excel
    Route::post('/simple-exel/expot', [ExportationEcontroller::class, 'exportation'])->name('export');
    // Ventes (affichage + création)
    Route::get('/ventes', [VenteController::class, 'index'])->name('ventes.index');
    Route::get('/ventes-filtrees', [VenteController::class, 'ventesFiltrees'])->name('ventes.filtrees');
    Route::get('/ventes-export-pdf', [VenteController::class, 'exportPDF'])->name('ventes.export.pdf');
    Route::get('/ventes/create', [VenteController::class, 'create'])->name('ventes.create')->middleware('verifier.heure.vente');
    Route::post('/ventes', [VenteController::class, 'store'])->name('ventes.store')->middleware('verifier.heure.vente');
    Route::get('/ventes/{vente}', [VenteController::class, 'show'])->name('ventes.show');
    // Gestion des horaires
    Route::delete('/ventes/{vente}', [VenteController::class, 'destroy'])->name('ventes.destroy');
    Route::post('/ventes/{vente}/annuler', [VenteController::class, 'annulerVente'])->name('ventes.annuler');
    // Suppression des clients
    Route::delete('clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
    // Produits (sauf index)
    Route::resource('produits', ProduitController::class)->except('index');
    // Mouvements de stock (hors index)
    Route::resource('mouvementStocks', MouvementStockController::class)->only( 'create', 'store', 'update', 'destroy');
    Route::get('/mouvementStocks/{mouvementStock}/edit', [MouvementStockController::class, 'edit'])->name('mouvementStocks.edit');
    // Gestion des horaires
    Route::get('/horaires', [HoraireController::class, 'index'])->name('admin.horaires.index');
    Route::get('/horaires/edit', [HoraireController::class, 'edit'])->name('admin.horaires.edit');
    Route::post('/horaires', [HoraireController::class, 'update'])->name('admin.horaires.update');
    Route::resource('users', UserContoller::class);
    Route::post('/users/{user}/toggle', [UserContoller::class, 'toggle'])->name('users.toggle');
});
// Groupe pour les super administrateurs
Route::middleware(['web', 'verified', 'auth', 'is.super.admin'])->group(function () {
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::get('/admin/horaires/historique', [HoraireController::class, 'historique'])->name('admin.horaires.historique');
});
require __DIR__.'/auth.php';
