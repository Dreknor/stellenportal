<x-layouts.public>
    <x-slot:title>{{ __('Stellenangebote') }}</x-slot:title>

    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 dark:text-gray-100 mb-4">{{ __('Stellenangebote') }}</h1>
        <p class="text-gray-600 dark:text-gray-400">{{ __('Finden Sie Ihre nächste berufliche Herausforderung') }}</p>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
        <form method="GET" action="{{ route('public.jobs.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Suchbegriff') }}</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Stichwort, Berufsgruppe, Einrichtung...') }}"
                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Durchsucht Titel, Beschreibung, Anforderungen, Benefits und Einrichtungsname') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Ort / PLZ') }}</label>
                    <input type="text" name="location" value="{{ request('location') }}" placeholder="{{ __('z.B. Berlin oder 10115') }}"
                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Umkreis (km)') }}</label>
                    <select name="radius" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        <option value="10" {{ request('radius') == '10' ? 'selected' : '' }}>10 km</option>
                        <option value="25" {{ request('radius') == '25' ? 'selected' : '' }}>25 km</option>
                        <option value="50" {{ request('radius', '50') == '50' ? 'selected' : '' }}>50 km</option>
                        <option value="100" {{ request('radius') == '100' ? 'selected' : '' }}>100 km</option>
                        <option value="200" {{ request('radius') == '200' ? 'selected' : '' }}>200 km</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Beschäftigungsart') }}</label>
                    <select name="employment_type" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">{{ __('Alle') }}</option>
                        <option value="full_time" {{ request('employment_type') === 'full_time' ? 'selected' : '' }}>{{ __('Vollzeit') }}</option>
                        <option value="part_time" {{ request('employment_type') === 'part_time' ? 'selected' : '' }}>{{ __('Teilzeit') }}</option>
                        <option value="mini_job" {{ request('employment_type') === 'mini_job' ? 'selected' : '' }}>{{ __('Minijob') }}</option>
                        <option value="internship" {{ request('employment_type') === 'internship' ? 'selected' : '' }}>{{ __('Praktikum') }}</option>
                        <option value="apprenticeship" {{ request('employment_type') === 'apprenticeship' ? 'selected' : '' }}>{{ __('Ausbildung') }}</option>
                    </select>
                </div>

                <div class="flex items-end gap-2 md:col-span-1 lg:col-span-3">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        {{ __('Suchen') }}
                    </button>
                    @if(request()->hasAny(['search', 'location', 'employment_type']))
                        <a href="{{ route('public.jobs.index') }}" class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium py-2 px-4 rounded-md transition-colors">
                            {{ __('Zurücksetzen') }}
                        </a>
                    @endif
                </div>
            </div>

            @if(isset($searchLocation) && isset($searchCoordinates))
                <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-blue-800 dark:text-blue-300">
                                <strong>{{ __('Umkreissuche aktiv:') }}</strong> {{ request('radius', 50) }} km um {{ $searchCoordinates['display_name'] ?? $searchLocation }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </form>
    </div>

    <!-- Results Count -->
    <div class="mb-6">
        <p class="text-gray-600 dark:text-gray-400">
            @if(request()->hasAny(['search', 'location', 'employment_type']))
                <strong>{{ $jobPostings->total() }}</strong> {{ __('Stellenangebote gefunden') }}
            @else
                <strong>{{ $jobPostings->total() }}</strong> {{ __('aktive Stellenangebote') }}
            @endif
        </p>
    </div>

    <!-- Job Postings List -->
    <div class="space-y-6">
        @forelse($jobPostings as $jobPosting)
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
                <!-- Header Image Banner -->
                @if($headerImage)
                    <div class="h-32 bg-cover bg-center relative" style="background-image: url('{{ $headerImage }}')">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <div class="flex items-center text-white">
                                <svg class="w-4 h-4 mr-2 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span class="text-sm font-medium">{{ $jobPosting->facility->name }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="h-32 bg-gradient-to-r from-green-200 to-green-400 dark:from-gray-800 dark:to-gray-700 relative">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-16 h-16 rounded-full bg-green-50 dark:bg-green-900 text-green-700 dark:text-green-200 flex items-center justify-center text-xl font-bold">
                                {!! $initials ?: '&nbsp;' !!}
                            </div>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <div class="flex items-center text-gray-800 dark:text-gray-100">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span class="text-sm font-medium">{{ $jobPosting->facility->name }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="p-6">
                    <div class="flex justify-between items-start">
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
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <span>{{ $jobPosting->facility->address->city }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-3">
                                <a href="{{ route('public.jobs.show', $jobPosting) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                    {{ $jobPosting->title }}
                                </a>
                            </h2>

                            <div class="flex flex-wrap gap-3 mb-4">
                                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                    {{ $jobPosting->getEmploymentTypeLabel() }}
                                </span>
                                @if($jobPosting->job_category)
                                    <span class="px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        {{ $jobPosting->job_category }}
                                    </span>
                                @endif
                            </div>

                            <p class="text-gray-600 dark:text-gray-400 line-clamp-3 mb-4">
                                {{ Str::limit(strip_tags($jobPosting->description), 250) }}
                            </p>

                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('Veröffentlicht am') }} {{ $jobPosting->published_at->format('d.m.Y') }}
                            </div>
                        </div>

                        <div class="ml-6">
                            <a href="{{ route('public.jobs.show', $jobPosting) }}"
                               class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition-colors">
                                {{ __('Details') }}
                            </a>
                        </div>
                    </div>


                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <p class="text-gray-500 dark:text-gray-400 text-lg">{{ __('Keine Stellenangebote gefunden.') }}</p>
                <p class="text-gray-400 dark:text-gray-500 mt-2">{{ __('Versuchen Sie eine andere Suche.') }}</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($jobPostings->hasPages())
        <div class="mt-8">
            {{ $jobPostings->links() }}
        </div>
    @endif
</x-layouts.public>

