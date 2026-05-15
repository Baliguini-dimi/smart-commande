@extends('layouts.admin')

@section('title', 'Restaurants')
@section('page_title', 'Gestion des restaurants')
@section('page_subtitle', 'Tous les restaurants inscrits sur la plateforme')

@section('content')

<div class="bg-blue-950/50 border border-blue-900 rounded-2xl overflow-hidden">

    {{-- En-tête --}}
    <div class="px-6 py-4 border-b border-blue-900">
        <div class="text-sm text-blue-400">
            {{ $restaurants->total() }} restaurant(s) au total
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-blue-900">
                    @foreach(['Restaurant', 'Gérant', 'Plan', 'Abonnement', 'Commandes', 'Actions'] as $h)
                    <th class="px-5 py-3 text-left text-xs font-bold text-blue-500
                               uppercase tracking-wide">
                        {{ $h }}
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($restaurants as $restaurant)
                <tr class="border-b border-blue-900/50 hover:bg-blue-900/20 transition-colors">
                    <td class="px-5 py-4">
                        <div class="font-semibold text-white text-sm">
                            {{ $restaurant->name }}
                        </div>
                        <div class="text-xs text-blue-500">
                            {{ $restaurant->address ?? 'Adresse non renseignée' }}
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <div class="text-sm text-blue-200">{{ $restaurant->user?->name }}</div>
                        <div class="text-xs text-blue-500">{{ $restaurant->user?->email }}</div>
                    </td>
                    <td class="px-5 py-4">
                        <span class="bg-blue-900/50 text-blue-300 px-2.5 py-1
                                     rounded-lg text-xs font-semibold">
                            {{ $restaurant->plan?->name ?? 'Aucun' }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        @if($restaurant->hasActiveSubscription())
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                            <span class="text-xs text-green-400 font-semibold">Actif</span>
                        </div>
                        <div class="text-xs text-blue-500 mt-0.5">
                            Expire le {{ $restaurant->subscription_expires_at->format('d/m/Y') }}
                        </div>
                        @else
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 bg-red-400 rounded-full"></span>
                            <span class="text-xs text-red-400 font-semibold">Expiré</span>
                        </div>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <span class="text-sm font-bold text-white">
                            {{ $restaurant->orders()->count() }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <form method="POST"
                              action="{{ route('admin.restaurants.toggle', $restaurant) }}">
                            @csrf @method('PATCH')
                            <button class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                                           text-xs font-semibold transition-colors
                                {{ $restaurant->is_active
                                    ? 'bg-red-900/50 text-red-400 hover:bg-red-900'
                                    : 'bg-green-900/50 text-green-400 hover:bg-green-900' }}">
                                @if($restaurant->is_active)
                                    <x-heroicon-o-pause-circle class="w-3.5 h-3.5"/>
                                    Suspendre
                                @else
                                    <x-heroicon-o-play-circle class="w-3.5 h-3.5"/>
                                    Activer
                                @endif
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($restaurants->hasPages())
    <div class="px-6 py-4 border-t border-blue-900">
        {{ $restaurants->links() }}
    </div>
    @endif
</div>

@endsection