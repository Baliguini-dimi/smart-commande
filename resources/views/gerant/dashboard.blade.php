@extends('layouts.gerant')

@section('title', 'Tableau de bord')
@section('page_title', 'Tableau de bord')
@section('page_subtitle', 'Vue d\'ensemble de votre activité')

@section('content')

{{-- ═══ CARTES STATISTIQUES ═══ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Commandes</span>
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-blue-600"/>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-gray-900" style="font-family:'Plus Jakarta Sans',sans-serif">
            {{ $stats['today_orders'] ?? 0 }}
        </div>
        <div class="text-xs text-gray-400 mt-1">Aujourd'hui</div>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Ventes</span>
            <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center">
                <x-heroicon-o-banknotes class="w-5 h-5 text-green-600"/>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-gray-900" style="font-family:'Plus Jakarta Sans',sans-serif">
            {{ number_format($stats['today_sales'] ?? 0, 0, ',', ' ') }} F
        </div>
        <div class="text-xs text-gray-400 mt-1">Aujourd'hui</div>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Tables</span>
            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
                <x-heroicon-o-squares-2x2 class="w-5 h-5 text-amber-600"/>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-gray-900" style="font-family:'Plus Jakarta Sans',sans-serif">
            {{ $activeTables ?? 0 }}/{{ $totalTables ?? 0 }}
        </div>
        <div class="text-xs text-gray-400 mt-1">Tables configurées</div>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">En attente</span>
            <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center">
                <x-heroicon-o-clock class="w-5 h-5 text-red-500"/>
            </div>
        </div>
        <div class="text-2xl font-extrabold {{ ($stats['pending_count'] ?? 0) > 0 ? 'text-red-500' : 'text-gray-900' }}"
            style="font-family:'Plus Jakarta Sans',sans-serif">
            {{ $stats['pending_count'] ?? 0 }}
        </div>
        <div class="text-xs text-gray-400 mt-1">À traiter maintenant</div>
    </div>
</div>

{{-- ═══ COMMANDES + SIDEBAR DROITE ═══ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Commandes en direct --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                <h2 class="font-bold text-gray-800" style="font-family:'Plus Jakarta Sans',sans-serif">
                    Commandes en direct
                </h2>
            </div>
            <a href="{{ route('gerant.orders.index') }}"
               class="text-sm text-blue-600 font-semibold hover:underline flex items-center gap-1">
                Voir tout
                <x-heroicon-o-arrow-right class="w-4 h-4"/>
            </a>
        </div>

        <div id="orders-container">
            @forelse($recentOrders ?? [] as $order)
            <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-50 rounded-xl flex flex-col items-center justify-center flex-shrink-0">
                        <span class="text-xs text-blue-400 font-medium leading-none">Table</span>
                        <span class="text-lg font-extrabold text-blue-700 leading-none"
                              style="font-family:'Plus Jakarta Sans',sans-serif">
                            {{ $order->restaurantTable->number }}
                        </span>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-800">
                            {{ $order->orderItems->count() }} article(s)
                        </div>
                        <div class="text-xs text-gray-400 mt-0.5">
                            {{ $order->orderItems->take(2)->map(fn($i) => $i->dish->name)->join(', ') }}
                            @if($order->orderItems->count() > 2) ... @endif
                        </div>
                        @if($order->client_note)
                        <div class="text-xs text-amber-600 mt-1 flex items-center gap-1">
                            <x-heroicon-o-chat-bubble-left class="w-3 h-3"/>
                            {{ $order->client_note }}
                        </div>
                        @endif
                    </div>
                </div>

                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                    <div class="font-bold text-gray-900 text-sm">
                        {{ number_format($order->total_amount, 0, ',', ' ') }} F
                    </div>
                    @php
                        $statusConfig = [
                            'pending'   => ['label' => 'En attente',     'class' => 'bg-amber-100 text-amber-700'],
                            'preparing' => ['label' => 'En préparation', 'class' => 'bg-blue-100 text-blue-700'],
                            'ready'     => ['label' => 'Prêt',           'class' => 'bg-green-100 text-green-700'],
                            'served'    => ['label' => 'Servi',          'class' => 'bg-gray-100 text-gray-500'],
                        ];
                        $s = $statusConfig[$order->status] ?? $statusConfig['pending'];
                    @endphp
                    <span class="text-xs font-bold px-3 py-1 rounded-full {{ $s['class'] }}">
                        {{ $s['label'] }}
                    </span>
                    <span class="text-xs text-gray-300">
                        {{ $order->created_at->diffForHumans() }}
                    </span>
                </div>
            </div>
            @empty
            <div class="px-6 py-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <x-heroicon-o-clipboard-document-list class="w-8 h-8 text-gray-400"/>
                </div>
                <p class="text-gray-500 text-sm font-medium">Aucune commande pour l'instant</p>
                <p class="text-gray-400 text-xs mt-1">Les commandes apparaîtront ici en temps réel</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Colonne droite --}}
    <div class="flex flex-col gap-4">

        {{-- Top plats --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
                <x-heroicon-o-trophy class="w-4 h-4 text-amber-500"/>
                <h2 class="font-bold text-gray-800 text-sm" style="font-family:'Plus Jakarta Sans',sans-serif">
                    Top plats du jour
                </h2>
            </div>
            <div class="p-5">
                @forelse($topDishes ?? [] as $index => $dish)
                <div class="flex items-center gap-3 mb-3">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 text-xs font-bold
                                 flex items-center justify-center flex-shrink-0">
                        {{ $index + 1 }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-gray-800 truncate">{{ $dish->dish_name }}</div>
                        <div class="text-xs text-gray-400">{{ $dish->total_quantity }} commandé(s)</div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4">
                    <x-heroicon-o-chart-bar class="w-8 h-8 text-gray-300 mx-auto mb-2"/>
                    <p class="text-xs text-gray-400">Données disponibles après les premières commandes</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Accès rapides --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center gap-2 mb-4">
                <x-heroicon-o-bolt class="w-4 h-4 text-blue-600"/>
                <h2 class="font-bold text-gray-800 text-sm" style="font-family:'Plus Jakarta Sans',sans-serif">
                    Accès rapides
                </h2>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('gerant.menus.index') }}"
                   class="flex flex-col items-center gap-2 p-3 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors group">
                    <x-heroicon-o-document-text class="w-6 h-6 text-blue-600 group-hover:scale-110 transition-transform"/>
                    <span class="text-xs font-semibold text-blue-700 text-center">Gérer menus</span>
                </a>
                <a href="{{ route('gerant.tables.index') }}"
                   class="flex flex-col items-center gap-2 p-3 bg-amber-50 rounded-xl hover:bg-amber-100 transition-colors group">
                    <x-heroicon-o-squares-2x2 class="w-6 h-6 text-amber-600 group-hover:scale-110 transition-transform"/>
                    <span class="text-xs font-semibold text-amber-700 text-center">Mes tables</span>
                </a>
                <a href="{{ route('gerant.orders.index') }}"
                   class="flex flex-col items-center gap-2 p-3 bg-green-50 rounded-xl hover:bg-green-100 transition-colors group">
                    <x-heroicon-o-clipboard-document-list class="w-6 h-6 text-green-600 group-hover:scale-110 transition-transform"/>
                    <span class="text-xs font-semibold text-green-700 text-center">Commandes</span>
                </a>
                <a href="{{ route('gerant.analytics') }}"
                   class="flex flex-col items-center gap-2 p-3 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors group">
                    <x-heroicon-o-arrow-trending-up class="w-6 h-6 text-purple-600 group-hover:scale-110 transition-transform"/>
                    <span class="text-xs font-semibold text-purple-700 text-center">Statistiques</span>
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Rafraîchissement automatique toutes les 10 secondes
    setInterval(function() {
        // Sera complété au module commandes
    }, 10000);
</script>
@endpush