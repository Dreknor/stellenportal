@props([
    'facility',
    'editUrl' => false,
    'showActions' => false])

@php
    $headerImage = $facility->getFirstMediaUrl('header_image') ?: $facility->getFirstMediaUrl('header') ?: $facility->getFirstMediaUrl('cover') ?: $facility->getFirstMediaUrl('logo') ?: $facility->getFirstMediaUrl();

    if ($showActions == true) {
         if ($editUrl === true ){
            $url = route('facilities.edit', $facility);
        } else {
            $url = route('facilities.show', $facility);
        }
    }

    // Compute initials for placeholder (max 2 letters)
    $initials = '';
    $nameParts = preg_split('/\s+/', trim((string) $facility->name));
    if (!empty($nameParts)) {
        $firstTwo = array_slice($nameParts, 0, 2);
        foreach ($firstTwo as $part) {
            $initials .= mb_strtoupper(mb_substr($part, 0, 1));
        }
    }

    // Check if address has a map
    $hasMap = $facility->address && $facility->address->getFirstMedia('map');
    $mapUrl = $hasMap ? $facility->address->getFirstMediaUrl('map') : null;
@endphp

<article {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6']) }}>
    @if($headerImage)
        <div class="h-40 md:h-56 bg-cover bg-center" style="background-image: url('{{ $headerImage }}')">
            <div class="h-full w-full bg-gradient-to-t from-black/50 to-transparent flex items-end p-4">
                <h2 class="text-white text-lg md:text-2xl font-semibold leading-tight">{{ $facility->name }}</h2>
            </div>
        </div>
    @else
        <div class="h-40 md:h-56 flex items-center justify-center bg-gradient-to-r from-green-200 to-green-400 dark:from-gray-800 dark:to-gray-700 bg-green-100 dark:bg-green-900 p-3 ">
            <div class="text-center p-4">
                <div class="w-20 h-20 md:w-24 md:h-24 rounded-full bg-green-50 dark:bg-green-900 text-green-700 dark:text-green-200 flex items-center justify-center text-2xl font-bold mx-auto">
                    {!! $initials ?: '&nbsp;' !!}
                </div>
                <h2 class="mt-3 text-gray-800 dark:text-gray-100 text-lg font-semibold">{{ $facility->name }}</h2>
            </div>
        </div>
    @endif

    <div class="p-6">
        <div class="mb-3">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                <x-fas-building class="w-4 h-4 mr-1" />
                {{ $facility->organization->name }}
            </span>
        </div>

        <p class="text-gray-600 dark:text-gray-400 mt-1">
            {{ \Illuminate\Support\Str::limit($facility->description, 220) }}
        </p>

        <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
            <div class="{{ $hasMap ? 'grid grid-cols-1 md:grid-cols-2 gap-6' : '' }}">
                <div class="mt-4 text-gray-600 dark:text-gray-400 space-y-1">
                    @if($facility->email)
                        <p><strong>{{ __('E-Mail:') }}</strong> <a href="mailto:{{ $facility->email }}" class="text-blue-600 hover:underline">{{ $facility->email }}</a></p>
                    @endif

                    @if($facility->phone)
                        <p><strong>{{ __('Telefon:') }}</strong> {{ $facility->phone }}</p>
                    @endif

                    @if($facility->address)
                        <p><strong>{{ __('Adresse:') }}</strong></p>
                        <p>
                            {{ $facility->address->street }} {{ $facility->address->number }}<br>
                            {{ $facility->address->zip_code }} {{ $facility->address->city }}
                        </p>
                    @endif

                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <p class="flex items-center justify-between">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Guthaben:') }}</span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ number_format($facility->getCurrentCreditBalance(), 0, ',', '.') }}
                            </span>
                        </p>
                    </div>
                </div>

                @if($hasMap)
                    <div class="mt-4 md:mt-0">
                        <img src="{{ $mapUrl }}" alt="{{ __('Karte') }}" class="w-full h-auto rounded-lg border border-gray-200 dark:border-gray-600 max-w-md shadow-md">
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-6 flex items-center justify-between">
            <div>
                @if($showActions)
                    @if($editUrl === true)
                        <div class="flex items-center space-x-3">
                            <x-button type="primary" tag="a" :href="$url">{{ __('Bearbeiten') }}</x-button>
                            <x-button type="success" tag="a" :href="route('credits.facility.purchase', $facility)" size="sm" class="justify-center">
                                <x-fas-plus class="w-4 h-4 mr-2" />
                                {{ __('Guthaben aufladen') }}
                            </x-button>
                        </div>
                     @else
                         <x-button type="primary" tag="a" :href="$url">{{ __('Details') }}</x-button>
                     @endif
                 @endif
            </div>

            <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ $facility->updated_at ? $facility->updated_at->diffForHumans() : '' }}
            </div>
        </div>
    </div>
</article>
