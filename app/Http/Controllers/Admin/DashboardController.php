<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\Plan;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistiques globales
        $totalRestaurants  = Restaurant::count();
        $activeRestaurants = Restaurant::where('is_active', true)
            ->where('subscription_expires_at', '>', now())->count();
        $expiredRestaurants = Restaurant::where(function($q) {
            $q->whereNull('subscription_expires_at')
              ->orWhere('subscription_expires_at', '<=', now());
        })->count();

        $totalOrders    = Order::count();
        $monthOrders    = Order::whereMonth('created_at', now()->month)->count();
        $totalRevenue   = Subscription::where('status', 'active')->sum('amount_paid');
        $monthRevenue   = Subscription::whereMonth('created_at', now()->month)
                                      ->sum('amount_paid');

        // Restaurants récents
        $recentRestaurants = Restaurant::with(['user', 'plan'])
            ->latest()->take(10)->get();

        // Revenus 6 derniers mois
        $monthlyRevenue = DB::table('subscriptions')
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month,
                         SUM(amount_paid) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Répartition par plan
        $planStats = Plan::withCount('restaurants')->get();

        return view('admin.dashboard', compact(
            'totalRestaurants', 'activeRestaurants', 'expiredRestaurants',
            'totalOrders', 'monthOrders', 'totalRevenue', 'monthRevenue',
            'recentRestaurants', 'monthlyRevenue', 'planStats'
        ));
    }
}