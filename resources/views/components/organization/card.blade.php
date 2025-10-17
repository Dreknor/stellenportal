@props([
    'organization',
    'editUrl' => false,
    'showActions' => false])

@php
    $headerImage = $organization->getFirstMediaUrl('header_image') ?: $organization->getFirstMediaUrl('header') ?: $organization->getFirstMediaUrl('cover') ?: $organization->getFirstMediaUrl('logo') ?: $organization->getFirstMediaUrl();

    if ($showActions == true) {
         if ($editUrl === true ){
            $url = route('organizations.edit', $organization);
        } else {
            $url = route('organizations.show', $organization);
        }
    }


    // Compute initials for placeholder (max 2 letters)
    $initials = '';
    $nameParts = preg_split('/\s+/', trim((string) $organization->name));
    if (!empty($nameParts)) {
        $firstTwo = array_slice($nameParts, 0, 2);
        foreach ($firstTwo as $part) {
            $initials .= mb_strtoupper(mb_substr($part, 0, 1));
        }
    }

    // Check if address has a map
    $hasMap = $organization->address && $organization->address->getFirstMedia('map');
    $mapUrl = $hasMap ? $organization->address->getFirstMediaUrl('map') : null;
@endphp

<article {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6']) }}>
    @if($headerImage)
        <div class="h-40 md:h-56 bg-cover bg-center" style="background-image: url('{{ $headerImage }}')">
            <div class="h-full w-full bg-gradient-to-t from-black/50 to-transparent flex items-end p-4">
                <h2 class="text-white text-lg md:text-2xl font-semibold leading-tight">{{ $organization->name }}</h2>
            </div>
        </div>
    @else
        <div class="h-40 md:h-56 flex items-center justify-center bg-gradient-to-r from-blue-200 to-blue-400 dark:from-gray-800 dark:to-gray-700 bg-blue-100 dark:bg-blue-900 p-3 ">
            <div class="text-center p-4">
                <div class="w-20 h-20 md:w-24 md:h-24 rounded-full bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-200 flex items-center justify-center text-2xl font-bold mx-auto">
                    {!! $initials ?: '&nbsp;' !!}
                </div>
                <h2 class="mt-3 text-gray-800 dark:text-gray-100 text-lg font-semibold">{{ $organization->name }}</h2>
            </div>
        </div>
    @endif

    <div class="p-6">
        <p class="text-gray-600 dark:text-gray-400 mt-1">
            {{ \Illuminate\Support\Str::limit($organization->description, 220) }}
        </p>
        <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
            <div class="{{ $hasMap ? 'grid grid-cols-1 md:grid-cols-2 gap-6' : '' }}">
                <div class="mt-4   text-gray-600 dark:text-gray-400 space-y-1">
                    @if($organization->email)
                        <p><strong>{{ __('E-Mail:') }}</strong> <a href="mailto:{{ $organization->email }}" class="text-blue-600 hover:underline">{{ $organization->email }}</a></p>
                    @endif

                    @if($organization->phone || $organization->telefon)
                        <p><strong>{{ __('Telefon:') }}</strong> {{ $organization->phone }}</p>
                    @endif

                    @if($organization->address )
                        <p><strong>{{ __('Adresse:') }}</strong></p>
                        <p>
                            {{ $organization->address->street }} {{ $organization->address->number }}<br>
                            {{ $organization->address->zip_code  }} {{ $organization->address->city }}
                        </p>
                    @endif

                    @if($organization->facilities && $organization->facilities->count() > 0)
                        <p class="mt-3"><strong>{{ __('Einrichtungen:') }}</strong> {{ $organization->facilities->count() }}</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($organization->facilities->take(3) as $facility)
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                    <x-fas-house-medical class="w-3 h-3 mr-1" />
                                    {{ $facility->name }}
                                </span>
                            @endforeach
                            @if($organization->facilities->count() > 3)
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                    +{{ $organization->facilities->count() - 3 }} {{ __('weitere') }}
                                </span>
                            @endif
                        </div>
                    @endif

                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <p class="flex items-center justify-between">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Guthaben:') }}</span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ number_format($organization->getCurrentCreditBalance(), 0, ',', '.') }}
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
                    @if($editUrl === true )
                        <x-button type="primary" tag="a" :href="$url">{{ __('Bearbeiten') }}</x-button>
                    @else
                        <x-button type="primary" tag="a" :href="$url">{{ __('Details') }}</x-button>
                    @endif
                @endif
            </div>

            <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ $organization->updated_at ? $organization->updated_at->diffForHumans() : '' }}
            </div>
        </div>
    </div>
</article>
