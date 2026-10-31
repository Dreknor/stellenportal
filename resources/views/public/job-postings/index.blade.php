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
                    'employmentType' => $job->getSchemaEmploymentType(),
                    'hiringOrganization' => [
                        '@type' => 'Organization',
                        'name' => $job->facility->name,
                        'logo' => $job->facility->getFirstMediaUrl('logo') ?: null,
                    ],
                    'url' => route('public.jobs.show', $job),
                ],
            ];

            // jobLocation ist ein von Google gefordertes Pflichtfeld.
            // Adresse bevorzugt von der Einrichtung, sonst vom Träger (Organisation).
            $jobLocationAddress = $job->facility->address
                ?: ($job->facility->organization?->address);

            if ($jobLocationAddress) {
                $streetAddress = trim($jobLocationAddress->street . ' ' . $jobLocationAddress->number);
                $postalAddress = ['@type' => 'PostalAddress'];

                if ($streetAddress !== '') {
                    $postalAddress['streetAddress'] = $streetAddress;
                }
                if ($jobLocationAddress->city) {
                    $postalAddress['addressLocality'] = $jobLocationAddress->city;
                }
                $postalAddress['addressRegion'] = $jobLocationAddress->getStateOrDefault();
                if ($jobLocationAddress->zip_code) {
                    $postalAddress['postalCode'] = $jobLocationAddress->zip_code;
                }
                $postalAddress['addressCountry'] = 'DE';

                $item['item']['jobLocation'] = [
                    '@type' => 'Place',
                    'address' => $postalAddress,
                ];

                if ($jobLocationAddress->latitude && $jobLocationAddress->longitude) {
                    $item['item']['jobLocation']['geo'] = [
                        '@type' => 'GeoCoordinates',
                        'latitude' => (string) $jobLocationAddress->latitude,
                        'longitude' => (string) $jobLocationAddress->longitude,
                    ];
                }
            } else {
                // Fallback: mindestens das Land angeben, damit das Pflichtfeld nie fehlt.
                $item['item']['jobLocation'] = [
                    '@type' => 'Place',
                    'address' => [
                        '@type' => 'PostalAddress',
                        'addressCountry' => 'DE',
                    ],
                ];
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
                $item['item']['applicationContact'] = [
                    '@type' => 'ContactPoint',
                    'email' => $job->contact_email,
                    'telephone' => $job->contact_phone ?: null,
                    'name' => $job->contact_person ?: null,
                ];
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
    <script type="application/ld+json">{!! json_encode($collectionPageSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
    @endpush

    @php
        $employmentTypes = [
            '' => __('Alle'),
            'full_time' => __('Vollzeit'),
            'part_time' => __('Teilzeit'),
            'mini_job' => __('Minijob'),
            'internship' => __('Praktikum'),
            'apprenticeship' => __('Ausbildung'),
            'volunteer' => __('Ehrenamt'),
        ];
        $inputClasses = 'w-full h-12 rounded-lg border-0 bg-sand-50 dark:bg-gray-700 text-base text-gray-900 dark:text-gray-100 placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:ring-2 focus:ring-primary-500';
    @endphp

    <x-slot:hero>
        <section class="bg-primary-800 text-white" aria-labelledby="jobs-heading">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-12 sm:pt-14 sm:pb-16">
                <p class="text-xs sm:text-sm font-bold tracking-widest uppercase text-primary-200 mb-3">{{ __('Jobs an evangelischen Schulen und Einrichtungen in Sachsen') }}</p>
                <h1 id="jobs-heading" class="text-3xl sm:text-5xl font-extrabold leading-tight mb-3">{{ __('Stellenangebote') }}</h1>
                <p class="text-base sm:text-lg text-primary-100 max-w-2xl mb-8">{{ __('Lehrkräfte, Schulbegleitung, Pädagogik und Verwaltung – finden Sie Ihre nächste berufliche Herausforderung.') }}</p>

                <form method="GET" action="{{ route('public.jobs.index') }}" role="search" aria-label="Stellenangebote durchsuchen">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-2 shadow-2xl shadow-black/20 grid grid-cols-1 md:grid-cols-12 gap-2">
                        <div class="md:col-span-4 relative">
                            <label for="search" class="sr-only">{{ __('Suchbegriff') }}</label>
                            <svg class="w-5 h-5 text-gray-500 absolute left-3.5 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="search" id="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Beruf, Fach oder Einrichtung') }}"
                                   class="{{ $inputClasses }} pl-11" aria-describedby="search-help">
                            <p id="search-help" class="sr-only">{{ __('Durchsucht Titel, Beschreibung, Anforderungen, Benefits und Einrichtungsname.') }}</p>
                        </div>

                        {{-- Ort-Autocomplete mit Alpine.js --}}
                        <div x-data="locationSearch(@js(request('location', '')), @js(request('lat', '')), @js(request('lon', '')))"
                             class="md:col-span-4 relative">
                            <label for="location" class="sr-only">{{ __('Ort oder PLZ') }}</label>
                            <svg class="w-5 h-5 text-gray-500 absolute left-3.5 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <input type="text"
                                   id="location"
                                   name="location"
                                   x-model="displayText"
                                   @input.debounce.400ms="fetchSuggestions"
                                   @keydown.escape="close"
                                   @keydown.arrow-down.prevent="navigateDown"
                                   @keydown.arrow-up.prevent="navigateUp"
                                   @keydown.enter="if (open && activeIndex >= 0) { $event.preventDefault(); selectActive(); }"
                                   @blur="onBlur"
                                   placeholder="{{ __('Ort oder PLZ, z. B. Dresden') }}"
                                   autocomplete="off"
                                   role="combobox"
                                   aria-autocomplete="list"
                                   aria-controls="location-suggestions"
                                   :aria-expanded="open && suggestions.length > 0"
                                   class="{{ $inputClasses }} pl-11 pr-9">
                            {{-- Lade-Indikator --}}
                            <span x-show="loading" class="absolute right-3 top-3.5 text-gray-400" aria-hidden="true">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                            </span>

                            {{-- Vorschläge-Dropdown --}}
                            <ul x-show="open && suggestions.length > 0"
                                id="location-suggestions"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border border-sand-200 dark:border-gray-600 rounded-xl shadow-lg max-h-72 overflow-y-auto text-gray-800"
                                role="listbox"
                                aria-label="{{ __('Ortsvorschläge') }}"
                                style="display: none;">
                                <template x-for="(s, i) in suggestions" :key="i">
                                    <li @mousedown.prevent="select(s)"
                                        @mouseenter="activeIndex = i"
                                        :class="activeIndex === i
                                            ? 'bg-primary-50 dark:bg-primary-900/30'
                                            : 'hover:bg-sand-50 dark:hover:bg-gray-700'"
                                        class="px-4 py-2.5 cursor-pointer border-b border-sand-100 dark:border-gray-700 last:border-0"
                                        role="option"
                                        :aria-selected="activeIndex === i">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <div class="min-w-0">
                                                <span class="block text-sm font-medium text-gray-800 dark:text-gray-100 truncate" x-text="s.short_name"></span>
                                                <span class="block text-xs text-gray-500 dark:text-gray-400 truncate" x-text="s.display_name"></span>
                                            </div>
                                        </div>
                                    </li>
                                </template>
                            </ul>

                            {{-- Voraufgelöste Koordinaten (werden nur gefüllt, wenn Nutzer einen Vorschlag auswählt) --}}
                            <input type="hidden" name="lat" x-model="lat">
                            <input type="hidden" name="lon" x-model="lon">
                        </div>

                        <div class="md:col-span-2">
                            <label for="radius" class="sr-only">{{ __('Umkreis (km)') }}</label>
                            <select id="radius" name="radius" class="{{ $inputClasses }} px-3">
                                @foreach([10, 25, 50, 100, 200] as $km)
                                    <option value="{{ $km }}" @selected((string) request('radius', '50') === (string) $km)>+{{ $km }} km</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="md:col-span-2 h-12 rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-base font-bold px-5 transition-colors">
                            {{ __('Stellen finden') }}
                        </button>
                    </div>

                    <fieldset class="mt-5">
                        <legend class="sr-only">{{ __('Beschäftigungsart') }}</legend>
                        <div class="flex flex-wrap gap-2">
                            @foreach($employmentTypes as $value => $label)
                                <label class="cursor-pointer">
                                    <input type="radio" name="employment_type" value="{{ $value }}" class="peer sr-only"
                                           @checked((string) request('employment_type', '') === (string) $value)
                                           onchange="this.form.submit()">
                                    <span class="inline-flex items-center min-h-10 px-4 rounded-full text-sm font-medium border border-white/40 text-white hover:bg-white/10 peer-checked:bg-white peer-checked:text-primary-800 peer-checked:border-white peer-focus-visible:outline peer-focus-visible:outline-3 peer-focus-visible:outline-primary-300 transition-colors">
                                        {{ $label }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                </form>
            </div>
        </section>
    </x-slot:hero>

    <!-- Results Count -->
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5" role="status" aria-live="polite">
        <p class="text-base text-gray-700 dark:text-gray-300">
            @if(request()->hasAny(['search', 'location', 'employment_type']))
                <strong class="text-gray-900 dark:text-gray-100">{{ $jobPostings->total() }}</strong> {{ __('Stellenangebote gefunden') }}
            @else
                <strong class="text-gray-900 dark:text-gray-100">{{ $jobPostings->total() }}</strong> {{ __('aktive Stellenangebote') }}
            @endif
            @if(isset($searchLocation) && isset($searchCoordinates))
                <span class="text-gray-600 dark:text-gray-400">· {{ __('im Umkreis von') }} {{ request('radius', 50) }} km {{ __('um') }} {{ $searchCoordinates['display_name'] ?? $searchLocation }}</span>
            @endif
        </p>
        @if(request()->hasAny(['search', 'location', 'employment_type']))
            <a href="{{ route('public.jobs.index') }}" class="text-sm font-semibold text-primary-700 dark:text-primary-300 hover:underline">
                {{ __('Alle Filter zurücksetzen') }}
            </a>
        @endif
    </div>

    <!-- Job Postings List -->
    <div class="space-y-3">
        @forelse($jobPostings as $jobPosting)
            @php
                // Compute initials for placeholder (max 2 letters)
                $initials = '';
                $nameParts = preg_split('/\s+/', trim((string) $jobPosting->facility->name));
                if (!empty($nameParts)) {
                    $firstTwo = array_slice($nameParts, 0, 2);
                    foreach ($firstTwo as $part) {
                        $initials .= mb_strtoupper(mb_substr($part, 0, 1));
                    }
                }

                // Logo bevorzugt für das kleine Icon, Header-Bild als Fallback
                $facilityLogo = $jobPosting->facility->getFirstMediaUrl('logo') ?: $jobPosting->facility->getFirstMediaUrl('header_image');
                $isNew = $jobPosting->published_at->gt(now()->subDays(7));
                $validThrough = $jobPosting->expires_at ?? $jobPosting->published_at->copy()->addMonths(3);
            @endphp

            <article class="group relative bg-white dark:bg-gray-800 rounded-2xl border border-sand-200 dark:border-gray-700 p-4 sm:p-5 flex gap-4 sm:gap-5 hover:border-primary-500 hover:shadow-lg hover:shadow-primary-900/5 transition"
                     itemscope itemtype="https://schema.org/JobPosting">
                <div class="flex-shrink-0">
                    @if($facilityLogo)
                        <img src="{{ $facilityLogo }}" alt="{{ $jobPosting->facility->name }} Logo" loading="lazy"
                             class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl object-cover border border-sand-200 dark:border-gray-600 bg-white">
                    @else
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-200 flex items-center justify-center text-base sm:text-lg font-extrabold" aria-hidden="true">
                            @if($initials){{ $initials }}@else&nbsp;@endif
                        </div>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mb-1">
                        <h2 class="text-lg font-bold leading-snug text-gray-900 dark:text-gray-100" itemprop="title">
                            <a href="{{ route('public.jobs.show', $jobPosting) }}" class="group-hover:text-primary-700 dark:group-hover:text-primary-300 after:absolute after:inset-0 after:rounded-2xl" itemprop="url">
                                {{ $jobPosting->title }}
                            </a>
                        </h2>
                        @if($isNew)
                            <span class="text-xs font-bold bg-amber-100 text-amber-900 dark:bg-amber-900/40 dark:text-amber-200 rounded-md px-2 py-0.5">{{ __('Neu') }}</span>
                        @endif
                    </div>

                    <div class="text-sm sm:text-base text-gray-600 dark:text-gray-400 mb-3" itemprop="hiringOrganization" itemscope itemtype="https://schema.org/Organization">
                        <span itemprop="name">{{ $jobPosting->facility->name }}</span>
                        @if($facilityLogo)
                            <meta itemprop="logo" content="{{ $facilityLogo }}">
                        @endif
                        @if($jobPosting->facility->address)
                            <span aria-hidden="true">·</span>
                            <span>{{ $jobPosting->facility->address->zip_code }} {{ $jobPosting->facility->address->city }}</span>
                        @endif
                    </div>

                    @if($jobPosting->facility->address)
                        <div itemprop="jobLocation" itemscope itemtype="https://schema.org/Place" hidden>
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

                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-3" itemprop="description">
                        {{ Str::limit(strip_tags($jobPosting->description), 220) }}
                    </p>

                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 bg-sand-100 dark:bg-gray-700 rounded-md px-2.5 py-1" >
                            <meta itemprop="employmentType" content="{{ $jobPosting->getSchemaEmploymentType() }}">
                            {{ $jobPosting->getEmploymentTypeLabel() }}
                        </span>
                        @if($jobPosting->job_category)
                            <span class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 bg-sand-100 dark:bg-gray-700 rounded-md px-2.5 py-1">
                                {{ $jobPosting->job_category }}
                            </span>
                        @endif
                        <time class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 ml-auto" datetime="{{ $jobPosting->published_at->toIso8601String() }}" itemprop="datePosted">
                            {{ __('Veröffentlicht am') }} {{ $jobPosting->published_at->format('d.m.Y') }}
                        </time>
                    </div>

                    <meta itemprop="validThrough" content="{{ $validThrough->toIso8601String() }}">
                </div>
            </article>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-sand-200 dark:border-gray-700 p-10 sm:p-14 text-center" role="status">
                <div class="w-14 h-14 mx-auto rounded-full bg-primary-50 dark:bg-gray-700 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-primary-700 dark:text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Keine Stellenangebote gefunden.') }}</p>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Versuchen Sie eine andere Suche oder vergrößern Sie den Umkreis.') }}</p>
                @if(request()->hasAny(['search', 'location', 'employment_type']))
                    <a href="{{ route('public.jobs.index') }}" class="inline-block mt-5 px-5 py-2.5 rounded-lg border border-primary-200 text-primary-800 dark:text-primary-200 dark:border-gray-600 font-semibold hover:bg-primary-50 dark:hover:bg-gray-700">{{ __('Alle Stellen anzeigen') }}</a>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($jobPostings->hasPages())
        <nav class="mt-8" aria-label="Seitennummerierung">
            {{ $jobPostings->withQueryString()->links() }}
        </nav>
    @endif

    <!-- Call to action for employers -->
    <section class="mt-12 bg-white dark:bg-gray-800 border border-sand-200 dark:border-gray-700 rounded-2xl p-6 sm:p-8 flex flex-col md:flex-row md:items-center md:justify-between gap-5" aria-labelledby="employer-cta-heading">
        <div>
            <h2 id="employer-cta-heading" class="text-xl sm:text-2xl font-extrabold text-primary-800 dark:text-primary-200 mb-1">{{ __('Sie suchen Personal für Ihre Einrichtung?') }}</h2>
            <p class="text-gray-600 dark:text-gray-400">{{ __('Stellenanzeige anlegen, mit Credits veröffentlichen – drei Monate sichtbar.') }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="px-5 py-3 rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold">{{ __('Jetzt registrieren') }}</a>
            @endif
            <a href="{{ route('public.pricing') }}" class="px-5 py-3 rounded-lg border border-primary-200 dark:border-gray-600 text-primary-800 dark:text-primary-200 font-semibold hover:bg-primary-50 dark:hover:bg-gray-700">{{ __('Preise ansehen') }}</a>
        </div>
    </section>

    @push('scripts')
    <script>
        function locationSearch(initialText, initialLat, initialLon) {
            return {
                displayText: initialText,
                lat: initialLat,
                lon: initialLon,
                suggestions: [],
                open: false,
                activeIndex: -1,
                loading: false,

                fetchSuggestions() {
                    // Koordinaten zurücksetzen, sobald der Nutzer tippt
                    this.lat = '';
                    this.lon = '';

                    if (this.displayText.length < 2) {
                        this.suggestions = [];
                        this.open = false;
                        return;
                    }

                    this.loading = true;

                    fetch('/api/location-suggestions?q=' + encodeURIComponent(this.displayText))
                        .then(r => r.json())
                        .then(data => {
                            this.suggestions = data;
                            this.open = data.length > 0;
                            this.activeIndex = -1;
                            this.loading = false;
                        })
                        .catch(() => {
                            this.loading = false;
                        });
                },

                select(s) {
                    this.displayText = s.short_name;
                    this.lat = s.lat;
                    this.lon = s.lon;
                    this.open = false;
                    this.suggestions = [];
                },

                selectActive() {
                    if (this.activeIndex >= 0 && this.suggestions[this.activeIndex]) {
                        this.select(this.suggestions[this.activeIndex]);
                    }
                    // Kein aktiver Eintrag → Formular normal absenden (Enter-Taste propagiert)
                },

                navigateDown() {
                    if (!this.open) return;
                    this.activeIndex = Math.min(this.activeIndex + 1, this.suggestions.length - 1);
                },

                navigateUp() {
                    if (!this.open) return;
                    this.activeIndex = Math.max(this.activeIndex - 1, 0);
                },

                close() {
                    this.open = false;
                    this.activeIndex = -1;
                },

                onBlur() {
                    // Verzögerung, damit @mousedown auf dem Eintrag noch feuert
                    setTimeout(() => { this.open = false; }, 200);
                },
            };
        }
    </script>
    @endpush
</x-layouts.public>

