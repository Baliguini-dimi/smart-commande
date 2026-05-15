@extends('layouts.gerant')

@section('title', 'Mon Abonnement')
@section('page_title', 'Mon Abonnement')
@section('page_subtitle', 'Gérez votre abonnement Smart Commande')

@section('content')

{{-- Statut abonnement actuel --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <x-heroicon-o-credit-card class="w-6 h-6 text-blue-600"/>
            </div>
            <div>
                <h2 class="font-bold text-gray-900"
                    style="font-family:'Plus Jakarta Sans',sans-serif">
                    {{ $currentPlan ? 'Plan ' . $currentPlan->name : 'Aucun plan actif' }}
                </h2>
                @if($restaurant?->subscription_expires_at)
                <p class="text-sm text-gray-500 mt-0.5">
                    @if($restaurant->hasActiveSubscription())
                        Expire le {{ $restaurant->subscription_expires_at->format('d/m/Y') }}
                        ({{ $restaurant->subscription_expires_at->diffForHumans() }})
                    @else
                        <span class="text-red-500 font-semibold">Abonnement expiré</span>
                    @endif
                </p>
                @endif
            </div>
        </div>
        @if($restaurant?->hasActiveSubscription())
        <span class="flex items-center gap-2 bg-green-100 text-green-700
                     px-4 py-2 rounded-xl text-sm font-bold">
            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
            Actif
        </span>
        @else
        <span class="flex items-center gap-2 bg-red-100 text-red-700
                     px-4 py-2 rounded-xl text-sm font-bold">
            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
            Expiré
        </span>
        @endif
    </div>
</div>

{{-- Plans disponibles --}}
<h3 class="font-bold text-gray-800 mb-4"
    style="font-family:'Plus Jakarta Sans',sans-serif">
    Choisir un plan
</h3>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    @foreach($plans as $plan)
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden
        {{ $currentPlan?->id === $plan->id
            ? 'border-blue-500 ring-2 ring-blue-500/20'
            : 'border-gray-100' }}">

        {{-- Badge plan actuel --}}
        @if($currentPlan?->id === $plan->id)
        <div class="bg-blue-600 text-white text-xs font-bold text-center py-1.5 tracking-wide">
            VOTRE PLAN ACTUEL
        </div>
        @endif

        <div class="p-5">
            <h4 class="font-extrabold text-gray-900 text-lg mb-1"
                style="font-family:'Plus Jakarta Sans',sans-serif">
                {{ $plan->name }}
            </h4>
            <div class="flex items-baseline gap-1 mb-4">
                <span class="text-3xl font-extrabold text-blue-600"
                      style="font-family:'Plus Jakarta Sans',sans-serif">
                    {{ number_format($plan->price_monthly, 0, ',', ' ') }}
                </span>
                <span class="text-sm text-gray-400">FCFA/mois</span>
            </div>

            {{-- Caractéristiques --}}
            <ul class="space-y-2 mb-5">
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <x-heroicon-o-check-circle class="w-4 h-4 text-green-500 flex-shrink-0"/>
                    Jusqu'à {{ $plan->max_tables }} tables
                </li>
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <x-heroicon-o-check-circle class="w-4 h-4 text-green-500 flex-shrink-0"/>
                    {{ $plan->max_menus }} menu(s) actif(s)
                </li>
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <x-heroicon-o-check-circle class="w-4 h-4 text-green-500 flex-shrink-0"/>
                    Commandes illimitées
                </li>
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <x-heroicon-o-check-circle class="w-4 h-4 text-green-500 flex-shrink-0"/>
                    QR codes automatiques
                </li>
                @if($plan->name !== 'Starter')
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <x-heroicon-o-check-circle class="w-4 h-4 text-green-500 flex-shrink-0"/>
                    Analytics & statistiques
                </li>
                @endif
                @if($plan->name === 'Premium')
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <x-heroicon-o-check-circle class="w-4 h-4 text-green-500 flex-shrink-0"/>
                    Support dédié WhatsApp
                </li>
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <x-heroicon-o-check-circle class="w-4 h-4 text-green-500 flex-shrink-0"/>
                    Export données Excel/PDF
                </li>
                @endif
            </ul>

            @if($currentPlan?->id === $plan->id)
            <button disabled
                class="w-full py-2.5 rounded-xl text-sm font-bold bg-gray-100
                       text-gray-400 cursor-not-allowed">
                Plan actuel
            </button>
            @else
            <button onclick="selectPlan({{ $plan->id }}, '{{ $plan->name }}', {{ $plan->price_monthly }})"
                class="w-full py-2.5 rounded-xl text-sm font-bold transition-colors
                       bg-blue-600 text-white hover:bg-blue-700">
                Choisir ce plan
            </button>
            @endif
        </div>
    </div>
    @endforeach
</div>

{{-- Info paiement --}}
<div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 flex items-start gap-3">
    <x-heroicon-o-information-circle class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5"/>
    <div>
        <p class="text-sm font-semibold text-blue-800 mb-1">Paiement sécurisé</p>
        <p class="text-xs text-blue-700">
            Les abonnements sont payables via Wave CI ou Orange Money.
            L'activation est immédiate après confirmation du paiement.
            Pour tout problème, contactez-nous sur WhatsApp.
        </p>
    </div>
</div>

{{-- Modal confirmation plan --}}
<div id="plan-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center px-4">
    <div class="bg-white rounded-2xl shadow-xl p-6 max-w-sm w-full">
        <h3 class="font-bold text-gray-900 mb-2"
            style="font-family:'Plus Jakarta Sans',sans-serif">
            Confirmer l'abonnement
        </h3>
        <p class="text-sm text-gray-500 mb-4" id="plan-modal-text"></p>
        <div class="flex gap-3">
            <button onclick="closePlanModal()"
                class="flex-1 py-2.5 border border-gray-200 text-gray-600 rounded-xl
                       text-sm font-semibold hover:bg-gray-50">
                Annuler
            </button>
            <button onclick="confirmPlan()"
                class="flex-1 py-2.5 bg-blue-600 text-white rounded-xl
                       text-sm font-bold hover:bg-blue-700">
                Confirmer
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let selectedPlanId = null;

    function selectPlan(planId, planName, price) {
        selectedPlanId = planId;
        const formatted = new Intl.NumberFormat('fr-FR').format(price);
        document.getElementById('plan-modal-text').textContent =
            `Vous allez souscrire au plan ${planName} pour ${formatted} FCFA/mois. Le paiement se fera via Wave CI ou Orange Money.`;
        document.getElementById('plan-modal').classList.remove('hidden');
    }

    function closePlanModal() {
        document.getElementById('plan-modal').classList.add('hidden');
        selectedPlanId = null;
    }

    function confirmPlan() {
        // Intégration CinetPay à compléter
        alert('Fonctionnalité de paiement en cours d\'intégration. Contactez le support pour activer votre abonnement.');
        closePlanModal();
    }
</script>
@endpush