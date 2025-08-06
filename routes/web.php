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
use App\Http\Controllers\UserContoller; 
use App\Http\Controllers\VenteController;
use Illuminate\Support\Facades\Route;


//****** New  routes  *******//




Route::resource('mouvementStocks', MouvementStockController::class)->only( 'create', 'store', 'update', 'destroy');
Route::get('/mouvementStocks/{mouvementStock}/edit', [MouvementStockController::class, 'edit'])->name('mouvementStocks.edit');

// Gestion des horaires
Route::get('/horaires', [HoraireController::class, 'index'])->name('admin.horaires.index');
Route::get('/horaires/edit', [HoraireController::class, 'edit'])->name('admin.horaires.edit');
Route::post('/horaires', [HoraireController::class, 'update'])->name('admin.horaires.update');

Route::get('produits', [ProduitController::class, 'index'])->name('produits.index');

Route::resource('produits', ProduitController::class)->except('index');

// Gestion des rôles, permissions, utilisateurs





//****** Fin New  routes  *******//

// Page de connexion par défaut
Route::get('/', fn () => view('auth.login'));

// Route::get('/clients/search', [ClientController::class, 'search'])->name('clients.search');
// Groupe pour utilisateurs authentifiés
Route::middleware('auth')->group(function () {
    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Tableau de bord
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // // Clients (hors suppression)
    // Route::resource('clients', ClientController::class)->except('destroy');

    // Produits (uniquement index)
    

    // Mouvements de stock (uniquement index)
    Route::get('mouvementStocks', [MouvementStockController::class, 'index'])->name('mouvementStocks.index');

    // Exportation Excel
    Route::post('/simple-exel/expot', [ExportationEcontroller::class, 'exportation'])->name('export');

    // Ventes (affichage + création)
    Route::get('/ventes', [VenteController::class, 'index'])->name('ventes.index');
    Route::get('/ventes/create', [VenteController::class, 'create'])->name('ventes.create')->middleware('verifier.heure.vente');
    Route::post('/ventes', [VenteController::class, 'store'])->name('ventes.store')->middleware('verifier.heure.vente');
    Route::get('/ventes/{vente}', [VenteController::class, 'show'])->name('ventes.show');

});




// Groupe pour les administrateurs vérifiés
Route::middleware(['web', 'verified', 'auth', 'is.admin'])->group(function () {
    // Gestion des ventes (édition, suppression, annulation)
    Route::get('/ventes/{vente}/edit', [VenteController::class, 'edit'])->name('ventes.edit');
    Route::put('/ventes', [VenteController::class, 'update'])->name('ventes.update');
    Route::delete('/ventes/{vente}', [VenteController::class, 'destroy'])->name('ventes.destroy');
    Route::post('/ventes/{vente}/annuler', [VenteController::class, 'annulerVente'])->name('ventes.annuler');
    Route::get('/admin/horaires/historique', [HoraireController::class, 'historique'])->name('admin.horaires.historique');



    // Suppression des clients
    // Route::delete('clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

    // Produits (sauf index)
    // Route::resource('produits', ProduitController::class)->except('index');

    // Gestion des rôles, permissions, utilisateurs
    // Route::resource('roles', RoleController::class);
    // Route::resource('permissions', PermissionController::class);
    // Route::resource('users', UserContoller::class);

    // Mouvements de stock (hors index)
    // Route::resource('mouvementStocks', MouvementStockController::class)->only( 'create', 'store', 'update', 'destroy');
    // Route::get('/mouvementStocks/{mouvementStock}/edit', [MouvementStockController::class, 'edit'])->name('mouvementStocks.edit');

    // // Gestion des horaires
    // Route::get('/horaires', [HoraireController::class, 'index'])->name('admin.horaires.index');
    // Route::get('/horaires/edit', [HoraireController::class, 'edit'])->name('admin.horaires.edit');
    // Route::post('/horaires', [HoraireController::class, 'update'])->name('admin.horaires.update');
});
// Route::resource('users', UserContoller::class);









// BONNE ROUTES //




Route::middleware('auth')->group(function () {
    // Routes pour les utilisateurs authentifié
    Route::resource('clients', ClientController::class)->except('destroy');
    Route::delete('clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
    Route::get('/clients/search', [ClientController::class, 'search'])->name('clients.search');



    Route::group(['middleware' => ['role:super admin|admin']], function () {
        // Routes pour les super administrateurs
        Route::resource('users', UserContoller::class);

    });



    Route::group(['middleware' => ['role:super admin']], function () {
        // Routes pour les super administrateurs
        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class)->except('show');
    });
});


// Auth routes (login, register, etc.)
require __DIR__.'/auth.php';
