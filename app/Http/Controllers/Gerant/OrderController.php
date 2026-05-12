<?php

namespace App\Http\Controllers\Gerant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $restaurant = auth()->user()->restaurant;
        $status     = request('status', 'all');

        $query = Order::where('restaurant_id', $restaurant->id)
            ->with(['orderItems.dish', 'restaurantTable'])
            ->latest();

        // Filtre par statut
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->paginate(15);

        // Compteurs par statut
        $counts = [
            'pending'   => Order::where('restaurant_id', $restaurant->id)->where('status', 'pending')->count(),
            'preparing' => Order::where('restaurant_id', $restaurant->id)->where('status', 'preparing')->count(),
            'ready'     => Order::where('restaurant_id', $restaurant->id)->where('status', 'ready')->count(),
            'served'    => Order::where('restaurant_id', $restaurant->id)->where('status', 'served')->count(),
        ];

        $pendingOrdersCount = $counts['pending'];

        return view('gerant.orders.index', compact('orders', 'counts', 'pendingOrdersCount'));
    }

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

        return response()->json([
            'success' => true,
            'status'  => $order->status,
            'message' => 'Statut mis à jour.',
        ]);
    }
}