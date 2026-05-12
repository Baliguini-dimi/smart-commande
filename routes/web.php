<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// ─── Page d'accueil ──────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
});

// ─── Routes Auth (générées par Breeze) ───────────────────
require __DIR__.'/auth.php';

// ─── Dashboard Gérant ────────────────────────────────────
Route::middleware(['auth', 'gerant'])
    ->prefix('dashboard')
    ->name('gerant.')
    ->group(function () {

    // Tableau de bord
    Route::get('/', [App\Http\Controllers\Gerant\DashboardController::class, 'index'])
         ->name('dashboard');

    // Commandes
    Route::resource('orders', App\Http\Controllers\Gerant\OrderController::class)
         ->only(['index', 'show', 'update']);

    // Menus
    Route::resource('menus', App\Http\Controllers\Gerant\MenuController::class);

    // Tables
    Route::resource('tables', App\Http\Controllers\Gerant\TableController::class);

    // Regénérer QR code d'une table
    Route::post('/tables/{table}/regenerate',
        [App\Http\Controllers\Gerant\TableController::class, 'regenerateQr'])
        ->name('tables.regenerate');

    // Statistiques
    Route::get('/analytics',
        [App\Http\Controllers\Gerant\DashboardController::class, 'analytics'])
         ->name('analytics');

    // Restaurant
    Route::get('/restaurant/edit',
        [App\Http\Controllers\Gerant\RestaurantController::class, 'edit'])
         ->name('restaurant.edit');

    Route::put('/restaurant',
        [App\Http\Controllers\Gerant\RestaurantController::class, 'update'])
         ->name('restaurant.update');

    // Abonnement
    Route::get('/subscription',
        [App\Http\Controllers\Gerant\SubscriptionController::class, 'index'])
         ->name('subscription');
});

// ─── Panel Super Admin ───────────────────────────────────
Route::middleware(['auth', 'super_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

});

// ─── Interface Client (QR Code) — sans authentification ──
Route::get('/menu/{slug}/table/{tableNumber}', function ($slug, $tableNumber) {
    return view('client.menu', compact('slug', 'tableNumber'));
})->name('client.menu');