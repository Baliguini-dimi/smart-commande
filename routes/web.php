<?php

use Illuminate\Support\Facades\Route;

// ─── Page d'accueil ──────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
});

// ─── Routes Auth (Breeze) ────────────────────────────────────
require __DIR__.'/auth.php';

// ─── Dashboard Gérant ────────────────────────────────────────
Route::middleware(['auth', 'gerant'])
    ->prefix('dashboard')
    ->name('gerant.')
    ->group(function () {

    // Tableau de bord
    Route::get('/', [App\Http\Controllers\Gerant\DashboardController::class, 'index'])
         ->name('dashboard');

    // Restaurant
    Route::get('/restaurant/edit',
        [App\Http\Controllers\Gerant\RestaurantController::class, 'edit'])
         ->name('restaurant.edit');
    Route::put('/restaurant',
        [App\Http\Controllers\Gerant\RestaurantController::class, 'update'])
         ->name('restaurant.update');

    // Menus
    Route::resource('menus', App\Http\Controllers\Gerant\MenuController::class);

    // Catégories
    Route::post('/menus/{menu}/categories',
        [App\Http\Controllers\Gerant\MenuController::class, 'storeCategory'])
        ->name('menus.categories.store');
    Route::delete('/categories/{category}',
        [App\Http\Controllers\Gerant\MenuController::class, 'destroyCategory'])
        ->name('categories.destroy');

    // Plats
    Route::post('/categories/{category}/dishes',
        [App\Http\Controllers\Gerant\MenuController::class, 'storeDish'])
        ->name('categories.dishes.store');
    Route::patch('/dishes/{dish}/toggle',
        [App\Http\Controllers\Gerant\MenuController::class, 'toggleDish'])
        ->name('dishes.toggle');
    Route::delete('/dishes/{dish}',
        [App\Http\Controllers\Gerant\MenuController::class, 'destroyDish'])
        ->name('dishes.destroy');

    // Tables & QR Codes
    Route::resource('tables', App\Http\Controllers\Gerant\TableController::class);
    Route::post('/tables/{table}/regenerate',
        [App\Http\Controllers\Gerant\TableController::class, 'regenerateQr'])
        ->name('tables.regenerate');

    // Commandes
    Route::get('/orders',
        [App\Http\Controllers\Gerant\OrderController::class, 'index'])
        ->name('orders.index');
    Route::patch('/orders/{order}/status',
        [App\Http\Controllers\Gerant\OrderController::class, 'updateStatus'])
        ->name('orders.updateStatus');

    // Statistiques
    Route::get('/analytics',
        [App\Http\Controllers\Gerant\DashboardController::class, 'analytics'])
        ->name('analytics');

    // Abonnement
    Route::get('/subscription',
        [App\Http\Controllers\Gerant\SubscriptionController::class, 'index'])
        ->name('subscription');
});

// ─── Panel Super Admin ────────────────────────────────────────
Route::middleware(['auth', 'super_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');
});

// ─── Interface Client (QR Code) ───────────────────────────────
Route::prefix('menu')->name('client.')->group(function () {

    // Page menu
    Route::get('/{slug}/table/{tableNumber}',
        [App\Http\Controllers\Client\MenuController::class, 'show'])
        ->name('menu');

    // Soumettre une commande
    Route::post('/{slug}/table/{tableNumber}/order',
        [App\Http\Controllers\Client\MenuController::class, 'placeOrder'])
        ->name('order');

    // Statut commande
    Route::get('/order/{orderId}/status',
        [App\Http\Controllers\Client\MenuController::class, 'orderStatus'])
        ->name('order.status');

    // Paiement — initier
    Route::post('/payment/{order}/initiate',
        [App\Http\Controllers\Client\PaymentController::class, 'initiate'])
        ->name('payment.initiate');

    // Paiement — retour
    Route::get('/payment/{order}/return',
        [App\Http\Controllers\Client\PaymentController::class, 'return'])
        ->name('payment.return');

    // Paiement — webhook (sans CSRF)
    Route::post('/payment/notify',
        [App\Http\Controllers\Client\PaymentController::class, 'notify'])
        ->name('payment.notify')
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
});