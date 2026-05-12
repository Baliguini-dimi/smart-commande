<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $restaurant->name }} — Menu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        h1,h2,h3 { font-family: 'Plus Jakarta Sans', sans-serif; }
        .category-btn.active { background: #1B4FE4; color: white; }
        .dish-card { transition: transform 0.15s; }
        .dish-card:active { transform: scale(0.98); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen pb-32">

    {{-- ═══ HEADER RESTAURANT ═══ --}}
    <div class="bg-blue-800 text-white sticky top-0 z-20 shadow-lg">
        <div class="px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if($restaurant->logo)
                    <img src="{{ Storage::url($restaurant->logo) }}"
                         alt="{{ $restaurant->name }}"
                         class="w-9 h-9 rounded-lg object-cover border border-blue-700">
                @else
                    <div class="w-9 h-9 rounded-lg bg-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>
                        </svg>
                    </div>
                @endif
                <div>
                    <div class="font-bold text-sm leading-tight">{{ $restaurant->name }}</div>
                    <div class="text-blue-300 text-xs">Table {{ $table->number }}</div>
                </div>
            </div>
            {{-- Bouton panier --}}
            <button onclick="toggleCart()"
                class="relative bg-white/10 hover:bg-white/20 rounded-xl px-3 py-2
                       flex items-center gap-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="text-sm font-bold" id="cart-count">0</span>
                <span id="cart-total-header" class="text-xs text-blue-200 hidden sm:block">0 F</span>
            </button>
        </div>

        {{-- Catégories --}}
        <div class="flex gap-2 px-4 pb-3 overflow-x-auto scrollbar-hide">
            @foreach($menu->categories as $category)
            <button onclick="scrollToCategory('cat-{{ $category->id }}')"
                class="category-btn flex-shrink-0 px-4 py-1.5 rounded-full text-xs font-semibold
                       bg-blue-700/50 text-blue-100 hover:bg-blue-600 transition-colors">
                {{ $category->name }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- ═══ CONTENU MENU ═══ --}}
    <div class="max-w-2xl mx-auto px-4 pt-4">

        @foreach($menu->categories as $category)
        @if($category->dishes->count() > 0)
        <div id="cat-{{ $category->id }}" class="mb-6">

            {{-- Titre catégorie --}}
            <div class="flex items-center gap-2 mb-3">
                <div class="h-px bg-gray-200 flex-1"></div>
                <span class="text-xs font-bold text-gray-500 uppercase tracking-widest px-3">
                    {{ $category->name }}
                </span>
                <div class="h-px bg-gray-200 flex-1"></div>
            </div>

            {{-- Plats --}}
            @foreach($category->dishes as $dish)
            <div class="dish-card bg-white rounded-2xl border border-gray-100 shadow-sm
                        mb-3 overflow-hidden flex">

                {{-- Photo --}}
                <div class="w-24 h-24 flex-shrink-0 bg-gray-100">
                    @if($dish->image)
                        <img src="{{ Storage::url($dish->image) }}"
                             alt="{{ $dish->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-blue-50">
                            <svg class="w-8 h-8 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Infos --}}
                <div class="flex-1 p-3 flex flex-col justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm leading-tight mb-1">
                            {{ $dish->name }}
                        </h3>
                        @if($dish->description)
                        <p class="text-xs text-gray-400 line-clamp-2 leading-relaxed">
                            {{ $dish->description }}
                        </p>
                        @endif
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <span class="font-extrabold text-blue-700 text-base">
                            {{ number_format($dish->price, 0, ',', ' ') }} F
                        </span>

                        {{-- Contrôle quantité --}}
                        <div class="flex items-center gap-2" id="control-{{ $dish->id }}">
                            <div id="add-btn-{{ $dish->id }}">
                                <button onclick="addToCart({{ $dish->id }}, '{{ addslashes($dish->name) }}', {{ $dish->price }})"
                                    class="bg-blue-600 text-white w-8 h-8 rounded-lg flex items-center
                                           justify-center hover:bg-blue-700 transition-colors font-bold text-lg">
                                    +
                                </button>
                            </div>
                            <div id="qty-control-{{ $dish->id }}" class="hidden flex items-center gap-2">
                                <button onclick="decreaseQty({{ $dish->id }})"
                                    class="w-8 h-8 rounded-lg border border-gray-200 flex items-center
                                           justify-center text-gray-600 hover:bg-gray-100 font-bold">
                                    −
                                </button>
                                <span id="qty-{{ $dish->id }}"
                                      class="text-sm font-bold text-gray-900 w-5 text-center">0</span>
                                <button onclick="addToCart({{ $dish->id }}, '{{ addslashes($dish->name) }}', {{ $dish->price }})"
                                    class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center
                                           justify-center hover:bg-blue-700 font-bold">
                                    +
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
        @endforeach

        {{-- Appel serveur --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-4 text-center">
            <p class="text-xs text-gray-400 mb-2">Besoin d'aide ?</p>
            <button onclick="callWaiter()"
                class="inline-flex items-center gap-2 bg-amber-50 text-amber-700 border
                       border-amber-200 px-4 py-2 rounded-xl text-sm font-semibold
                       hover:bg-amber-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                Appeler le serveur
            </button>
        </div>
    </div>

    {{-- ═══ PANIER (SLIDE UP) ═══ --}}
    <div id="cart-overlay" onclick="toggleCart()"
         class="fixed inset-0 bg-black/50 z-30 hidden"></div>

    <div id="cart-panel"
         class="fixed bottom-0 left-0 right-0 bg-white rounded-t-2xl z-40
                transform translate-y-full transition-transform duration-300 shadow-2xl
                max-h-[85vh] flex flex-col">

        {{-- Handle --}}
        <div class="flex justify-center pt-3 pb-1">
            <div class="w-10 h-1 bg-gray-300 rounded-full"></div>
        </div>

        {{-- Header panier --}}
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900" style="font-family:'Plus Jakarta Sans',sans-serif">
                Mon panier
            </h3>
            <button onclick="toggleCart()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Articles --}}
        <div id="cart-items" class="flex-1 overflow-y-auto px-5 py-3"></div>

        {{-- Note client --}}
        <div class="px-5 pb-2">
            <textarea id="client-note" rows="2" placeholder="Note pour le restaurant (optionnel)..."
                class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm resize-none
                       focus:outline-none focus:border-blue-400"></textarea>
        </div>

        {{-- Mode de paiement --}}
        <div class="px-5 pb-3">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                Mode de paiement
            </p>
            <div class="grid grid-cols-3 gap-2">
                @foreach(['wave' => 'Wave CI', 'orange_money' => 'Orange Money', 'cash' => 'Cash'] as $val => $label)
                <label class="cursor-pointer">
                    <input type="radio" name="payment_method" value="{{ $val }}"
                           class="hidden peer" {{ $val === 'wave' ? 'checked' : '' }}>
                    <div class="text-center py-2 px-1 rounded-xl border border-gray-200 text-xs
                                font-semibold text-gray-600 peer-checked:border-blue-500
                                peer-checked:bg-blue-50 peer-checked:text-blue-700 transition-all">
                        {{ $label }}
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Total + Bouton commander --}}
        <div class="px-5 pb-6 border-t border-gray-100 pt-3">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-semibold text-gray-600">Total</span>
                <span id="cart-total" class="text-xl font-extrabold text-blue-700"
                      style="font-family:'Plus Jakarta Sans',sans-serif">0 F</span>
            </div>
            <button onclick="submitOrder()"
                class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold text-base
                       hover:bg-blue-700 transition-colors disabled:opacity-50"
                id="order-btn">
                Passer la commande
            </button>
        </div>
    </div>

    {{-- ═══ CONFIRMATION COMMANDE ═══ --}}
    <div id="order-success" class="fixed inset-0 bg-white z-50 flex flex-col items-center
                                    justify-center text-center px-6 hidden">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-5">
            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h2 class="text-2xl font-extrabold text-gray-900 mb-2"
            style="font-family:'Plus Jakarta Sans',sans-serif">
            Commande envoyée !
        </h2>
        <p class="text-gray-500 text-sm mb-2">Votre commande a bien été reçue.</p>
        <p class="text-gray-400 text-xs mb-6">Le restaurant prépare votre commande.</p>

        {{-- Statut commande --}}
        <div class="bg-gray-50 rounded-2xl p-4 w-full max-w-xs mb-6">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Statut</p>
            <div id="order-status-label"
                 class="text-lg font-bold text-amber-600">
                En attente
            </div>
        </div>

        <p class="text-xs text-gray-400">Cette page se met à jour automatiquement</p>
    </div>

    {{-- ═══ JAVASCRIPT ═══ --}}
    <script>
        // ── État du panier ──────────────────────────────
        let cart     = {};
        let orderId  = null;

        // ── Ajouter au panier ───────────────────────────
        function addToCart(dishId, dishName, price) {
            if (!cart[dishId]) {
                cart[dishId] = { name: dishName, price: price, qty: 0 };
            }
            cart[dishId].qty++;
            updateCartUI(dishId);
            updateCartSummary();
        }

        // ── Diminuer quantité ───────────────────────────
        function decreaseQty(dishId) {
            if (!cart[dishId]) return;
            cart[dishId].qty--;
            if (cart[dishId].qty <= 0) {
                delete cart[dishId];
                document.getElementById('qty-control-' + dishId).classList.add('hidden');
                document.getElementById('add-btn-' + dishId).classList.remove('hidden');
            }
            updateCartUI(dishId);
            updateCartSummary();
        }

        // ── Mettre à jour UI d'un plat ──────────────────
        function updateCartUI(dishId) {
            const qty = cart[dishId]?.qty || 0;
            const qtyEl = document.getElementById('qty-' + dishId);
            const qtyControl = document.getElementById('qty-control-' + dishId);
            const addBtn = document.getElementById('add-btn-' + dishId);

            if (qtyEl) qtyEl.textContent = qty;

            if (qty > 0) {
                qtyControl?.classList.remove('hidden');
                addBtn?.classList.add('hidden');
            }
        }

        // ── Mettre à jour le résumé panier ─────────────
        function updateCartSummary() {
            const count = Object.values(cart).reduce((s, i) => s + i.qty, 0);
            const total = Object.values(cart).reduce((s, i) => s + (i.price * i.qty), 0);

            document.getElementById('cart-count').textContent = count;
            document.getElementById('cart-total').textContent =
                new Intl.NumberFormat('fr-FR').format(total) + ' F';
            document.getElementById('cart-total-header').textContent =
                new Intl.NumberFormat('fr-FR').format(total) + ' F';

            if (count > 0) {
                document.getElementById('cart-total-header').classList.remove('hidden');
            }

            // Mettre à jour les articles dans le panel
            const itemsEl = document.getElementById('cart-items');
            if (Object.keys(cart).length === 0) {
                itemsEl.innerHTML = `
                    <div class="text-center py-8 text-gray-400">
                        <p class="text-sm">Votre panier est vide</p>
                    </div>`;
                return;
            }

            itemsEl.innerHTML = Object.entries(cart).map(([id, item]) => `
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-800">${item.name}</p>
                        <p class="text-xs text-gray-400">
                            ${new Intl.NumberFormat('fr-FR').format(item.price)} F × ${item.qty}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="decreaseQty(${id})"
                            class="w-7 h-7 rounded-lg border border-gray-200 text-gray-600
                                   hover:bg-gray-100 flex items-center justify-center font-bold">
                            −
                        </button>
                        <span class="text-sm font-bold w-5 text-center">${item.qty}</span>
                        <button onclick="addToCart(${id}, '${item.name}', ${item.price})"
                            class="w-7 h-7 rounded-lg bg-blue-600 text-white
                                   hover:bg-blue-700 flex items-center justify-center font-bold">
                            +
                        </button>
                    </div>
                    <div class="text-sm font-bold text-blue-700 ml-3 w-20 text-right">
                        ${new Intl.NumberFormat('fr-FR').format(item.price * item.qty)} F
                    </div>
                </div>
            `).join('');
        }

        // ── Toggle panier ───────────────────────────────
        function toggleCart() {
            const panel   = document.getElementById('cart-panel');
            const overlay = document.getElementById('cart-overlay');
            panel.classList.toggle('translate-y-full');
            overlay.classList.toggle('hidden');
            updateCartSummary();
        }

        // ── Défiler vers une catégorie ──────────────────
        function scrollToCategory(catId) {
            const el = document.getElementById(catId);
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // ── Appeler le serveur ──────────────────────────
        function callWaiter() {
            alert('Le serveur a été appelé ! Il arrive bientôt.');
        }

        // ── Soumettre la commande ───────────────────────
        async function submitOrder() {
            const items = Object.entries(cart).map(([dishId, item]) => ({
                dish_id:  parseInt(dishId),
                quantity: item.qty,
                note:     '',
            }));

            if (items.length === 0) {
                alert('Votre panier est vide.');
                return;
            }

            const paymentMethod = document.querySelector(
                'input[name="payment_method"]:checked'
            )?.value || 'cash';

            const note = document.getElementById('client-note').value;

            const btn = document.getElementById('order-btn');
            btn.disabled    = true;
            btn.textContent = 'Envoi en cours...';

            try {
                const response = await fetch(
                    '{{ route("client.order", ["slug" => $restaurant->slug, "tableNumber" => $table->number]) }}',
                    {
                        method:  'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                        },
                        body: JSON.stringify({
                            items:          items,
                            payment_method: paymentMethod,
                            client_note:    note,
                        }),
                    }
                );

                const data = await response.json();

                if (data.success) {
                    orderId = data.order_id;
                    document.getElementById('order-success').classList.remove('hidden');
                    document.getElementById('cart-panel').classList.add('translate-y-full');
                    document.getElementById('cart-overlay').classList.add('hidden');
                    startStatusPolling();
                } else {
                    alert('Une erreur est survenue. Réessayez.');
                    btn.disabled    = false;
                    btn.textContent = 'Passer la commande';
                }
            } catch (error) {
                alert('Erreur de connexion. Vérifiez votre connexion internet.');
                btn.disabled    = false;
                btn.textContent = 'Passer la commande';
            }
        }

        // ── Polling statut commande ─────────────────────
        function startStatusPolling() {
            const labels = {
                pending:   'En attente',
                preparing: 'En préparation',
                ready:     'Prêt à servir',
                served:    'Servi',
            };
            const colors = {
                pending:   'text-amber-600',
                preparing: 'text-blue-600',
                ready:     'text-green-600',
                served:    'text-gray-500',
            };

            const interval = setInterval(async () => {
                if (!orderId) return;

                try {
                    const res  = await fetch(`/menu/order/${orderId}/status`);
                    const data = await res.json();

                    const el = document.getElementById('order-status-label');
                    el.textContent = labels[data.status] || data.status;
                    el.className   = 'text-lg font-bold ' + (colors[data.status] || 'text-gray-700');

                    if (data.status === 'served') clearInterval(interval);
                } catch (e) {
                    console.error('Erreur statut:', e);
                }
            }, 8000); // Vérifie toutes les 8 secondes
        }

        // Init panier vide
        updateCartSummary();
    </script>
</body>
</html>