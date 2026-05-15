<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function getDashboardStats(Restaurant $restaurant): array
    {
        return [
            'today_orders'  => Order::where('restaurant_id', $restaurant->id)
                                    ->whereDate('created_at', today())
                                    ->count(),
            'today_sales'   => Order::where('restaurant_id', $restaurant->id)
                                    ->whereDate('created_at', today())
                                    ->where('payment_status', 'paid')
                                    ->sum('total_amount'),
            'pending_count' => Order::where('restaurant_id', $restaurant->id)
                                    ->where('status', 'pending')
                                    ->count(),
        ];
    }

    public function getTopDishes(Restaurant $restaurant, int $limit = 5)
    {
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('dishes', 'order_items.dish_id', '=', 'dishes.id')
            ->where('orders.restaurant_id', $restaurant->id)
            ->whereDate('orders.created_at', today())
            ->selectRaw('dishes.name as dish_name, SUM(order_items.quantity) as total_quantity')
            ->groupBy('dishes.id', 'dishes.name')
            ->orderByDesc('total_quantity')
            ->take($limit)
            ->get();
    }
}