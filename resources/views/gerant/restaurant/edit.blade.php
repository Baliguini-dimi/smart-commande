@extends('layouts.gerant')

@section('title', 'Mon Restaurant')
@section('page_title', 'Mon Restaurant')
@section('page_subtitle', 'Configurez les informations de votre établissement')

@section('content')
@use('Illuminate\Support\Facades\Storage')

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-gray-100 bg-blue-50 flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <x-heroicon-o-building-storefront class="w-5 h-5 text-blue-600"/>
            </div>
            <div>
                <h2 class="font-bold text-gray-800"
                    style="font-family:'Plus Jakarta Sans',sans-serif">
                    {{ $restaurant ? 'Modifier mon restaurant' : 'Créer mon restaurant' }}
                </h2>
                <p class="text-xs text-gray-500">Ces informations seront visibles par vos clients</p>
            </div>
        </div>

        <form method="POST" action="{{ route('gerant.restaurant.update') }}"
              enctype="multipart/form-data" class="p-6">
            @csrf @method('PUT')

            @if($restaurant?->logo)
            <div class="mb-5 flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
                <img src="{{ Storage::url($restaurant->logo) }}"
                     alt="Logo" class="w-16 h-16 rounded-xl object-cover border border-gray-200">
                <div>
                    <div class="text-sm font-semibold text-gray-700">Logo actuel</div>
                    <div class="text-xs text-gray-400">Choisissez un nouveau fichier pour le remplacer</div>
                </div>
            </div>
            @endif

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Logo du restaurant</label>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 hover:border-blue-300 transition-colors">
                    <input type="file" name="logo" accept="image/*"
                        class="w-full text-sm text-gray-500
                               file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                               file:bg-blue-50 file:text-blue-700 file:font-semibold
                               hover:file:bg-blue-100 cursor-pointer">
                    <p class="text-xs text-gray-400 mt-2">JPG, PNG ou WEBP — Max 2 Mo</p>
                </div>
                @error('logo')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nom du restaurant <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name"
                    value="{{ old('name', $restaurant?->name) }}"
                    placeholder="Ex: Chez Kouamé, Maquis Le Wê..."
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm
                           focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Téléphone / WhatsApp</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-heroicon-o-phone class="w-4 h-4 text-gray-400"/>
                    </div>
                    <input type="text" name="phone"
                        value="{{ old('phone', $restaurant?->phone) }}"
                        placeholder="+225 07 XX XX XX XX"
                        class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm
                               focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400">
                </div>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Adresse</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-heroicon-o-map-pin class="w-4 h-4 text-gray-400"/>
                    </div>
                    <input type="text" name="address"
                        value="{{ old('address', $restaurant?->address) }}"
                        placeholder="Ex: Cocody Riviera 2, Abidjan"
                        class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm
                               focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Description courte</label>
                <textarea name="description" rows="3"
                    placeholder="Décrivez votre restaurant en quelques mots..."
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm
                           focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 resize-none">{{ old('description', $restaurant?->description) }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Max 1000 caractères</p>
            </div>

            <div class="flex gap-3">
                @if($restaurant)
                <a href="{{ route('gerant.dashboard') }}"
                    class="flex-1 py-3 border border-gray-200 text-gray-600 rounded-xl
                           text-sm font-semibold text-center hover:bg-gray-50 transition-colors">
                    Annuler
                </a>
                @endif
                <button type="submit"
                    class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-bold text-sm
                           hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                    <x-heroicon-o-check-circle class="w-4 h-4"/>
                    {{ $restaurant ? 'Enregistrer les modifications' : 'Créer mon restaurant' }}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection