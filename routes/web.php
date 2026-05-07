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
Route::middleware(['auth', 'gerant'])->prefix('dashboard')->name('gerant.')->group(function () {

    Route::get('/', function () {
        return view('gerant.dashboard');
    })->name('dashboard');

});

// ─── Panel Super Admin ───────────────────────────────────
Route::middleware(['auth', 'super_admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

});

// ─── Interface Client (QR Code) — sans authentification ──
Route::get('/menu/{slug}/table/{tableNumber}', function ($slug, $tableNumber) {
    return view('client.menu', compact('slug', 'tableNumber'));
})->name('client.menu');