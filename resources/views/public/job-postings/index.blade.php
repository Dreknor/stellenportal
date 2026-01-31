<x-layouts.public>
    <x-slot:title>{{ __('Stellenangebote') }}</x-slot:title>
    <x-slot:metaDescription>{{ __('Durchsuchen Sie aktuelle Stellenangebote für Lehrkräfte, Schulbegleitung und pädagogisches Personal an evangelischen Schulen und Einrichtungen in Sachsen.') }}</x-slot:metaDescription>

    @push('structured-data')
    @php
        $itemListElements = [];
        foreach ($jobPostings as $index => $job) {
            $item = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => [
                    '@type' => 'JobPosting',
                    'title' => $job->title,
                    'description' => Str::limit(strip_tags($job->description), 200),
                    'datePosted' => $job->published_at->toIso8601String(),
                    'validThrough' => ($job->expires_at ?? $job->published_at->addMonths(3))->toIso8601String(),
                    'employmentType' => strtoupper(str_replace('_', '_', $job->employment_type)),
                    'hiringOrganization' => array_filter([
                        '@type' => 'Organization',
                        'name' => $job->facility->name,
                        'logo' => $job->facility->getFirstMediaUrl('logo') ?: null,
                    ]),
                    'url' => route('public.jobs.show', $job),
                ],
            ];

            if ($job->facility->address) {
                $streetAddress = trim($job->facility->address->street . ' ' . $job->facility->address->number);
                $item['item']['jobLocation'] = [
                    '@type' => 'Place',
                    'address' => array_filter([
                        '@type' => 'PostalAddress',
                        'streetAddress' => $streetAddress ?: null,
                        'addressLocality' => $job->facility->address->city,
                        'addressRegion' => $job->facility->address->getStateOrDefault(),
                        'postalCode' => $job->facility->address->zip_code,
                        'addressCountry' => 'DE',
                    ]),
                ];

                if ($job->facility->address->latitude && $job->facility->address->longitude) {
                    $item['item']['jobLocation']['geo'] = [
                        '@type' => 'GeoCoordinates',
                        'latitude' => $job->facility->address->latitude,
                        'longitude' => $job->facility->address->longitude,
                    ];
                }
            }

            if ($job->requirements) {
                $item['item']['qualifications'] = strip_tags($job->requirements);
            }

            if ($job->benefits) {
                $item['item']['benefits'] = strip_tags($job->benefits);
            }

            // Add baseSalary if salary information is available
            if ($job->salary_min || $job->salary_max) {
                $baseSalary = [
                    '@type' => 'MonetaryAmount',
                    'currency' => 'EUR',
                ];

                if ($job->salary_min && $job->salary_max) {
                    $baseSalary['value'] = [
                        '@type' => 'QuantitativeValue',
                        'minValue' => $job->salary_min,
                        'maxValue' => $job->salary_max,
                        'unitText' => 'YEAR'
                    ];
                } elseif ($job->salary_min) {
                    $baseSalary['value'] = [
                        '@type' => 'QuantitativeValue',
                        'minValue' => $job->salary_min,
                        'unitText' => 'YEAR'
                    ];
                } elseif ($job->salary_max) {
                    $baseSalary['value'] = [
                        '@type' => 'QuantitativeValue',
                        'maxValue' => $job->salary_max,
                        'unitText' => 'YEAR'
                    ];
                }

                $item['item']['baseSalary'] = $baseSalary;
            }

            if ($job->contact_email) {
                $item['item']['applicationContact'] = array_filter([
                    '@type' => 'ContactPoint',
                    'email' => $job->contact_email,
                    'telephone' => $job->contact_phone ?: null,
                    'name' => $job->contact_person ?: null,
                ]);
            }

            $itemListElements[] = $item;
        }

        $collectionPageSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => 'Stellenangebote',
            'description' => 'Aktuelle Stellenangebote für Lehrkräfte und pädagogisches Personal',
            'url' => url()->current(),
            'mainEntity' => [
                '@type' => 'ItemList',
                'numberOfItems' => $jobPostings->total(),
                'itemListElement' => $itemListElements,
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($collectionPageSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 dark:text-gray-100 mb-4">{{ __('Stellenangebote') }}</h1>
        <p class="text-gray-600 dark:text-gray-400">{{ __('Finden Sie Ihre nächste berufliche Herausforderung') }}</p>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8" role="search" aria-label="Stellenangebote durchsuchen">
        <form method="GET" action="{{ route('public.jobs.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Suchbegriff') }}</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Stichwort, Berufsgruppe, Einrichtung...') }}"
                           class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                           aria-describedby="search-help">
                    <p id="search-help" class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Durchsucht Titel, Beschreibung, Anforderungen, Benefits und Einrichtungsname') }}</p>
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Ort / PLZ') }}</label>
                    <input type="text" id="location" name="location" value="{{ request('location') }}" placeholder="{{ __('z.B. Berlin oder 10115') }}"
                           class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                </div>

                <div>
                    <label for="radius" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Umkreis (km)') }}</label>
                    <select id="radius" name="radius" class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
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
                    <label for="employment_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Beschäftigungsart') }}</label>
                    <select id="employment_type" name="employment_type" class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">{{ __('Alle') }}</option>
                        <option value="full_time" {{ request('employment_type') === 'full_time' ? 'selected' : '' }}>{{ __('Vollzeit') }}</option>
                        <option value="part_time" {{ request('employment_type') === 'part_time' ? 'selected' : '' }}>{{ __('Teilzeit') }}</option>
                        <option value="mini_job" {{ request('employment_type') === 'mini_job' ? 'selected' : '' }}>{{ __('Minijob') }}</option>
                        <option value="internship" {{ request('employment_type') === 'internship' ? 'selected' : '' }}>{{ __('Praktikum') }}</option>
                        <option value="apprenticeship" {{ request('employment_type') === 'apprenticeship' ? 'selected' : '' }}>{{ __('Ausbildung') }}</option>
                        <option value="volunteer" {{ request('employment_type') === 'volunteer' ? 'selected' : '' }}>{{ __('Ehrenamt') }}</option>
                    </select>
                </div>

                <div class="flex items-end gap-2 md:col-span-1 lg:col-span-3">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors flex items-center justify-center" aria-label="Stellenangebote suchen">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        {{ __('Suchen') }}
                    </button>
                    @if(request()->hasAny(['search', 'location', 'employment_type']))
                        <a href="{{ route('public.jobs.index') }}" class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium py-2 px-4 rounded-md transition-colors" aria-label="Suchfilter zurücksetzen">
                            {{ __('Zurücksetzen') }}
                        </a>
                    @endif
                </div>
            </div>

            @if(isset($searchLocation) && isset($searchCoordinates))
                <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md" role="status" aria-live="polite">
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
    <div class="mb-6" role="status" aria-live="polite">
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

            <article class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow"
                     itemscope itemtype="https://schema.org/JobPosting">
                <!-- Header Image Banner -->
                @if($headerImage)
                    <div class="h-32 bg-cover bg-center relative" style="background-image: url('{{ $headerImage }}')" role="img" aria-label="Bild der Einrichtung {{ $jobPosting->facility->name }}">
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
                                @php
                                    // Logo bevorzugt für das kleine Icon, Header-Bild als Fallback
                                    $facilityLogo = $jobPosting->facility->getFirstMediaUrl('logo') ?: $jobPosting->facility->getFirstMediaUrl('header_image');
                                @endphp
                                @if($facilityLogo)
                                    <div class="flex-shrink-0">
                                        <img src="{{ $facilityLogo }}" alt="{{ $jobPosting->facility->name }} Logo" class="w-12 h-12 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600">
                                    </div>
                                @else
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200 flex items-center justify-center text-sm font-bold border-2 border-gray-200 dark:border-gray-600" aria-hidden="true">
                                            {!! $initials ?: '&nbsp;' !!}
                                        </div>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0" itemprop="hiringOrganization" itemscope itemtype="https://schema.org/Organization">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300" itemprop="name">{{ $jobPosting->facility->name }}</span>
                                    </div>
                                    @if($jobPosting->facility->address)
                                        <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <span>{{ $jobPosting->facility->address->city }}</span>
                                            @if($jobPosting->facility->address->zip_code)
                                                , {{ $jobPosting->facility->address->zip_code }}
                                            @endif
                                        </div>
                                        <div itemprop="jobLocation" itemscope itemtype="https://schema.org/Place" style="display:none;">
                                            <div itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
                                                <meta itemprop="streetAddress" content="{{ $jobPosting->facility->address->street }} {{ $jobPosting->facility->address->number }}">
                                                <meta itemprop="addressLocality" content="{{ $jobPosting->facility->address->city }}">
                                                <meta itemprop="addressRegion" content="{{ $jobPosting->facility->address->getStateOrDefault() }}">
                                                <meta itemprop="postalCode" content="{{ $jobPosting->facility->address->zip_code }}">
                                                <meta itemprop="addressCountry" content="DE">
                                            </div>
                                            @if($jobPosting->facility->address->latitude && $jobPosting->facility->address->longitude)
                                            <div itemprop="geo" itemscope itemtype="https://schema.org/GeoCoordinates">
                                                <meta itemprop="latitude" content="{{ $jobPosting->facility->address->latitude }}">
                                                <meta itemprop="longitude" content="{{ $jobPosting->facility->address->longitude }}">
                                            </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-3" itemprop="title">
                                <a href="{{ route('public.jobs.show', $jobPosting) }}" class="hover:text-blue-600 dark:hover:text-blue-400" aria-label="Details zur Stelle {{ $jobPosting->title }}">
                                    {{ $jobPosting->title }}
                                </a>
                            </h2>

                            <div class="flex flex-wrap gap-3 mb-4">
                                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300" itemprop="employmentType">
                                    {{ $jobPosting->getEmploymentTypeLabel() }}
                                </span>
                                @if($jobPosting->job_category)
                                    <span class="px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        {{ $jobPosting->job_category }}
                                    </span>
                                @endif
                            </div>

                            <p class="text-gray-600 dark:text-gray-400 line-clamp-3 mb-4" itemprop="description">
                                {{ Str::limit(strip_tags($jobPosting->description), 250) }}
                            </p>

                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <time datetime="{{ $jobPosting->published_at->toIso8601String() }}" itemprop="datePosted">
                                    {{ __('Veröffentlicht am') }} {{ $jobPosting->published_at->format('d.m.Y') }}
                                </time>
                            </div>

                            <meta itemprop="validThrough" content="{{ $jobPosting->published_at->addMonths(3)->toIso8601String() }}">
                        </div>

                        <div class="ml-6">
                            <a href="{{ route('public.jobs.show', $jobPosting) }}"
                               class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition-colors"
                               aria-label="Details zur Stelle {{ $jobPosting->title }} ansehen">
                                {{ __('Details') }}
                            </a>
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center" role="status">
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
        <nav class="mt-8" aria-label="Seitennummerierung">
            {{ $jobPostings->links() }}
        </nav>
    @endif
</x-layouts.public>

