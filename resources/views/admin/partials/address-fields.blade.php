@php($address = $model->address)
<div class="border-t border-gray-200 dark:border-gray-700 pt-6">
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Adresse') }}</h3>
    <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">{{ __('Beim Speichern einer geänderten Adresse werden die Koordinaten automatisch neu ermittelt.') }}</p>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="md:col-span-3">
            <label for="street" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Straße') }}</label>
            <input type="text" name="street" id="street" value="{{ old('street', $address?->street) }}"
                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('street')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Hausnummer') }}</label>
            <input type="text" name="number" id="number" value="{{ old('number', $address?->number) }}"
                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('number')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="zip_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('PLZ') }}</label>
            <input type="text" name="zip_code" id="zip_code" value="{{ old('zip_code', $address?->zip_code) }}"
                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('zip_code')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div class="md:col-span-2">
            <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Ort') }}</label>
            <input type="text" name="city" id="city" value="{{ old('city', $address?->city) }}"
                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('city')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="state" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Bundesland') }}</label>
            <input type="text" name="state" id="state" value="{{ old('state', $address?->state) }}" placeholder="Sachsen"
                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('state')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
    </div>

    @if($address)
        <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
            {{ __('Koordinaten:') }}
            @if($address->latitude && $address->longitude)
                <span class="font-mono">{{ $address->latitude }}, {{ $address->longitude }}</span>
            @else
                {{ __('nicht vorhanden') }}
            @endif
        </p>
        @include('admin.partials.geocoding-error', ['address' => $address])

        @if($address->latitude && $address->longitude)
            <div id="addressMap" class="mt-4 h-64 rounded-lg border border-gray-200 dark:border-gray-600"
                 role="img" aria-label="{{ __('Karte mit hinterlegtem Standort') }}"></div>

            @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        if (typeof L === 'undefined') {
                            return;
                        }

                        const position = [{{ (float) $address->latitude }}, {{ (float) $address->longitude }}];
                        const map = L.map('addressMap').setView(position, 15);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                        }).addTo(map);

                        L.marker(position).addTo(map);
                    });
                </script>
            @endpush
        @endif
    @endif
</div>
