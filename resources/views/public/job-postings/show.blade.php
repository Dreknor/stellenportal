<x-layouts.public>
    <x-slot:title>{{ $jobPosting->title }} - {{ $jobPosting->facility->name }}</x-slot:title>
    <x-slot:metaDescription>{{ Str::limit(strip_tags($jobPosting->description), 155) }}</x-slot:metaDescription>
    <x-slot:ogType>article</x-slot:ogType>
    <x-slot:ogTitle>{{ $jobPosting->title }}</x-slot:ogTitle>
    <x-slot:ogDescription>{{ Str::limit(strip_tags($jobPosting->description), 200) }}</x-slot:ogDescription>
    @if($jobPosting->facility->getFirstMediaUrl('header_image'))
        <x-slot:ogImage>{{ $jobPosting->facility->getFirstMediaUrl('header_image') }}</x-slot:ogImage>
    @endif

    @push('structured-data')
    @php
        $jobPostingSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'JobPosting',
            'title' => $jobPosting->title,
            'description' => strip_tags($jobPosting->description),
            'datePosted' => $jobPosting->published_at->toIso8601String(),
            'validThrough' => ($jobPosting->expires_at ?? $jobPosting->published_at->addMonths(3))->toIso8601String(),
            'employmentType' => strtoupper(str_replace('_', '_', $jobPosting->employment_type)),
            'hiringOrganization' => array_filter([
                '@type' => 'Organization',
                'name' => $jobPosting->facility->name,
                'logo' => $jobPosting->facility->getFirstMediaUrl('logo') ?: null,
            ]),
            'url' => route('public.jobs.show', $jobPosting),
        ];

        if ($jobPosting->facility->address) {
            $jobPostingSchema['jobLocation'] = array_filter([
                '@type' => 'Place',
                'address' => array_filter([
                    '@type' => 'PostalAddress',
                    'streetAddress' => $jobPosting->facility->address->street,
                    'addressLocality' => $jobPosting->facility->address->city,
                    'postalCode' => $jobPosting->facility->address->postal_code,
                    'addressRegion' => $jobPosting->facility->address->state,
                    'addressCountry' => 'DE',
                ]),
                'geo' => ($jobPosting->facility->address->latitude && $jobPosting->facility->address->longitude) ? [
                    '@type' => 'GeoCoordinates',
                    'latitude' => $jobPosting->facility->address->latitude,
                    'longitude' => $jobPosting->facility->address->longitude,
                ] : null,
            ]);
        }

        if ($jobPosting->requirements) {
            $jobPostingSchema['qualifications'] = strip_tags($jobPosting->requirements);
        }

        if ($jobPosting->benefits) {
            $jobPostingSchema['benefits'] = strip_tags($jobPosting->benefits);
        }

        if ($jobPosting->contact_email) {
            $jobPostingSchema['applicationContact'] = array_filter([
                '@type' => 'ContactPoint',
                'email' => $jobPosting->contact_email,
                'telephone' => $jobPosting->contact_phone ?: null,
                'name' => $jobPosting->contact_person ?: null,
            ]);
        }
    @endphp
    <script type="application/ld+json">{!! json_encode($jobPostingSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

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

    <!-- Back Button -->
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('public.jobs.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline flex items-center" aria-label="Zurück zur Stellenangebote-Übersicht">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            {{ __('Zurück zur Übersicht') }}
        </a>

        <a href="{{ route('public.jobs.pdf', $jobPosting) }}"
           class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md transition-colors"
           target="_blank"
           aria-label="Stellenanzeige als PDF herunterladen">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            {{ __('Als PDF herunterladen') }}
        </a>
    </div>

    <!-- Facility Header Image Banner -->
    @if($headerImage)
        <div class="mb-6 rounded-lg overflow-hidden shadow-lg">
            <div class="h-48 md:h-64 bg-cover bg-center relative" style="background-image: url('{{ $headerImage }}')" role="img" aria-label="Bild der Einrichtung {{ $jobPosting->facility->name }}">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6">
                    <div class="flex items-center text-white mb-2">
                        <svg class="w-5 h-5 mr-2 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span class="text-lg font-semibold">{{ $jobPosting->facility->name }}</span>
                    </div>
                    @if($jobPosting->facility->address)
                        <div class="flex items-center text-white/90 text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>{{ $jobPosting->facility->address->city }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="mb-6 rounded-lg overflow-hidden shadow-lg">
            <div class="h-48 md:h-64 bg-gradient-to-r from-green-200 to-green-400 dark:from-gray-800 dark:to-gray-700 relative">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <div class="w-24 h-24 rounded-full bg-green-50 dark:bg-green-900 text-green-700 dark:text-green-200 flex items-center justify-center text-3xl font-bold mx-auto mb-4">
                            {!! $initials ?: '&nbsp;' !!}
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $jobPosting->facility->name }}</h2>
                        @if($jobPosting->facility->address)
                            <div class="flex items-center justify-center text-gray-700 dark:text-gray-300 text-sm mt-2">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>{{ $jobPosting->facility->address->city }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <article class="lg:col-span-2" itemscope itemtype="https://schema.org/JobPosting">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-4" itemprop="title">{{ $jobPosting->title }}</h1>

                    <div class="flex flex-wrap gap-3 mb-6">
                        <span class="px-4 py-2 rounded-full text-sm font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300" itemprop="employmentType">
                            {{ $jobPosting->getEmploymentTypeLabel() }}
                        </span>
                        @if($jobPosting->job_category)
                            <span class="px-4 py-2 rounded-full text-sm font-semibold bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                {{ $jobPosting->job_category }}
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <time datetime="{{ $jobPosting->published_at->toIso8601String() }}" itemprop="datePosted">
                            {{ __('Veröffentlicht am') }} {{ $jobPosting->published_at->format('d.m.Y') }}
                        </time>
                    </div>

                    <meta itemprop="validThrough" content="{{ ($jobPosting->expires_at ?? $jobPosting->published_at->addMonths(3))->toIso8601String() }}">
                </div>

                <!-- Description -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">{{ __('Stellenbeschreibung') }}</h2>
                    <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300" itemprop="description">
                        {!! nl2br(e($jobPosting->description)) !!}
                    </div>
                </div>

                @if($jobPosting->requirements)
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">{{ __('Das bringen Sie mit') }}</h2>
                        <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300" itemprop="qualifications">
                            {!! nl2br(e($jobPosting->requirements)) !!}
                        </div>
                    </div>
                @endif

                @if($jobPosting->benefits)
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">{{ __('Das bieten wir Ihnen') }}</h2>
                        <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300" itemprop="benefits">
                            {!! nl2br(e($jobPosting->benefits)) !!}
                        </div>
                    </div>
                @endif

                <div itemprop="hiringOrganization" itemscope itemtype="https://schema.org/Organization">
                    <meta itemprop="name" content="{{ $jobPosting->facility->name }}">
                </div>

                @if($jobPosting->facility->address)
                <div itemprop="jobLocation" itemscope itemtype="https://schema.org/Place">
                    <div itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
                        <meta itemprop="streetAddress" content="{{ $jobPosting->facility->address->street }}">
                        <meta itemprop="addressLocality" content="{{ $jobPosting->facility->address->city }}">
                        <meta itemprop="postalCode" content="{{ $jobPosting->facility->address->postal_code }}">
                        <meta itemprop="addressRegion" content="{{ $jobPosting->facility->address->state }}">
                        <meta itemprop="addressCountry" content="DE">
                    </div>
                </div>
                @endif
            </div>
        </article>

        <!-- Sidebar -->
        <aside class="space-y-6">
            <!-- Apply Card -->
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg shadow-sm border border-blue-200 dark:border-blue-800 p-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">{{ __('Interesse geweckt?') }}</h3>
                <p class="text-gray-700 dark:text-gray-300 mb-6">{{ __('Bewerben Sie sich jetzt für diese Stelle!') }}</p>

                @if($jobPosting->contact_email)
                    <a href="mailto:{{ $jobPosting->contact_email }}"
                       class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center font-medium py-3 px-6 rounded-md transition-colors mb-3"
                       aria-label="Per E-Mail bewerben">
                        {{ __('Jetzt bewerben') }}
                    </a>
                @endif
            </div>

            <!-- Facility Info -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">{{ __('Über die Einrichtung') }}</h3>

                <div class="space-y-4">
                    <!-- Facility Image -->
                    <div class="flex items-start gap-3">
                        @if($headerImage)
                            <div class="flex-shrink-0">
                                <img src="{{ $headerImage }}" alt="{{ $jobPosting->facility->name }} Logo" class="w-16 h-16 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600">
                            </div>
                        @else
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 rounded-full bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200 flex items-center justify-center text-lg font-bold border-2 border-gray-200 dark:border-gray-600" aria-hidden="true">
                                    {!! $initials ?: '&nbsp;' !!}
                                </div>
                            </div>
                        @endif
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800 dark:text-gray-100 mb-1">{{ $jobPosting->facility->name }}</p>
                            @if($jobPosting->facility->organization)
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $jobPosting->facility->organization->name }}</p>
                            @endif
                        </div>
                    </div>

                    @if($jobPosting->facility->address)
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <address class="not-italic">
                                    <p class="text-gray-700 dark:text-gray-300">{{ $jobPosting->facility->address->street }}</p>
                                    <p class="text-gray-700 dark:text-gray-300">
                                        {{ $jobPosting->facility->address->postal_code }} {{ $jobPosting->facility->address->city }}
                                    </p>
                                </address>
                            </div>
                        </div>
                    @endif

                    @if($jobPosting->facility->description)
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ Str::limit($jobPosting->facility->description, 200) }}
                            </p>
                        </div>
                    @endif

                    <!-- Address Map -->
                    @if($jobPosting->facility->address->latitude && $jobPosting->facility->address->longitude)
                        <!-- Map -->
                        <div class="mt-4">
                            <div id="facilityMap" class="h-48 rounded-lg border border-gray-200 dark:border-gray-600" role="img" aria-label="Karte mit Standort der Einrichtung"></div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Contact Info -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">{{ __('Kontakt') }}</h3>

                <div class="space-y-4">
                    @if($jobPosting->contact_person)
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $jobPosting->contact_person }}</p>
                            </div>
                        </div>
                    @endif

                    @if($jobPosting->contact_email)
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <a href="mailto:{{ $jobPosting->contact_email }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline break-all">
                                    {{ $jobPosting->contact_email }}
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($jobPosting->contact_phone)
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <div>
                                <a href="tel:{{ $jobPosting->contact_phone }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                    {{ $jobPosting->contact_phone }}
                                </a>
                            </div>
                        </div>
                    @endif

                    @if(!$jobPosting->contact_person && !$jobPosting->contact_email && !$jobPosting->contact_phone)
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Kontaktdaten siehe Einrichtung') }}
                        </p>
                    @endif
                </div>
            </div>

            <!-- Job Details -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">{{ __('Details') }}</h3>

                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-600 dark:text-gray-400">{{ __('Beschäftigungsart') }}</dt>
                        <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $jobPosting->getEmploymentTypeLabel() }}</dd>
                    </div>

                    @if($jobPosting->job_category)
                        <div class="flex justify-between">
                            <dt class="text-gray-600 dark:text-gray-400">{{ __('Kategorie') }}</dt>
                            <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $jobPosting->job_category }}</dd>
                        </div>
                    @endif

                    <div class="flex justify-between">
                        <dt class="text-gray-600 dark:text-gray-400">{{ __('Veröffentlicht') }}</dt>
                        <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $jobPosting->published_at->format('d.m.Y') }}</dd>
                    </div>

                    @if($jobPosting->expires_at)
                        <div class="flex justify-between">
                            <dt class="text-gray-600 dark:text-gray-400">{{ __('Bewerbung bis') }}</dt>
                            <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $jobPosting->expires_at->format('d.m.Y') }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </aside>
    </div>

    @if($jobPosting->facility->address && $jobPosting->facility->address->latitude && $jobPosting->facility->address->longitude)
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const map = L.map('facilityMap').setView([{{ $jobPosting->facility->address->latitude }}, {{ $jobPosting->facility->address->longitude }}], 15);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    L.marker([{{ $jobPosting->facility->address->latitude }}, {{ $jobPosting->facility->address->longitude }}]).addTo(map);
                });
            </script>
        @endpush
    @endif
</x-layouts.public>
