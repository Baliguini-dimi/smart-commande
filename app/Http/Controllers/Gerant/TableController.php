<?php

namespace App\Http\Controllers\Gerant;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class TableController extends Controller
{
    // ── Liste des tables ──────────────────────────────
    public function index()
    {
        $restaurant = auth()->user()->restaurant;

        if (!$restaurant) {
            return redirect()->route('gerant.restaurant.edit')
                   ->with('error', 'Configurez d\'abord votre restaurant.');
        }

        $tables = RestaurantTable::where('restaurant_id', $restaurant->id)
            ->orderBy('number')
            ->get();

        return view('gerant.tables.index', compact('tables', 'restaurant'));
    }

    // ── Formulaire création ───────────────────────────
    public function create()
    {
        return view('gerant.tables.create');
    }

    // ── Enregistrer une table ─────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required|string|max:50',
            'zone'   => 'nullable|string|max:100',
        ], [
            'number.required' => 'Le numéro ou nom de la table est obligatoire.',
        ]);

        $restaurant = auth()->user()->restaurant;

        // Vérifie que ce numéro n'existe pas déjà dans ce restaurant
        $exists = RestaurantTable::where('restaurant_id', $restaurant->id)
            ->where('number', $request->number)
            ->exists();

        if ($exists) {
            return back()->withErrors(['number' => 'Ce numéro de table existe déjà.']);
        }

        // Crée la table
        $table = RestaurantTable::create([
            'restaurant_id' => $restaurant->id,
            'number'        => $request->number,
            'zone'          => $request->zone,
            'is_active'     => true,
        ]);

        // Génère le QR code
        $this->generateQrCode($table, $restaurant);

        return redirect()->route('gerant.tables.index')
               ->with('success', 'Table ' . $table->number . ' créée avec son QR code.');
    }

    // ── Activer / Désactiver une table ────────────────
    public function update(Request $request, RestaurantTable $table)
    {
        $this->authorizeTable($table);
        $table->update(['is_active' => !$table->is_active]);

        $status = $table->is_active ? 'activée' : 'désactivée';
        return back()->with('success', "Table {$table->number} $status.");
    }

    // ── Supprimer une table ───────────────────────────
    public function destroy(RestaurantTable $table)
    {
        $this->authorizeTable($table);

        // Supprime le QR code du stockage
        if ($table->qr_code_path) {
            Storage::disk('public')->delete($table->qr_code_path);
        }

        $table->delete();

        return redirect()->route('gerant.tables.index')
               ->with('success', 'Table supprimée.');
    }

    // ── Regénérer le QR code ──────────────────────────
    public function regenerateQr(RestaurantTable $table)
    {
        $this->authorizeTable($table);
        $restaurant = auth()->user()->restaurant;
        $this->generateQrCode($table, $restaurant);

        return back()->with('success', 'QR code regénéré pour la table ' . $table->number);
    }

    // ── Génération du QR code ─────────────────────────
    private function generateQrCode(RestaurantTable $table, $restaurant)
    {
        // URL que le client verra en scannant
        $menuUrl = route('client.menu', [
            'slug'        => $restaurant->slug,
            'tableNumber' => $table->number,
        ]);

        // Crée le dossier si nécessaire
        $folder = 'qrcodes/' . $restaurant->id;
        Storage::disk('public')->makeDirectory($folder);

        $fileName = $folder . '/table-' . $table->id . '.svg';

        // Génère le QR code SVG
        $qrCode = QrCode::format('svg')
            ->size(300)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($menuUrl);

        Storage::disk('public')->put($fileName, $qrCode);

        // Met à jour le chemin dans la base de données
        $table->update(['qr_code_path' => $fileName]);
    }

    // ── Sécurité : la table appartient au gérant ──────
    private function authorizeTable(RestaurantTable $table)
    {
        $restaurant = auth()->user()->restaurant;
        if (!$restaurant || $table->restaurant_id !== $restaurant->id) {
            abort(403, 'Accès non autorisé.');
        }
    }
}