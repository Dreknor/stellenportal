@php($address = $model->address)
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Adresse') }}</h2>
    @if($address)
        <div class="text-sm text-gray-900 dark:text-gray-100">
            <p>{{ $address->street }} {{ $address->number }}</p>
            <p>{{ $address->zip_code }} {{ $address->city }}</p>
            @if($address->state)<p>{{ $address->state }}</p>@endif
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                {{ __('Koordinaten:') }}
                @if($address->latitude && $address->longitude)
                    <span class="font-mono">{{ $address->latitude }}, {{ $address->longitude }}</span>
                @else
                    {{ __('nicht vorhanden') }}
                @endif
            </p>
        </div>
        @can($permission)
            <form method="POST" action="{{ $geocodeRoute }}" class="mt-4">
                @csrf
                <x-button type="secondary" size="sm" native-type="submit">
                    {{ __('Koordinaten neu ermitteln') }}
                </x-button>
            </form>
        @endcan
    @else
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Keine Adresse hinterlegt.') }}</p>
    @endif
    @can($permission)
        @if(! empty($copyCoordinatesRoute))
            <form method="POST" action="{{ $copyCoordinatesRoute }}" class="mt-3"
                  onsubmit="return confirm('{{ __('Sollen die Koordinaten der Organisation für diese Einrichtung übernommen werden? Vorhandene Koordinaten werden überschrieben.') }}');">
                @csrf
                <x-button type="secondary" size="sm" native-type="submit">
                    {{ __('Koordinaten der Organisation übernehmen') }}
                </x-button>
            </form>
        @endif
        <div class="mt-3">
            <x-button type="secondary" size="sm" tag="a" :href="$editRoute">{{ __('Adresse bearbeiten') }}</x-button>
        </div>
    @endcan
</div>
