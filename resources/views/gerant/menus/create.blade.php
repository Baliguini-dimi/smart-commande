@extends('layouts.gerant')

@section('title', 'Nouveau Menu')
@section('page_title', 'Nouveau Menu')
@section('page_subtitle', 'Créez un menu pour votre restaurant')

@section('content')

<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-gray-100 bg-blue-50 flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <x-heroicon-o-document-text class="w-5 h-5 text-blue-600"/>
            </div>
            <div>
                <h2 class="font-bold text-gray-800"
                    style="font-family:'Plus Jakarta Sans',sans-serif">
                    Nouveau menu
                </h2>
                <p class="text-xs text-gray-500">
                    Ajoutez ensuite des catégories et des plats
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('gerant.menus.store') }}" class="p-6">
            @csrf

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nom du menu <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}"
                    placeholder="Ex: Menu du midi, Carte des boissons, Carte complète..."
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm
                           focus:outline-none focus:border-blue-400 focus:ring-1
                           focus:ring-blue-400 transition-colors">
                @error('name')
                    <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                        <x-heroicon-o-exclamation-circle class="w-3.5 h-3.5"/>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Description (optionnel)
                </label>
                <textarea name="description" rows="2"
                    placeholder="Ex: Disponible de 11h à 15h..."
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm
                           focus:outline-none focus:border-blue-400 focus:ring-1
                           focus:ring-blue-400 resize-none transition-colors">{{ old('description') }}</textarea>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('gerant.menus.index') }}"
                    class="flex-1 py-3 border border-gray-200 text-gray-600 rounded-xl
                           text-sm font-semibold text-center hover:bg-gray-50 transition-colors">
                    Annuler
                </a>
                <button type="submit"
                    class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-bold text-sm
                           hover:bg-blue-700 transition-colors flex items-center
                           justify-center gap-2">
                    <x-heroicon-o-check-circle class="w-4 h-4"/>
                    Créer le menu
                </button>
            </div>
        </form>
    </div>
</div>

@endsection