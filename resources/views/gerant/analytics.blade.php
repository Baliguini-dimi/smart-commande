@extends('layouts.gerant')

@section('title', 'Statistiques')
@section('page_title', 'Statistiques')
@section('page_subtitle', 'Analyse de votre activité')

@section('content')

{{-- Cartes résumé --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['label' => 'Commandes ce mois', 'value' => $monthOrders ?? 0,     'icon' => 'clipboard-document-list', 'color' => 'blue'],
        ['label' => 'Ventes ce mois',    'value' => number_format($monthSales ?? 0, 0, ',', ' ') . ' F', 'icon' => 'banknotes', 'color' => 'green'],
        ['label' => 'Commandes totales', 'value' => $totalOrders ?? 0,     'icon' => 'chart-bar',              'color' => 'purple'],
        ['label' => 'Tables actives',    'value' => $activeTables ?? 0,    'icon' => 'squares-2x2',            'color' => 'amber'],
    ] as $stat)
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">
                {{ $stat['label'] }}
            </span>
            <div class="w-10 h-10 bg-{{ $stat['color'] }}-50 rounded-xl flex items-center justify-center">
                <x-dynamic-component
                    :component="'heroicon-o-' . $stat['icon']"
                    class="w-5 h-5 text-{{ $stat['color'] }}-600"/>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-gray-900"
             style="font-family:'Plus Jakarta Sans',sans-serif">
            {{ $stat['value'] }}
        </div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    {{-- Ventes 7 derniers jours --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2"
            style="font-family:'Plus Jakarta Sans',sans-serif">
            <x-heroicon-o-arrow-trending-up class="w-5 h-5 text-blue-600"/>
            Ventes — 7 derniers jours
        </h3>
        @if(($weeklyData ?? collect())->count() > 0)
        <div class="flex items-end gap-2 h-32">
            @php $maxVal = $weeklyData->max('total') ?: 1; @endphp
            @foreach($weeklyData as $day)
            <div class="flex-1 flex flex-col items-center gap-1">
                <div class="text-xs text-gray-400 font-medium">
                    {{ number_format($day->total / 1000, 0) }}k
                </div>
                <div class="w-full bg-blue-600 rounded-t-lg transition-all"
                     style="height: {{ max(4, ($day->total / $maxVal) * 100) }}px">
                </div>
                <div class="text-xs text-gray-400">
                    {{ \Carbon\Carbon::parse($day->date)->format('d/m') }}
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="flex flex-col items-center justify-center h-32 text-gray-400">
            <x-heroicon-o-chart-bar class="w-10 h-10 mb-2 text-gray-300"/>
            <p class="text-sm">Données disponibles après les premières commandes</p>
        </div>
        @endif
    </div>

    {{-- Top plats --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2"
            style="font-family:'Plus Jakarta Sans',sans-serif">
            <x-heroicon-o-trophy class="w-5 h-5 text-amber-500"/>
            Top 5 plats du mois
        </h3>
        @forelse($topDishes ?? [] as $index => $dish)
        <div class="flex items-center gap-3 mb-3">
            <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0
                {{ $index === 0 ? 'bg-amber-100 text-amber-700' :
                   ($index === 1 ? 'bg-gray-100 text-gray-600' :
                   ($index === 2 ? 'bg-orange-100 text-orange-700' : 'bg-blue-50 text-blue-600')) }}">
                {{ $index + 1 }}
            </span>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-semibold text-gray-800 truncate">
                    {{ $dish->dish_name }}
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5 mt-1">
                    @php $maxQty = $topDishes->max('total_quantity') ?: 1; @endphp
                    <div class="bg-blue-500 h-1.5 rounded-full"
                         style="width: {{ ($dish->total_quantity / $maxQty) * 100 }}%">
                    </div>
                </div>
            </div>
            <span class="text-sm font-bold text-gray-600 flex-shrink-0">
                {{ $dish->total_quantity }}x
            </span>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center h-24 text-gray-400">
            <x-heroicon-o-trophy class="w-8 h-8 mb-2 text-gray-300"/>
            <p class="text-sm">Aucune commande ce mois</p>
        </div>
        @endforelse
    </div>
</div>

{{-- Commandes par statut --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2"
        style="font-family:'Plus Jakarta Sans',sans-serif">
        <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-blue-600"/>
        Répartition des commandes ce mois
    </h3>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['key' => 'pending',   'label' => 'En attente',     'color' => 'amber'],
            ['key' => 'preparing', 'label' => 'En préparation', 'color' => 'blue'],
            ['key' => 'ready',     'label' => 'Prêt',           'color' => 'green'],
            ['key' => 'served',    'label' => 'Servi',          'color' => 'gray'],
        ] as $s)
        <div class="bg-{{ $s['color'] }}-50 rounded-xl p-4 text-center">
            <div class="text-2xl font-extrabold text-{{ $s['color'] }}-700 mb-1"
                 style="font-family:'Plus Jakarta Sans',sans-serif">
                {{ $statusCounts[$s['key']] ?? 0 }}
            </div>
            <div class="text-xs font-semibold text-{{ $s['color'] }}-600">
                {{ $s['label'] }}
            </div>
        </div>
        @endforeach
    </div>
</div>

@endsection