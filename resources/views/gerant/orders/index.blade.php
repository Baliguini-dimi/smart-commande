@extends('layouts.gerant')

@section('title', 'Commandes')
@section('page_title', 'Commandes')
@section('page_subtitle', 'Gestion des commandes en temps réel')

@section('content')

{{-- Filtres statut --}}
<div class="flex items-center gap-2 mb-6 overflow-x-auto pb-1">
    @php
        $statuses = [
            'all'       => ['label' => 'Toutes',          'color' => 'gray'],
            'pending'   => ['label' => 'En attente',      'color' => 'amber'],
            'preparing' => ['label' => 'En préparation',  'color' => 'blue'],
            'ready'     => ['label' => 'Prêt',            'color' => 'green'],
            'served'    => ['label' => 'Servi',           'color' => 'gray'],
        ];
        $current = request('status', 'all');
    @endphp

    @foreach($statuses as $key => $s)
    <a href="{{ route('gerant.orders.index', ['status' => $key]) }}"
        class="flex-shrink-0 px-4 py-2 rounded-xl text-xs font-bold transition-all
        {{ $current === $key
            ? 'bg-blue-600 text-white shadow-sm'
            : 'bg-white text-gray-500 border border-gray-200 hover:border-blue-300' }}">
        {{ $s['label'] }}
        @if($key !== 'all')
            <span class="ml-1 opacity-70">({{ $counts[$key] ?? 0 }})</span>
        @endif
    </a>
    @endforeach

    {{-- Indicateur live --}}
    <div class="ml-auto flex items-center gap-2 flex-shrink-0">
        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
        <span class="text-xs text-gray-400">Mise à jour auto</span>
    </div>
</div>

