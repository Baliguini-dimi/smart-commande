<?php

namespace App\Http\Controllers\Gerant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\RestaurantTable;
use App\Services\OrderService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function index()
    {
        $restaurant = auth()->user()->restaurant;

        if (!$restaurant) {
            return redirect()->route('gerant.restaurant.edit')
                   ->with('info', 'Configurez d\'abord votre restaurant.');
        }

        $stats = $this->orderService->getDashboardStats($restaurant);

        $totalTables  = RestaurantTable::where('restaurant_id', $restaurant->id)->count();
        $activeTables = RestaurantTable::where('restaurant_id', $restaurant->id)
                                       ->where('is_active', true)->count();

        $recentOrders = Order::where('restaurant_id', $restaurant->id)
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->with(['orderItems.dish', 'restaurantTable'])
            ->latest()
            ->take(10)
            ->get();

        $topDishes          = $this->orderService->getTopDishes($restaurant);
        $pendingOrdersCount = $stats['pending_count'];

        return view('gerant.dashboard', compact(
            'restaurant', 'stats', 'totalTables', 'activeTables',
            'recentOrders', 'topDishes', 'pendingOrdersCount'
        ));
    }

    public function analytics()
    {
        $restaurant = auth()->user()->restaurant;

        if (!$restaurant) {
            return redirect()->route('gerant.restaurant.edit');
        }

        $monthOrders = Order::where('restaurant_id', $restaurant->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $monthSales = Order::where('restaurant_id', $restaurant->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $totalOrders = Order::where('restaurant_id', $restaurant->id)->count();

        $activeTables = RestaurantTable::where('restaurant_id', $restaurant->id)
            ->where('is_active', true)->count();

        $weeklyData = DB::table('orders')
            ->where('restaurant_id', $restaurant->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topDishes = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('dishes', 'order_items.dish_id', '=', 'dishes.id')
            ->where('orders.restaurant_id', $restaurant->id)
            ->whereMonth('orders.created_at', now()->month)
            ->selectRaw('dishes.name as dish_name, SUM(order_items.quantity) as total_quantity')
            ->groupBy('dishes.id', 'dishes.name')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        $statusCounts = Order::where('restaurant_id', $restaurant->id)
            ->whereMonth('created_at', now()->month)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('gerant.analytics', compact(
            'monthOrders', 'monthSales', 'totalOrders',
            'activeTables', 'weeklyData', 'topDishes', 'statusCounts'
        ));
    }
}