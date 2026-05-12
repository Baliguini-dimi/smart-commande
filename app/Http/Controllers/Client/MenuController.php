<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\RestaurantTable;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Dish;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    // ── Affiche le menu au client ─────────────────────
    public function show(string $slug, string $tableNumber)
    {
        // Trouve le restaurant via son slug
        $restaurant = Restaurant::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Vérifie l'abonnement actif
        if (!$restaurant->hasActiveSubscription()) {
            return view('client.expired', compact('restaurant'));
        }

        // Trouve la table
        $table = RestaurantTable::where('restaurant_id', $restaurant->id)
            ->where('number', $tableNumber)
            ->where('is_active', true)
            ->firstOrFail();

        // Récupère le menu actif avec catégories et plats disponibles
        $menu = Menu::where('restaurant_id', $restaurant->id)
            ->where('is_active', true)
            ->with(['categories' => function($q) {
                $q->orderBy('sort_order')
                  ->with(['dishes' => function($q) {
                      $q->where('is_available', true)
                        ->orderBy('sort_order');
                  }]);
            }])
            ->first();

        if (!$menu) {
            return view('client.no-menu', compact('restaurant'));
        }

        return view('client.menu', compact('restaurant', 'table', 'menu'));
    }

    // ── Enregistre la commande ────────────────────────
    public function placeOrder(Request $request, string $slug, string $tableNumber)
    {
        $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.dish_id'   => 'required|integer|exists:dishes,id',
            'items.*.quantity'  => 'required|integer|min:1|max:20',
            'payment_method'     => 'required|in:wave,orange_money,cash',
            'client_note'        => 'nullable|string|max:300',
        ]);

        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $table = RestaurantTable::where('restaurant_id', $restaurant->id)
            ->where('number', $tableNumber)
            ->firstOrFail();

        // Calcule le total et vérifie les plats
        $total = 0;
        $orderItems = [];

        foreach ($request->items as $item) {
            $dish = Dish::where('id', $item['dish_id'])
                ->where('is_available', true)
                ->firstOrFail();

            $subtotal = $dish->price * $item['quantity'];
            $total   += $subtotal;

            $orderItems[] = [
                'dish_id'    => $dish->id,
                'quantity'   => $item['quantity'],
                'unit_price' => $dish->price,
                'subtotal'   => $subtotal,
                'note'       => $item['note'] ?? null,
            ];
        }

        // Crée la commande dans une transaction
        $order = DB::transaction(function () use (
            $restaurant, $table, $request, $total, $orderItems
        ) {
            $order = Order::create([
                'restaurant_id'      => $restaurant->id,
                'restaurant_table_id'=> $table->id,
                'status'             => 'pending',
                'total_amount'       => $total,
                'payment_method'     => $request->payment_method,
                'payment_status'     => 'pending',
                'client_note'        => $request->client_note,
            ]);

            foreach ($orderItems as $item) {
                $order->orderItems()->create($item);
            }

            return $order;
        });

        // Réponse JSON pour le frontend
        return response()->json([
            'success'  => true,
            'order_id' => $order->id,
            'total'    => $order->total_amount,
            'message'  => 'Commande envoyée avec succès !',
        ]);
    }

    // ── Retourne le statut d'une commande ─────────────
    public function orderStatus(int $orderId)
    {
        $order = Order::findOrFail($orderId);

        $labels = [
            'pending'   => 'En attente',
            'preparing' => 'En préparation',
            'ready'     => 'Prêt à servir',
            'served'    => 'Servi',
        ];

        return response()->json([
            'status'       => $order->status,
            'label'        => $labels[$order->status] ?? 'Inconnu',
            'total'        => $order->total_amount,
            'payment'      => $order->payment_status,
        ]);
    }
}