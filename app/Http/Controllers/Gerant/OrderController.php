<?php

namespace App\Http\Controllers\Gerant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // ── Liste de toutes les commandes ─────────────────
    public function index()
    {
        $restaurant = auth()->user()->restaurant;

        $orders = Order::where('restaurant_id', $restaurant->id)
            ->with(['orderItems.dish', 'restaurantTable'])
            ->latest()
            ->paginate(20);

        return view('gerant.orders.index', compact('orders'));
    }

    // ── Mettre à jour le statut d'une commande ────────
    public function updateStatus(Request $request, Order $order)
    {
        // Vérifie que la commande appartient au restaurant du gérant
        if ($order->restaurant_id !== auth()->user()->restaurant->id) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,preparing,ready,served',
        ]);

        $order->update(['status' => $request->status]);

        // Réponse JSON pour les requêtes AJAX
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'status'  => $order->status,
                'message' => 'Statut mis à jour.',
            ]);
        }

        return back()->with('success', 'Statut de la commande mis à jour.');
    }
}