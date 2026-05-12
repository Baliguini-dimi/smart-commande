@extends('layouts.gerant')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Tables & QR Codes')
@section('page_title', 'Tables & QR Codes')
@section('page_subtitle', 'Gérez vos tables et générez les QR codes')

@section('content')

{{-- En-tête --}}
<div class="flex items-center justify-between mb-6">
    <div class="text-sm text-gray-500">
        {{ $tables->count() }} table(s) configurée(s)
    </div>

    <a href="{{ route('gerant.tables.create') }}"
        class="bg-blue-600 text-white px-5 py-2.5 rounded-xl font-semibold text-sm
               hover:bg-blue-700 transition-colors flex items-center gap-2">
        <x-heroicon-o-plus class="w-4 h-4"/>
        Nouvelle table
    </a>
</div>

{{-- Grille des tables --}}
@forelse($tables as $table)

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-4 overflow-hidden">
    <div class="flex items-center gap-4 p-5">

        {{-- QR Code --}}
        <div class="w-24 h-24 flex-shrink-0 bg-gray-50 rounded-xl border border-gray-200
                    flex items-center justify-center overflow-hidden">

            @if($table->qr_code_path && Storage::disk('public')->exists($table->qr_code_path))

                <img src="{{ Storage::url($table->qr_code_path) }}"
                     alt="QR Table {{ $table->number }}"
                     class="w-full h-full object-contain p-1">

            @else

                <x-heroicon-o-qr-code class="w-10 h-10 text-gray-300"/>

            @endif

        </div>

        {{-- Infos table --}}
        <div class="flex-1 min-w-0">

            <div class="flex items-center gap-2 mb-1">

                <h3 class="font-bold text-gray-900 text-base"
                    style="font-family:'Plus Jakarta Sans',sans-serif">
                    Table {{ $table->number }}
                </h3>

                <span class="flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold
                    {{ $table->is_active
                        ? 'bg-green-100 text-green-700'
                        : 'bg-gray-100 text-gray-500' }}">

                    <span class="w-1.5 h-1.5 rounded-full
                        {{ $table->is_active ? 'bg-green-500' : 'bg-gray-400' }}">
                    </span>

                    {{ $table->is_active ? 'Active' : 'Inactive' }}

                </span>

            </div>

            @if($table->zone)

            <div class="flex items-center gap-1.5 text-xs text-gray-400 mb-2">
                <x-heroicon-o-map-pin class="w-3.5 h-3.5"/>
                Zone : {{ $table->zone }}
            </div>

            @endif

            {{-- URL du menu --}}
            @if($restaurant->slug)

            <div class="flex items-center gap-1.5 text-xs text-blue-600 bg-blue-50
                        px-2.5 py-1.5 rounded-lg inline-flex max-w-full">

                <x-heroicon-o-link class="w-3 h-3 flex-shrink-0"/>

                <span class="truncate font-mono">
                    {{ url('/menu/' . $restaurant->slug . '/table/' . $table->number) }}
                </span>

            </div>

            @endif

        </div>

        {{-- Actions --}}
        <div class="flex flex-col gap-2 flex-shrink-0">

            {{-- Télécharger QR --}}
            @if($table->qr_code_path)

            <a href="{{ Storage::url($table->qr_code_path) }}"
               download="QR-Table-{{ $table->number }}.svg"
               class="flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-600
                      rounded-lg text-xs font-semibold hover:bg-blue-100 transition-colors">

                <x-heroicon-o-arrow-down-tray class="w-3.5 h-3.5"/>
                Télécharger

            </a>

            @endif

            {{-- Regénérer QR --}}
            <form method="POST"
                  action="{{ route('gerant.tables.regenerate', $table) }}">

                @csrf

                <button type="submit"
                    class="w-full flex items-center gap-1.5 px-3 py-1.5 bg-amber-50
                           text-amber-600 rounded-lg text-xs font-semibold
                           hover:bg-amber-100 transition-colors">

                    <x-heroicon-o-arrow-path class="w-3.5 h-3.5"/>
                    Regénérer

                </button>

            </form>

            {{-- Activer/Désactiver --}}
            <form method="POST"
                  action="{{ route('gerant.tables.update', $table) }}">

                @csrf
                @method('PUT')

                <button type="submit"
                    class="w-full flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                           text-xs font-semibold transition-colors
                    {{ $table->is_active
                        ? 'bg-gray-100 text-gray-500 hover:bg-gray-200'
                        : 'bg-green-50 text-green-600 hover:bg-green-100' }}">

                    @if($table->is_active)

                        <x-heroicon-o-pause-circle class="w-3.5 h-3.5"/>
                        Désactiver

                    @else

                        <x-heroicon-o-play-circle class="w-3.5 h-3.5"/>
                        Activer

                    @endif

                </button>

            </form>

            {{-- Supprimer --}}
            <form method="POST"
                  action="{{ route('gerant.tables.destroy', $table) }}"
                  onsubmit="return confirm('Supprimer la table {{ $table->number }} ?')">

                @csrf
                @method('DELETE')

                <button type="submit"
                    class="w-full flex items-center gap-1.5 px-3 py-1.5 bg-red-50
                           text-red-500 rounded-lg text-xs font-semibold
                           hover:bg-red-100 transition-colors">

                    <x-heroicon-o-trash class="w-3.5 h-3.5"/>
                    Supprimer

                </button>

            </form>

        </div>
    </div>
</div>

@empty

{{-- État vide --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">

    <div class="w-20 h-20 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-5">
        <x-heroicon-o-qr-code class="w-10 h-10 text-blue-400"/>
    </div>

    <h3 class="text-lg font-bold text-gray-800 mb-2"
        style="font-family:'Plus Jakarta Sans',sans-serif">

        Aucune table configurée

    </h3>

    <p class="text-gray-400 text-sm mb-6 max-w-sm mx-auto">
        Créez vos tables et générez les QR codes à poser sur chaque table de votre restaurant.
    </p>

    <a href="{{ route('gerant.tables.create') }}"
        class="inline-flex items-center gap-2 bg-blue-600 text-white
               px-6 py-3 rounded-xl font-bold text-sm hover:bg-blue-700 transition-colors">

        <x-heroicon-o-plus class="w-4 h-4"/>
        Créer ma première table

    </a>

</div>

@endforelse

@endsection