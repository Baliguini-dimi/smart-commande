<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Smart Commande</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        h1,h2,h3,h4,h5 { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

<div class="flex h-screen overflow-hidden">

    {{-- ═══ SIDEBAR ═══ --}}
    <aside id="sidebar"
        class="w-64 bg-blue-900 text-white flex flex-col fixed h-full z-30
               transition-transform duration-300 lg:translate-x-0 -translate-x-full">

        {{-- Logo --}}
        <div class="p-5 border-b border-blue-800/60">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center flex-shrink-0">
                    <svg width="20" height="20" viewBox="0 0 48 48" fill="none">
                        <rect x="5" y="5" width="14" height="14" rx="3" stroke="white" stroke-width="2" fill="none"/>
                        <rect x="8" y="8" width="5" height="5" rx="1" fill="white"/>
                        <rect x="5" y="29" width="14" height="14" rx="3" stroke="white" stroke-width="2" fill="none"/>
                        <rect x="8" y="32" width="5" height="5" rx="1" fill="white"/>
                        <rect x="29" y="5" width="14" height="14" rx="3" stroke="white" stroke-width="2" fill="none"/>
                        <rect x="32" y="8" width="5" height="5" rx="1" fill="white"/>
                        <path d="M27 22L22 31H26L20 42L34 27H29L33 19L27 22Z" fill="white" fill-opacity="0.9"/>
                    </svg>
                </div>
                <div>
                    <div class="font-extrabold text-sm tracking-tight leading-tight"
                         style="font-family:'Plus Jakarta Sans',sans-serif">Smart Commande</div>
                    <div class="text-blue-400 text-xs">Gérant</div>
                </div>
            </div>
        </div>

        {{-- Info restaurant --}}
        @if(auth()->user()->restaurant)
        <div class="px-3 mt-3">
            <div class="bg-blue-800/50 rounded-xl px-4 py-3">
                <div class="text-xs text-blue-400 mb-1 font-medium">Mon restaurant</div>
                <div class="font-semibold text-sm truncate text-white">
                    {{ auth()->user()->restaurant->name }}
                </div>
                @php $active = auth()->user()->restaurant->hasActiveSubscription(); @endphp
                <div class="flex items-center gap-1.5 mt-1.5">
                    <div class="w-1.5 h-1.5 rounded-full {{ $active ? 'bg-green-400' : 'bg-red-400' }}"></div>
                    <span class="text-xs {{ $active ? 'text-green-300' : 'text-red-300' }}">
                        {{ $active ? 'Abonnement actif' : 'Abonnement expiré' }}
                    </span>
                </div>
            </div>
        </div>
        @endif

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 overflow-y-auto space-y-0.5">

            <p class="text-xs font-bold text-blue-500 uppercase tracking-widest px-3 mb-2">
                Principal
            </p>

            <a href="{{ route('gerant.dashboard') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all
                {{ request()->routeIs('gerant.dashboard')
                    ? 'bg-blue-600 text-white font-semibold'
                    : 'text-blue-200 hover:bg-blue-800/60 hover:text-white' }}">
                <x-heroicon-o-chart-bar class="w-4 h-4 flex-shrink-0"/>
                Tableau de bord
            </a>

            <a href="{{ route('gerant.orders.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all
                {{ request()->routeIs('gerant.orders.*')
                    ? 'bg-blue-600 text-white font-semibold'
                    : 'text-blue-200 hover:bg-blue-800/60 hover:text-white' }}">
                <x-heroicon-o-clipboard-document-list class="w-4 h-4 flex-shrink-0"/>
                Commandes
                @if(($pendingOrdersCount ?? 0) > 0)
                <span class="ml-auto bg-red-500 text-white text-xs font-bold
                             w-5 h-5 rounded-full flex items-center justify-center">
                    {{ $pendingOrdersCount }}
                </span>
                @endif
            </a>

            <a href="{{ route('gerant.menus.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all
                {{ request()->routeIs('gerant.menus.*')
                    ? 'bg-blue-600 text-white font-semibold'
                    : 'text-blue-200 hover:bg-blue-800/60 hover:text-white' }}">
                <x-heroicon-o-document-text class="w-4 h-4 flex-shrink-0"/>
                Mes menus
            </a>

            <a href="{{ route('gerant.tables.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all
                {{ request()->routeIs('gerant.tables.*')
                    ? 'bg-blue-600 text-white font-semibold'
                    : 'text-blue-200 hover:bg-blue-800/60 hover:text-white' }}">
                <x-heroicon-o-squares-2x2 class="w-4 h-4 flex-shrink-0"/>
                Tables & QR Codes
            </a>

            <p class="text-xs font-bold text-blue-500 uppercase tracking-widest px-3 mb-2 mt-5">
                Analyse
            </p>

            <a href="{{ route('gerant.analytics') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all
                {{ request()->routeIs('gerant.analytics')
                    ? 'bg-blue-600 text-white font-semibold'
                    : 'text-blue-200 hover:bg-blue-800/60 hover:text-white' }}">
                <x-heroicon-o-arrow-trending-up class="w-4 h-4 flex-shrink-0"/>
                Statistiques
            </a>

            <p class="text-xs font-bold text-blue-500 uppercase tracking-widest px-3 mb-2 mt-5">
                Compte
            </p>

            <a href="{{ route('gerant.restaurant.edit') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all
                {{ request()->routeIs('gerant.restaurant.*')
                    ? 'bg-blue-600 text-white font-semibold'
                    : 'text-blue-200 hover:bg-blue-800/60 hover:text-white' }}">
                <x-heroicon-o-building-storefront class="w-4 h-4 flex-shrink-0"/>
                Mon restaurant
            </a>

            <a href="{{ route('gerant.subscription') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all
                {{ request()->routeIs('gerant.subscription')
                    ? 'bg-blue-600 text-white font-semibold'
                    : 'text-blue-200 hover:bg-blue-800/60 hover:text-white' }}">
                <x-heroicon-o-credit-card class="w-4 h-4 flex-shrink-0"/>
                Mon abonnement
            </a>
        </nav>

        {{-- User + Logout --}}
        <div class="p-4 border-t border-blue-800/60">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center
                            text-xs font-bold flex-shrink-0 uppercase">
                    {{ substr(auth()->user()->name, 0, 2) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold truncate">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-blue-400 truncate">{{ auth()->user()->email }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-2 px-3 py-2 text-sm text-blue-300
                           hover:text-white hover:bg-blue-800/60 rounded-lg transition-all">
                    <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4 flex-shrink-0"/>
                    Se déconnecter
                </button>
            </form>
        </div>
    </aside>

    {{-- ═══ CONTENU PRINCIPAL ═══ --}}
    <div class="flex-1 flex flex-col lg:ml-64 overflow-hidden">

        {{-- Header --}}
        <header class="bg-white border-b border-gray-100 px-6 py-4 flex items-center
                       justify-between sticky top-0 z-20 shadow-sm">
            <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg hover:bg-gray-100">
                <x-heroicon-o-bars-3 class="w-5 h-5 text-gray-600"/>
            </button>

            <div>
                <h1 class="text-base font-bold text-gray-900">@yield('page_title', 'Dashboard')</h1>
                <p class="text-xs text-gray-400">@yield('page_subtitle', '')</p>
            </div>

            <div class="flex items-center gap-3">
                <button class="relative p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <x-heroicon-o-bell class="w-5 h-5 text-gray-500"/>
                    @if(($pendingOrdersCount ?? 0) > 0)
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
                    @endif
                </button>
                <span class="text-xs text-gray-400 hidden sm:block">
                    {{ now()->locale('fr')->translatedFormat('l d F Y') }}
                </span>
            </div>
        </header>

        {{-- Contenu --}}
        <main class="flex-1 overflow-y-auto p-6">

            @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3
                        rounded-xl flex items-center gap-3 text-sm">
                <x-heroicon-o-check-circle class="w-5 h-5 text-green-500 flex-shrink-0"/>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3
                        rounded-xl flex items-center gap-3 text-sm">
                <x-heroicon-o-x-circle class="w-5 h-5 text-red-500 flex-shrink-0"/>
                {{ session('error') }}
            </div>
            @endif

            @if(session('info'))
            <div class="mb-4 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3
                        rounded-xl flex items-center gap-3 text-sm">
                <x-heroicon-o-information-circle class="w-5 h-5 text-blue-500 flex-shrink-0"/>
                {{ session('info') }}
            </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

{{-- Overlay mobile --}}
<div id="overlay" onclick="toggleSidebar()"
    class="fixed inset-0 bg-black/50 z-20 hidden lg:hidden"></div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('-translate-x-full');
        document.getElementById('overlay').classList.toggle('hidden');
    }
</script>

@stack('scripts')
</body>
</html>