@extends('layouts.gerant')

@section('title', 'Mes Menus')
@section('page_title', 'Mes Menus')
@section('page_subtitle', 'Gérez vos menus, catégories et plats')

@section('content')

{{-- En-tête page --}}
<div class="flex items-center justify-between mb-6">
    <div class="text-sm text-gray-500">
        {{ $menus->count() }} menu(s) configuré(s)
    </div>
    <a href="{{ route('gerant.menus.create') }}"
        class="bg-blue-600 text-white px-5 py-2.5 rounded-xl font-semibold text-sm
               hover:bg-blue-700 transition-colors flex items-center gap-2">
        <x-heroicon-o-plus class="w-4 h-4"/>
        Nouveau menu
    </a>
</div>

{{-- Liste des menus --}}
@forelse($menus as $menu)
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-6 overflow-hidden">

    {{-- En-tête du menu --}}
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50">
        <div class="flex items-center gap-3">

            {{-- Icône menu --}}
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <x-heroicon-o-document-text class="w-5 h-5 text-blue-600"/>
            </div>

            {{-- Nom et description --}}
            <div>
                <h2 class="font-bold text-gray-800" style="font-family:'Plus Jakarta Sans',sans-serif">
                    {{ $menu->name }}
                </h2>
                @if($menu->description)
                    <p class="text-xs text-gray-400">{{ $menu->description }}</p>
                @endif
            </div>

            {{-- Badge statut --}}
            <span class="flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold
                {{ $menu->is_active
                    ? 'bg-green-100 text-green-700'
                    : 'bg-gray-100 text-gray-500' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $menu->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                {{ $menu->is_active ? 'Actif' : 'Inactif' }}
            </span>
        </div>

        {{-- Boutons d'action --}}
        <div class="flex items-center gap-2">

            {{-- Bouton Gérer --}}
            <a href="{{ route('gerant.menus.show', $menu) }}"
                class="flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-600
                       rounded-lg text-xs font-semibold hover:bg-blue-100 transition-colors">
                <x-heroicon-o-pencil-square class="w-3.5 h-3.5"/>
                Gérer
            </a>

            {{-- Bouton Activer / Désactiver --}}
            <form method="POST" action="{{ route('gerant.menus.update', $menu) }}">
                @csrf @method('PUT')
                <button type="submit"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors
                    {{ $menu->is_active
                        ? 'bg-amber-50 text-amber-600 hover:bg-amber-100'
                        : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                    @if($menu->is_active)
                        <x-heroicon-o-pause-circle class="w-3.5 h-3.5"/>
                        Désactiver
                    @else
                        <x-heroicon-o-play-circle class="w-3.5 h-3.5"/>
                        Activer
                    @endif
                </button>
            </form>

            {{-- Bouton Supprimer --}}
            <form method="POST" action="{{ route('gerant.menus.destroy', $menu) }}"
                onsubmit="return confirm('Supprimer ce menu et tout son contenu ? Cette action est irréversible.')">
                @csrf @method('DELETE')
                <button type="submit"
                    class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-500
                           rounded-lg text-xs font-semibold hover:bg-red-100 transition-colors">
                    <x-heroicon-o-trash class="w-3.5 h-3.5"/>
                </button>
            </form>
        </div>
    </div>

    {{-- Aperçu des catégories --}}
    <div class="px-6 py-4">
        @if($menu->categories->count() > 0)
            <div class="flex flex-wrap gap-2 mb-3">
                @foreach($menu->categories as $cat)
                <span class="flex items-center gap-1.5 bg-blue-50 text-blue-700
                             px-3 py-1.5 rounded-lg text-xs font-semibold">
                    <x-heroicon-o-tag class="w-3 h-3"/>
                    {{ $cat->name }}
                    <span class="bg-blue-200 text-blue-800 px-1.5 py-0.5 rounded-full text-xs font-bold">
                        {{ $cat->dishes->count() }}
                    </span>
                </span>
                @endforeach
            </div>
            <div class="flex items-center gap-1.5 text-xs text-gray-400">
                <x-heroicon-o-information-circle class="w-3.5 h-3.5"/>
                Total : {{ $menu->categories->sum(fn($c) => $c->dishes->count()) }} plat(s)
            </div>
        @else
            <div class="flex items-center gap-2 text-sm text-gray-400 py-1">
                <x-heroicon-o-exclamation-circle class="w-4 h-4 text-amber-400"/>
                Aucune catégorie —
                <a href="{{ route('gerant.menus.show', $menu) }}"
                   class="text-blue-600 font-semibold hover:underline">
                    Ajouter des plats
                    <x-heroicon-o-arrow-right class="w-3.5 h-3.5 inline"/>
                </a>
            </div>
        @endif
    </div>
</div>
@empty

{{-- État vide --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
    <div class="w-20 h-20 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-5">
        <x-heroicon-o-document-text class="w-10 h-10 text-blue-400"/>
    </div>
    <h3 class="text-lg font-bold text-gray-800 mb-2"
        style="font-family:'Plus Jakarta Sans',sans-serif">
        Aucun menu créé
    </h3>
    <p class="text-gray-400 text-sm mb-6 max-w-sm mx-auto">
        Créez votre premier menu pour commencer à recevoir des commandes via QR code.
    </p>
    <a href="{{ route('gerant.menus.create') }}"
        class="inline-flex items-center gap-2 bg-blue-600 text-white
               px-6 py-3 rounded-xl font-bold text-sm hover:bg-blue-700 transition-colors">
        <x-heroicon-o-plus class="w-4 h-4"/>
        Créer mon premier menu
    </a>
</div>
@endforelse

@endsection