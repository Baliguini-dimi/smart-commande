<?php

namespace App\Http\Controllers\Gerant;

use App\Http\Controllers\Controller;
use App\Models\Plan;

class SubscriptionController extends Controller
{
    public function index()
    {
        $restaurant  = auth()->user()->restaurant;
        $plans       = Plan::where('is_active', true)->get();
        $currentPlan = $restaurant?->plan;

        return view('gerant.subscription', compact('restaurant', 'plans', 'currentPlan'));
    }
}