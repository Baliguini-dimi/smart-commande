@extends('layouts.gerant')
@use('Illuminate\Support\Facades\Storage')

@section('title', $menu->name)
@section('page_title', $menu->name)
@section('page_subtitle', 'Gérez les catégories et les plats')

@section('content')

<div class="mb-4">
    <a href="{{ route('gerant.menus.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-blue-600 hover:underline">
        <x-heroicon-o-arrow-left class="w-4 h-4"/>
        Retour aux menus
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ═══ CATÉGORIES + PLATS ═══ --}}
    <div class="lg:col-span-2 space-y-4">

        @forelse($menu->categories as $category)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

            {{-- En-tête catégorie --}}
            <div class="px-5 py-3 bg-blue-50 flex items-center justify-between border-b border-blue-100">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-tag class="w-4 h-4 text-blue-500"/>
                    <span class="font-bold text-gray-800 text-sm">{{ $category->name }}</span>
                    <span class="text-xs text-gray-400">({{ $category->dishes->count() }} plat(s))</span>
                </div>
                <form method="POST"
                      action="{{ route('gerant.categories.destroy', $category) }}"
                      onsubmit="return confirm('Supprimer cette catégorie et tous ses plats ?')">
                    @csrf @method('DELETE')
                    <button class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50
                                   rounded-lg transition-colors">
                        <x-heroicon-o-trash class="w-4 h-4"/>
                    </button>
                </form>
            </div>

            {{-- Plats de la catégorie --}}
            @foreach($category->dishes as $dish)
            <div class="px-5 py-4 border-b border-gray-50 flex items-center gap-4">

                {{-- Photo --}}
                <div class="w-14 h-14 rounded-xl overflow-hidden flex-shrink-0 bg-gray-100">
                    @if($dish->image)
                        <img src="{{ Storage::url($dish->image) }}"
                             alt="{{ $dish->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-blue-50">
                            <x-heroicon-o-photo class="w-6 h-6 text-blue-200"/>
                        </div>
                    @endif
                </div>

                {{-- Infos plat --}}
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-gray-800 text-sm truncate">{{ $dish->name }}</div>
                    @if($dish->description)
                        <div class="text-xs text-gray-400 truncate">{{ $dish->description }}</div>
                    @endif
                    <div class="font-bold text-blue-600 text-sm mt-0.5">
                        {{ number_format($dish->price, 0, ',', ' ') }} FCFA
                    </div>
                </div>

                {{-- Actions plat --}}
                <div class="flex items-center gap-2 flex-shrink-0">

                    {{-- Toggle disponibilité --}}
                    <form method="POST" action="{{ route('gerant.dishes.toggle', $dish) }}">
                        @csrf @method('PATCH')
                        <button class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors
                            {{ $dish->is_available
                                ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                            @if($dish->is_available)
                                <x-heroicon-o-check-circle class="w-3.5 h-3.5"/>
                                Dispo
                            @else
                                <x-heroicon-o-x-circle class="w-3.5 h-3.5"/>
                                Rupture
                            @endif
                        </button>
                    </form>

                    {{-- Supprimer plat --}}
                    <form method="POST" action="{{ route('gerant.dishes.destroy', $dish) }}"
                          onsubmit="return confirm('Supprimer ce plat ?')">
                        @csrf @method('DELETE')
                        <button class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50
                                       rounded-lg transition-colors">
                            <x-heroicon-o-trash class="w-3.5 h-3.5"/>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach

            {{-- Formulaire ajout plat --}}
            <div class="px-5 py-4 bg-gray-50">
                <details class="group">
                    <summary class="flex items-center gap-2 text-sm text-blue-600 font-semibold
                                    cursor-pointer hover:text-blue-700 list-none">
                        <x-heroicon-o-plus-circle class="w-4 h-4"/>
                        Ajouter un plat
                    </summary>
                    <form method="POST"
                          action="{{ route('gerant.categories.dishes.store', $category) }}"
                          enctype="multipart/form-data"
                          class="mt-4 space-y-3">
                        @csrf
                        <div class="grid grid-cols-2 gap-3">
                            <input type="text" name="name"
                                placeholder="Nom du plat *"
                                class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm
                                       focus:outline-none focus:border-blue-400">
                            <input type="number" name="price"
                                placeholder="Prix en FCFA *" min="0"
                                class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm
                                       focus:outline-none focus:border-blue-400">
                        </div>
                        <input type="text" name="description"
                            placeholder="Description (optionnel)"
                            class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm
                                   focus:outline-none focus:border-blue-400">
                        <div class="flex items-center gap-3">
                            <input type="file" name="image" accept="image/*"
                                class="text-xs text-gray-500 flex-1
                                       file:mr-3 file:py-1.5 file:px-3 file:rounded-lg
                                       file:border-0 file:bg-blue-50 file:text-blue-700
                                       file:text-xs file:font-semibold">
                            <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded-xl text-sm
                                       font-bold hover:bg-blue-700 flex-shrink-0 flex items-center gap-1.5">
                                <x-heroicon-o-check class="w-4 h-4"/>
                                Ajouter
                            </button>
                        </div>
                    </form>
                </details>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
            <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center
                        justify-center mx-auto mb-3">
                <x-heroicon-o-squares-plus class="w-7 h-7 text-blue-400"/>
            </div>
            <p class="text-gray-500 text-sm font-medium">Aucune catégorie</p>
            <p class="text-gray-400 text-xs mt-1">
                Ajoutez une catégorie depuis le panneau à droite
            </p>
        </div>
        @endforelse
    </div>

    {{-- ═══ SIDEBAR : Ajouter catégorie ═══ --}}
    <div class="space-y-4">

        {{-- Formulaire catégorie --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-bold text-gray-800 mb-4 text-sm flex items-center gap-2"
                style="font-family:'Plus Jakarta Sans',sans-serif">
                <x-heroicon-o-plus-circle class="w-4 h-4 text-blue-600"/>
                Nouvelle catégorie
            </h3>
            <form method="POST"
                  action="{{ route('gerant.menus.categories.store', $menu) }}">
                @csrf
                <div class="mb-3">
                    <input type="text" name="name"
                        placeholder="Ex: Grillades, Boissons..."
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm
                               focus:outline-none focus:border-blue-400 transition-colors">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-2.5 rounded-xl font-bold
                           text-sm hover:bg-blue-700 transition-colors flex items-center
                           justify-center gap-2">
                    <x-heroicon-o-plus class="w-4 h-4"/>
                    Ajouter la catégorie
                </button>
            </form>
        </div>

        {{-- Conseils --}}
        <div class="bg-blue-50 rounded-2xl border border-blue-100 p-5">
            <h4 class="font-bold text-blue-800 text-sm mb-3 flex items-center gap-2">
                <x-heroicon-o-light-bulb class="w-4 h-4"/>
                Conseils
            </h4>
            <ul class="text-xs text-blue-700 space-y-2">
                <li class="flex items-start gap-1.5">
                    <x-heroicon-o-check-circle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5"/>
                    Organisez vos plats en catégories claires
                </li>
                <li class="flex items-start gap-1.5">
                    <x-heroicon-o-check-circle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5"/>
                    Ajoutez des photos pour augmenter les commandes
                </li>
                <li class="flex items-start gap-1.5">
                    <x-heroicon-o-check-circle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5"/>
                    Marquez les plats en rupture plutôt que de les supprimer
                </li>
                <li class="flex items-start gap-1.5">
                    <x-heroicon-o-check-circle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5"/>
                    Les prix sont en FCFA, sans centimes
                </li>
            </ul>
        </div>

        {{-- Statut du menu --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h4 class="font-bold text-gray-800 text-sm mb-3"
                style="font-family:'Plus Jakarta Sans',sans-serif">
                Statut du menu
            </h4>
            <div class="flex items-center justify-between">
                <span class="flex items-center gap-2 text-sm
                    {{ $menu->is_active ? 'text-green-700' : 'text-gray-500' }}">
                    <span class="w-2 h-2 rounded-full
                        {{ $menu->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                    {{ $menu->is_active ? 'Menu actif' : 'Menu inactif' }}
                </span>
                <form method="POST" action="{{ route('gerant.menus.update', $menu) }}">
                    @csrf @method('PUT')
                    <button class="text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors
                        {{ $menu->is_active
                            ? 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                            : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                        {{ $menu->is_active ? 'Désactiver' : 'Activer' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection