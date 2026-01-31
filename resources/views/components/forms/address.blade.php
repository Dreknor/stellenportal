@props([
    'namePrefix' => null, // optional: z.B. 'address' -> names: address.street
    'error' => false,
    'class' => '',
    'required' => false,
    'address' => null,
])

<?php
    // Präfix für name-Attribute (mit Punkt), und ein ID-Präfix (Punkte -> Unterstriche)
    $namePrefix = $namePrefix ? rtrim($namePrefix, '.') : null;
    $pref = $namePrefix ? $namePrefix.'.' : '';
    $idPref = $namePrefix ? str_replace('.', '_', $namePrefix).'_' : '';
?>

<div class="space-y-4">
    {{-- Erste Reihe: Straße + Nr. (CSS Grid, 3fr / 1fr) --}}
    <div style="display:grid;grid-template-columns:3fr 1fr;gap:1rem;align-items:start;">
        <div style="min-width:0;">
            <label for="{{ $idPref }}street" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Straße') }}</label>
            <input
                type="text"
                name="{{ $pref }}street"
                id="{{ $idPref }}street"
                value="{{ old($pref.'street', $address?->street) }}"
                class="w-full px-4 py-1.5 rounded-lg text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $class }}"
                @if($required) required @endif
            />
            @error($pref.'street')
                <p class="mt-1 text-sm text-red-500" id="error_{{ $idPref }}street">{{ $message }}</p>
            @enderror
        </div>

        <div style="min-width:120px;">
            <label for="{{ $idPref }}number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nr.') }}</label>
            <input
                type="text"
                name="{{ $pref }}number"
                id="{{ $idPref }}number"
                value="{{ old($pref.'number', $address?->number) }}"
                class="w-full px-4 py-1.5 rounded-lg text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $class }}"
                @if($required) required @endif
            />
            @error($pref.'number')
                <p class="mt-1 text-sm text-red-500" id="error_{{ $idPref }}number">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Zweite Reihe: PLZ + Ort (CSS Grid, 1fr / 3fr) --}}
    <div style="display:grid;grid-template-columns:1fr 3fr;gap:1rem;align-items:start;margin-top:0.5rem;">
        <div style="min-width:120px;">
            <label for="{{ $idPref }}zip_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('PLZ') }}</label>
            <input
                type="text"
                name="{{ $pref }}zip_code"
                id="{{ $idPref }}zip_code"
                value="{{ old($pref.'zip_code', $address?->zip_code) }}"
                class="w-full px-4 py-1.5 rounded-lg text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $class }}"
                @if($required) required @endif
            />
            @error($pref.'zip_code')
                <p class="mt-1 text-sm text-red-500" id="error_{{ $idPref }}zip_code">{{ $message }}</p>
            @enderror
        </div>

        <div style="min-width:0;">
            <label for="{{ $idPref }}city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Ort') }}</label>
            <input
                type="text"
                name="{{ $pref }}city"
                id="{{ $idPref }}city"
                value="{{ old($pref.'city', $address?->city) }}"
                class="w-full px-4 py-1.5 rounded-lg text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $class }}"
                @if($required) required @endif
            />
            @error($pref.'city')
                <p class="mt-1 text-sm text-red-500" id="error_{{ $idPref }}city">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Dritte Reihe: Bundesland --}}
    <div style="margin-top:0.5rem;">
        <label for="{{ $idPref }}state" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Bundesland') }}</label>
        <select
            name="{{ $pref }}state"
            id="{{ $idPref }}state"
            class="w-full px-4 py-1.5 rounded-lg text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $class }}"
        >
            <option value="Sachsen" {{ old($pref.'state', $address?->state) == 'Sachsen' || old($pref.'state', $address?->state) === null ? 'selected' : '' }}>Sachsen</option>
            <option value="Baden-Württemberg" {{ old($pref.'state', $address?->state) == 'Baden-Württemberg' ? 'selected' : '' }}>Baden-Württemberg</option>
            <option value="Bayern" {{ old($pref.'state', $address?->state) == 'Bayern' ? 'selected' : '' }}>Bayern</option>
            <option value="Berlin" {{ old($pref.'state', $address?->state) == 'Berlin' ? 'selected' : '' }}>Berlin</option>
            <option value="Brandenburg" {{ old($pref.'state', $address?->state) == 'Brandenburg' ? 'selected' : '' }}>Brandenburg</option>
            <option value="Bremen" {{ old($pref.'state', $address?->state) == 'Bremen' ? 'selected' : '' }}>Bremen</option>
            <option value="Hamburg" {{ old($pref.'state', $address?->state) == 'Hamburg' ? 'selected' : '' }}>Hamburg</option>
            <option value="Hessen" {{ old($pref.'state', $address?->state) == 'Hessen' ? 'selected' : '' }}>Hessen</option>
            <option value="Mecklenburg-Vorpommern" {{ old($pref.'state', $address?->state) == 'Mecklenburg-Vorpommern' ? 'selected' : '' }}>Mecklenburg-Vorpommern</option>
            <option value="Niedersachsen" {{ old($pref.'state', $address?->state) == 'Niedersachsen' ? 'selected' : '' }}>Niedersachsen</option>
            <option value="Nordrhein-Westfalen" {{ old($pref.'state', $address?->state) == 'Nordrhein-Westfalen' ? 'selected' : '' }}>Nordrhein-Westfalen</option>
            <option value="Rheinland-Pfalz" {{ old($pref.'state', $address?->state) == 'Rheinland-Pfalz' ? 'selected' : '' }}>Rheinland-Pfalz</option>
            <option value="Saarland" {{ old($pref.'state', $address?->state) == 'Saarland' ? 'selected' : '' }}>Saarland</option>
            <option value="Sachsen-Anhalt" {{ old($pref.'state', $address?->state) == 'Sachsen-Anhalt' ? 'selected' : '' }}>Sachsen-Anhalt</option>
            <option value="Schleswig-Holstein" {{ old($pref.'state', $address?->state) == 'Schleswig-Holstein' ? 'selected' : '' }}>Schleswig-Holstein</option>
            <option value="Thüringen" {{ old($pref.'state', $address?->state) == 'Thüringen' ? 'selected' : '' }}>Thüringen</option>
        </select>
        @error($pref.'state')
            <p class="mt-1 text-sm text-red-500" id="error_{{ $idPref }}state">{{ $message }}</p>
        @enderror
    </div>
</div>
