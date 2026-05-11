<?php

namespace App\Http\Controllers\Gerant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Récupère le restaurant du gérant connecté
        $restaurant = auth()->user()->restaurant;

        // Si le gérant n'a pas encore de restaurant → redirige vers la création
        if (!$restaurant) {
            return redirect()->route('gerant.restaurant.edit')
                   ->with('info', 'Commencez par configurer votre restaurant.');
        }

        // ── Statistiques du jour ──────────────────────────────
        $todayOrders = Order::where('restaurant_id', $restaurant->id)
            ->whereDate('created_at', today())
            ->count();

        $todaySales = Order::where('restaurant_id', $restaurant->id)
            ->whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $pendingOrdersCount = Order::where('restaurant_id', $restaurant->id)
            ->where('status', 'pending')
            ->count();

        $totalTables  = RestaurantTable::where('restaurant_id', $restaurant->id)->count();
        $activeTables = RestaurantTable::where('restaurant_id', $restaurant->id)
            ->where('is_active', true)->count();

        // ── Commandes récentes (10 dernières) ────────────────
        $recentOrders = Order::where('restaurant_id', $restaurant->id)
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->with(['orderItems.dish', 'restaurantTable'])
            ->latest()
            ->take(10)
            ->get();

        // ── Top plats du jour ────────────────────────────────
        $topDishes = \DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('dishes', 'order_items.dish_id', '=', 'dishes.id')
            ->where('orders.restaurant_id', $restaurant->id)
            ->whereDate('orders.created_at', today())
            ->selectRaw('dishes.name as dish_name, SUM(order_items.quantity) as total_quantity')
            ->groupBy('dishes.id', 'dishes.name')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        return view('gerant.dashboard', compact(
            'restaurant',
            'todayOrders',
            'todaySales',
            'pendingOrdersCount',
            'totalTables',
            'activeTables',
            'recentOrders',
            'topDishes'
        ));
    }

    public function analytics()
    {
        // On construira cette page à l'étape des statistiques
        return view('gerant.analytics');
    }
}