<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement — Smart Commande</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-6">
    <div class="text-center max-w-sm w-full">

        @if($payment?->status === 'success' || $order->payment_status === 'paid')
        {{-- Succès --}}
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center
                    justify-center mx-auto mb-5">
            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h2 class="text-2xl font-extrabold text-gray-900 mb-2"
            style="font-family:'Plus Jakarta Sans',sans-serif">
            Paiement réussi !
        </h2>
        <p class="text-gray-500 text-sm mb-4">
            Votre commande #{{ $order->id }} est confirmée et payée.
        </p>
        <div class="bg-green-50 rounded-2xl p-4 text-center">
            <p class="text-2xl font-extrabold text-green-700">
                {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA
            </p>
            <p class="text-xs text-green-600 mt-1">Montant débité</p>
        </div>

        @else
        {{-- Échec ou en attente --}}
        <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center
                    justify-center mx-auto mb-5">
            <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-900 mb-2">
            Paiement en attente
        </h2>
        <p class="text-gray-500 text-sm">
            Si vous avez déjà payé, votre paiement sera confirmé dans quelques instants.
        </p>
        @endif

        <p class="text-xs text-gray-400 mt-6">
            Commande #{{ $order->id }} — Table {{ $order->restaurantTable->number }}
        </p>
    </div>
</body>
</html>