<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::with(['user', 'plan'])
            ->latest()
            ->paginate(20);

        return view('admin.restaurants.index', compact('restaurants'));
    }

    public function toggle(Restaurant $restaurant)
    {
        $restaurant->update(['is_active' => !$restaurant->is_active]);
        $status = $restaurant->is_active ? 'activé' : 'suspendu';
        return back()->with('success', "Restaurant $status.");
    }
}