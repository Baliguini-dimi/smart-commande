@extends('layouts.gerant')

@section('title', 'Nouvelle Table')
@section('page_title', 'Nouvelle Table')
@section('page_subtitle', 'Ajoutez une table et générez son QR code')

@section('content')

<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- En-tête --}}
        <div class="px-6 py-5 border-b border-gray-100 bg-blue-50 flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <x-heroicon-o-squares-2x2 class="w-5 h-5 text-blue-600"/>
            </div>
            <div>
                <h2 class="font-bold text-gray-800"
                    style="font-family:'Plus Jakarta Sans',sans-serif">
                    Nouvelle table
                </h2>
                <p class="text-xs text-gray-500">
                    Un QR code sera généré automatiquement
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('gerant.tables.store') }}" class="p-6">
            @csrf

            {{-- Numéro/Nom --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Numéro ou nom de la table
                    <span class="text-red-500">*</span>
                </label>
                <input type="text" name="number"
                    value="{{ old('number') }}"
                    placeholder="Ex: 1, 2, A, B, Terrasse 1, Bar VIP..."
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm
                           focus:outline-none focus:border-blue-400 focus:ring-1
                           focus:ring-blue-400 transition-colors">
                @error('number')
                    <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                        <x-heroicon-o-exclamation-circle class="w-3.5 h-3.5"/>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Zone --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Zone (optionnel)
                </label>
                <select name="zone"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm
                           focus:outline-none focus:border-blue-400 focus:ring-1
                           focus:ring-blue-400 bg-white transition-colors">
                    <option value="">-- Sélectionner une zone --</option>
                    <option value="Salle principale"   {{ old('zone') == 'Salle principale'   ? 'selected' : '' }}>Salle principale</option>
                    <option value="Terrasse"           {{ old('zone') == 'Terrasse'           ? 'selected' : '' }}>Terrasse</option>
                    <option value="Bar"                {{ old('zone') == 'Bar'                ? 'selected' : '' }}>Bar</option>
                    <option value="Salon VIP"          {{ old('zone') == 'Salon VIP'          ? 'selected' : '' }}>Salon VIP</option>
                    <option value="Jardin"             {{ old('zone') == 'Jardin'             ? 'selected' : '' }}>Jardin</option>
                </select>
            </div>

            {{-- Info QR --}}
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6
                        flex items-start gap-3">
                <x-heroicon-o-information-circle class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5"/>
                <div class="text-xs text-blue-700">
                    <p class="font-semibold mb-1">QR Code automatique</p>
                    <p>Un QR code unique sera généré et lié à cette table. Le client le scannera pour accéder directement à votre menu.</p>
                </div>
            </div>

            {{-- Boutons --}}
            <div class="flex gap-3">
                <a href="{{ route('gerant.tables.index') }}"
                    class="flex-1 py-3 border border-gray-200 text-gray-600 rounded-xl
                           text-sm font-semibold text-center hover:bg-gray-50 transition-colors">
                    Annuler
                </a>
                <button type="submit"
                    class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-bold
                           text-sm hover:bg-blue-700 transition-colors flex items-center
                           justify-center gap-2">
                    <x-heroicon-o-qr-code class="w-4 h-4"/>
                    Créer et générer QR
                </button>
            </div>
        </form>
    </div>
</div>

@endsection