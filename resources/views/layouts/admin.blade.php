<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Smart Commande</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        h1,h2,h3,h4,h5 { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-950 text-white antialiased">

<div class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    <aside class="w-60 bg-blue-950 flex flex-col fixed h-full z-30">

        {{-- Logo --}}
        <div class="p-5 border-b border-blue-900">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center">
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
                    <div class="font-extrabold text-sm"
                         style="font-family:'Plus Jakarta Sans',sans-serif">Smart Commande</div>
                    <div class="text-blue-400 text-xs font-semibold tracking-wide">SUPER ADMIN</div>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5">
            <p class="text-xs font-bold text-blue-600 uppercase tracking-widest px-3 mb-2">
                Administration
            </p>

            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all
               {{ request()->routeIs('admin.dashboard')
                   ? 'bg-blue-600 text-white font-semibold'
                   : 'text-blue-300 hover:bg-blue-900 hover:text-white' }}">
                <x-heroicon-o-chart-bar class="w-4 h-4 flex-shrink-0"/>
                Tableau de bord
            </a>

            <a href="{{ route('admin.restaurants.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all
               {{ request()->routeIs('admin.restaurants.*')
                   ? 'bg-blue-600 text-white font-semibold'
                   : 'text-blue-300 hover:bg-blue-900 hover:text-white' }}">
                <x-heroicon-o-building-storefront class="w-4 h-4 flex-shrink-0"/>
                Restaurants
            </a>
        </nav>

        {{-- User --}}
        <div class="p-4 border-t border-blue-900">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center
                            justify-center text-xs font-bold uppercase">
                    {{ substr(auth()->user()->name, 0, 2) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold truncate">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-blue-400">Super Admin</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-2 px-3 py-2 text-sm text-blue-400
                           hover:text-white hover:bg-blue-900 rounded-lg transition-all">
                    <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4"/>
                    Se déconnecter
                </button>
            </form>
        </div>
    </aside>

    {{-- Contenu --}}
    <div class="flex-1 flex flex-col ml-60 overflow-hidden">
        <header class="bg-blue-950/50 border-b border-blue-900 px-6 py-4
                       flex items-center justify-between sticky top-0 z-20">
            <div>
                <h1 class="text-base font-bold text-white">@yield('page_title')</h1>
                <p class="text-xs text-blue-400">@yield('page_subtitle', '')</p>
            </div>
            <span class="text-xs text-blue-500">
                {{ now()->locale('fr')->translatedFormat('l d F Y') }}
            </span>
        </header>

        <main class="flex-1 overflow-y-auto p-6">
            @if(session('success'))
            <div class="mb-4 bg-green-900/30 border border-green-700 text-green-300
                        px-4 py-3 rounded-xl flex items-center gap-3 text-sm">
                <x-heroicon-o-check-circle class="w-5 h-5 flex-shrink-0"/>
                {{ session('success') }}
            </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>