{{-- Liste des commandes --}}
<div id="orders-list">
    @forelse($orders as $order)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-4 overflow-hidden
                order-item" data-order-id="{{ $order->id }}">

        {{-- En-tête commande --}}
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between bg-gray-50">
            <div class="flex items-center gap-3">
                {{-- Numéro table --}}
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex flex-col items-center
                            justify-center flex-shrink-0 text-white">
                    <span class="text-xs leading-none opacity-70">Table</span>
                    <span class="text-sm font-extrabold leading-none"
                          style="font-family:'Plus Jakarta Sans',sans-serif">
                        {{ $order->restaurantTable->number }}
                    </span>
                </div>
                <div>
                    <div class="text-xs text-gray-400">
                        Commande #{{ $order->id }} •
                        {{ $order->created_at->format('H:i') }} •
                        {{ $order->created_at->diffForHumans() }}
                    </div>
                    <div class="font-bold text-gray-900 text-sm">
                        {{ $order->orderItems->count() }} article(s) —
                        {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA
                    </div>
                </div>
            </div>

            {{-- Badge statut --}}
            @php
                $badgeConfig = [
                    'pending'   => 'bg-amber-100 text-amber-700',
                    'preparing' => 'bg-blue-100 text-blue-700',
                    'ready'     => 'bg-green-100 text-green-700',
                    'served'    => 'bg-gray-100 text-gray-500',
                ];
                $statusLabels = [
                    'pending'   => 'En attente',
                    'preparing' => 'En préparation',
                    'ready'     => 'Prêt',
                    'served'    => 'Servi',
                ];
            @endphp
            <span class="status-badge px-3 py-1 rounded-full text-xs font-bold
                         {{ $badgeConfig[$order->status] ?? '' }}">
                {{ $statusLabels[$order->status] ?? $order->status }}
            </span>
        </div>

        {{-- Articles commandés --}}
        <div class="px-5 py-3">
            @foreach($order->orderItems as $item)
            <div class="flex items-center justify-between py-1.5
                        border-b border-gray-50 last:border-0">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-blue-50 rounded-lg flex items-center justify-center
                                 text-xs font-bold text-blue-600">
                        {{ $item->quantity }}
                    </span>
                    <span class="text-sm text-gray-800 font-medium">
                        {{ $item->dish->name }}
                    </span>
                    @if($item->note)
                        <span class="text-xs text-amber-600 italic">
                            — {{ $item->note }}
                        </span>
                    @endif
                </div>
                <span class="text-sm font-semibold text-gray-600">
                    {{ number_format($item->subtotal, 0, ',', ' ') }} F
                </span>
            </div>
            @endforeach

            {{-- Note client --}}
            @if($order->client_note)
            <div class="mt-3 flex items-start gap-2 bg-amber-50 rounded-xl px-3 py-2">
                <x-heroicon-o-chat-bubble-left class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5"/>
                <p class="text-xs text-amber-700">{{ $order->client_note }}</p>
            </div>
            @endif
        </div>

        {{-- Actions statut --}}
        @if($order->status !== 'served')
        <div class="px-5 py-3 border-t border-gray-100 bg-gray-50 flex items-center gap-2">
            <span class="text-xs text-gray-400 font-medium mr-1">Changer statut :</span>

            @php
                $nextStatuses = [
                    'pending'   => [['value' => 'preparing', 'label' => 'En préparation', 'class' => 'bg-blue-600 text-white hover:bg-blue-700']],
                    'preparing' => [['value' => 'ready', 'label' => 'Marquer prêt', 'class' => 'bg-green-600 text-white hover:bg-green-700']],
                    'ready'     => [['value' => 'served', 'label' => 'Marquer servi', 'class' => 'bg-gray-600 text-white hover:bg-gray-700']],
                ];
            @endphp

            @foreach($nextStatuses[$order->status] ?? [] as $next)
            <button onclick="updateOrderStatus({{ $order->id }}, '{{ $next['value'] }}')"
                class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold
                       transition-colors {{ $next['class'] }}">
                <x-heroicon-o-arrow-right class="w-3.5 h-3.5"/>
                {{ $next['label'] }}
            </button>
            @endforeach

            {{-- Paiement --}}
            <div class="ml-auto flex items-center gap-1.5">
                <x-heroicon-o-credit-card class="w-3.5 h-3.5 text-gray-400"/>
                <span class="text-xs text-gray-500 capitalize">
                    {{ str_replace('_', ' ', $order->payment_method ?? 'Non défini') }}
                </span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold
                    {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $order->payment_status === 'paid' ? 'Payé' : 'En attente' }}
                </span>
            </div>
        </div>
        @endif
    </div>
    @empty
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <x-heroicon-o-clipboard-document-list class="w-8 h-8 text-gray-400"/>
        </div>
        <p class="text-gray-500 font-medium">Aucune commande</p>
        <p class="text-gray-400 text-xs mt-1">
            Les commandes apparaîtront ici dès qu'un client scannera un QR code
        </p>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($orders->hasPages())
<div class="mt-6">
    {{ $orders->links() }}
</div>
@endif

@endsection

@push('scripts')
<script>
// ── Mettre à jour le statut d'une commande ──────────────
async function updateOrderStatus(orderId, newStatus) {
    try {
        const response = await fetch(`/dashboard/orders/${orderId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type':  'application/json',
                'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ status: newStatus }),
        });

        const data = await response.json();

        if (data.success) {
            // Recharge la page pour afficher le nouveau statut
            location.reload();
        }
    } catch (error) {
        console.error('Erreur:', error);
    }
}

// ── Rafraîchissement auto toutes les 15 secondes ────────
let autoRefresh = setInterval(() => {
    // Recharge silencieusement si on est sur l'onglet actif
    if (!document.hidden) location.reload();
}, 15000);

// Pause si l'onglet est caché
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        clearInterval(autoRefresh);
    } else {
        location.reload();
        autoRefresh = setInterval(() => {
            if (!document.hidden) location.reload();
        }, 15000);
    }
});
</script>
@endpush