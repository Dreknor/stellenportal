@if($address->geocoding_error)
    <p class="mt-1 text-xs text-red-600 dark:text-red-400">
        {{ __('Letzte Adresssuche fehlgeschlagen') }}@if($address->geocoding_failed_at) ({{ $address->geocoding_failed_at->format('d.m.Y H:i') }})@endif:
        {{ $address->geocoding_error }}
    </p>
@endif
