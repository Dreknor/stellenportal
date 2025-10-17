@props(['jobPosting', 'showActions' => false])

@php
    // Get header image from facility
    $headerImage = $jobPosting->facility->getFirstMediaUrl('header_image') ?: $jobPosting->facility->getFirstMediaUrl('header') ?: $jobPosting->facility->getFirstMediaUrl('cover') ?: $jobPosting->facility->getFirstMediaUrl('logo');

    // Compute initials for placeholder (max 2 letters)
    $initials = '';
    $nameParts = preg_split('/\s+/', trim((string) $jobPosting->facility->name));
    if (!empty($nameParts)) {
        $firstTwo = array_slice($nameParts, 0, 2);
        foreach ($firstTwo as $part) {
            $initials .= mb_strtoupper(mb_substr($part, 0, 1));
        }
    }

    // Check if address has a map
    $hasMap = $jobPosting->facility->address && $jobPosting->facility->address->getFirstMedia('map');
    $mapUrl = $hasMap ? $jobPosting->facility->address->getFirstMediaUrl('map') : null;
@endphp

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">
    @if($headerImage)
        <div class="h-32 md:h-40 bg-cover bg-center relative" style="background-image: url('{{ $headerImage }}')">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 p-4">
                <div class="flex items-center text-white">
                    <x-fas-building class="w-4 h-4 mr-2 opacity-90" />
                    <span class="text-sm font-medium">{{ $jobPosting->facility->name }}</span>
                </div>
            </div>
        </div>
    @else
        <div class="h-32 md:h-40 bg-gradient-to-r from-green-200 to-green-400 dark:from-gray-800 dark:to-gray-700 relative">
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <div class="w-16 h-16 rounded-full bg-green-50 dark:bg-green-900 text-green-700 dark:text-green-200 flex items-center justify-center text-xl font-bold mx-auto">
                        {!! $initials ?: '&nbsp;' !!}
                    </div>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 p-4">
                <div class="flex items-center text-gray-800 dark:text-gray-100">
                    <x-fas-building class="w-4 h-4 mr-2" />
                    <span class="text-sm font-medium">{{ $jobPosting->facility->name }}</span>
                </div>
            </div>
        </div>
    @endif

    <div class="p-6">
        <div class="flex justify-between items-start gap-4">
            <div class="flex-1">
                <!-- Facility Image and Info -->
                <div class="flex items-start gap-3 mb-3">
                    @if($headerImage)
                        <div class="flex-shrink-0">
                            <img src="{{ $headerImage }}" alt="{{ $jobPosting->facility->name }}" class="w-12 h-12 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600">
                        </div>
                    @else
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200 flex items-center justify-center text-sm font-bold border-2 border-gray-200 dark:border-gray-600">
                                {!! $initials ?: '&nbsp;' !!}
                            </div>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $jobPosting->facility->name }}</span>
                        </div>
                        @if($jobPosting->facility->address)
                            <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                <x-fas-map-marker-alt class="w-3 h-3 mr-1" />
                                <span>{{ $jobPosting->facility->address->city }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-3 mb-2">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                        <a href="{{ route('job-postings.show', $jobPosting) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                            {{ $jobPosting->title }}
                        </a>
                    </h3>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-{{ $jobPosting->getStatusColor() }}-100 text-{{ $jobPosting->getStatusColor() }}-800 dark:bg-{{ $jobPosting->getStatusColor() }}-900/30 dark:text-{{ $jobPosting->getStatusColor() }}-300">
                        {{ $jobPosting->getStatusLabel() }}
                    </span>
                </div>

                <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400 mb-3">
                    <div class="flex items-center">
                        <x-fas-briefcase class="w-4 h-4 mr-1" />
                        {{ $jobPosting->getEmploymentTypeLabel() }}
                    </div>
                    @if($jobPosting->job_category)
                        <div class="flex items-center">
                            <x-fas-tag class="w-4 h-4 mr-1" />
                            {{ $jobPosting->job_category }}
                        </div>
                    @endif
                </div>

                <p class="text-gray-600 dark:text-gray-400 line-clamp-2 mb-3">
                    {{ Str::limit(strip_tags($jobPosting->description), 200) }}
                </p>

                @if($jobPosting->published_at)
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Veröffentlicht am') }}: {{ $jobPosting->published_at->format('d.m.Y') }}
                        @if($jobPosting->expires_at)
                            | {{ __('Läuft ab am') }}: {{ $jobPosting->expires_at->format('d.m.Y') }}
                        @endif
                    </div>
                @endif
            </div>

            @if($showActions)
                <div class="flex flex-col gap-2">
                    <x-button tag="a" :href="route('job-postings.show', $jobPosting)" type="secondary" size="sm">
                        {{ __('Details') }}
                    </x-button>
                    @can('update', $jobPosting)
                        <x-button tag="a" :href="route('job-postings.edit', $jobPosting)" type="secondary" size="sm">
                            {{ __('Bearbeiten') }}
                        </x-button>
                    @endcan
                </div>
            @endif
        </div>

        <!-- Address Map -->
        @if($hasMap)
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <img src="{{ $mapUrl }}" alt="{{ __('Karte') }}" class="w-full h-auto rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm">
            </div>
        @endif
    </div>
</div>
