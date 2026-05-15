@extends('layouts.admin')

@section('title', 'Super Admin')
@section('page_title', 'Tableau de bord')
@section('page_subtitle', 'Vue globale de la plateforme Smart Commande')

@section('content')

{{-- Stats globales --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['label' => 'Restaurants total',  'value' => $totalRestaurants,  'icon' => 'building-storefront', 'color' => 'bg-blue-900/50 text-blue-400'],
        ['label' => 'Abonnés actifs',     'value' => $activeRestaurants, 'icon' => 'check-circle',        'color' => 'bg-green-900/50 text-green-400'],
        ['label' => 'Commandes ce mois',  'value' => $monthOrders,       'icon' => 'clipboard-document-list', 'color' => 'bg-purple-900/50 text-purple-400'],
        ['label' => 'Revenus ce mois',    'value' => number_format($monthRevenue, 0, ',', ' ') . ' F', 'icon' => 'banknotes', 'color' => 'bg-amber-900/50 text-amber-400'],
    ] as $stat)
    <div class="bg-blue-950/50 border border-blue-900 rounded-2xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-blue-500 uppercase tracking-wide">
                {{ $stat['label'] }}
            </span>
            <div class="w-9 h-9 {{ $stat['color'] }} rounded-xl flex items-center justify-center">
                <x-dynamic-component
                    :component="'heroicon-o-' . $stat['icon']"
                    class="w-4 h-4"/>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-white"
             style="font-family:'Plus Jakarta Sans',sans-serif">
            {{ $stat['value'] }}
        </div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Restaurants récents --}}
    <div class="lg:col-span-2 bg-blue-950/50 border border-blue-900 rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-blue-900 flex items-center justify-between">
            <h3 class="font-bold text-white text-sm"
                style="font-family:'Plus Jakarta Sans',sans-serif">
                Restaurants récents
            </h3>
            <a href="{{ route('admin.restaurants.index') }}"
               class="text-xs text-blue-400 hover:text-blue-300">Voir tout →</a>
        </div>
        @foreach($recentRestaurants as $restaurant)
        <div class="px-6 py-4 border-b border-blue-900/50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-blue-900 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-heroicon-o-building-storefront class="w-4 h-4 text-blue-400"/>
                </div>
                <div>
                    <div class="text-sm font-semibold text-white">{{ $restaurant->name }}</div>
                    <div class="text-xs text-blue-500">
                        {{ $restaurant->user?->email }} •
                        Plan {{ $restaurant->plan?->name ?? 'Aucun' }}
                    </div>
                </div>
            </div>
            <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold
                {{ $restaurant->hasActiveSubscription()
                    ? 'bg-green-900/50 text-green-400'
                    : 'bg-red-900/50 text-red-400' }}">
                <span class="w-1.5 h-1.5 rounded-full
                    {{ $restaurant->hasActiveSubscription() ? 'bg-green-400' : 'bg-red-400' }}">
                </span>
                {{ $restaurant->hasActiveSubscription() ? 'Actif' : 'Expiré' }}
            </span>
        </div>
        @endforeach
    </div>

    {{-- Répartition par plan --}}
    <div class="bg-blue-950/50 border border-blue-900 rounded-2xl p-6">
        <h3 class="font-bold text-white text-sm mb-4"
            style="font-family:'Plus Jakarta Sans',sans-serif">
            Répartition par plan
        </h3>
        @foreach($planStats as $plan)
        <div class="mb-4">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-sm text-blue-300 font-medium">{{ $plan->name }}</span>
                <span class="text-sm font-bold text-white">{{ $plan->restaurants_count }}</span>
            </div>
            @php
                $percent = $totalRestaurants > 0
                    ? ($plan->restaurants_count / $totalRestaurants) * 100 : 0;
            @endphp
            <div class="w-full bg-blue-900 rounded-full h-2">
                <div class="bg-blue-500 h-2 rounded-full transition-all"
                     style="width: {{ $percent }}%"></div>
            </div>
        </div>
        @endforeach

        <div class="mt-6 pt-4 border-t border-blue-900">
            <div class="flex justify-between text-sm">
                <span class="text-blue-400">Revenus total</span>
                <span class="font-bold text-white">
                    {{ number_format($totalRevenue, 0, ',', ' ') }} F
                </span>
            </div>
        </div>
    </div>
</div>

@endsection