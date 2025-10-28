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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 01-2 2z"></path>
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
                    @if($jobPosting->facility->address && $jobPosting->facility->address->latitude && $jobPosting->facility->address->longitude)
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

            <!-- Share Buttons -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">{{ __('Stelle teilen') }}</h3>

                @php
                    $shareUrl = route('public.jobs.show', $jobPosting);
                    $shareText = $jobPosting->title . ' - ' . $jobPosting->facility->name;
                @endphp

                {{-- Nur anzeigen, wenn die Stelle veröffentlicht wurde --}}
                @if($jobPosting->published_at && $jobPosting->published_at->isPast())
                <div class="flex flex-wrap gap-3">
                    <button type="button" class="inline-flex items-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-sm" data-share="twitter" aria-label="Teilen auf Twitter">
                        <!-- Twitter SVG -->
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M19.633 7.997c.013.176.013.353.013.53 0 5.393-4.103 11.61-11.61 11.61-2.307 0-4.453-.676-6.253-1.847.329.038.657.051.998.051 1.91 0 3.668-.651 5.073-1.747-1.785-.038-3.294-1.21-3.816-2.829.25.038.501.064.767.064.373 0 .746-.05 1.094-.143-1.869-.374-3.272-2.029-3.272-4.014v-.051c.55.307 1.181.494 1.86.517-1.104-.736-1.828-1.989-1.828-3.409 0-.747.201-1.445.552-2.048 2.031 2.493 5.073 4.138 8.494 4.309-.064-.302-.101-.615-.101-.936 0-2.268 1.853-4.121 4.121-4.121 1.186 0 2.258.5 3.011 1.302.936-.176 1.8-.526 2.586-.998-.307.96-.96 1.766-1.816 2.277.829-.089 1.62-.32 2.356-.649-.55.821-1.248 1.542-2.049 2.115z"/></svg>
                        {{ __('Twitter') }}
                    </button>

                    <button type="button" class="inline-flex items-center px-3 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-md text-sm" data-share="facebook" aria-label="Teilen auf Facebook">
                        <!-- Facebook SVG -->
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M22 12.07C22 6.48 17.52 2 11.93 2 6.48 2 2 6.48 2 12.07c0 4.99 3.66 9.13 8.44 9.94v-7.03H8.08v-2.9h2.36V9.41c0-2.34 1.39-3.62 3.52-3.62.99 0 2.03.18 2.03.18v2.23h-1.14c-1.12 0-1.47.7-1.47 1.41v1.7h2.5l-.4 2.9h-2.1v7.03C18.34 21.2 22 17.06 22 12.07z"/></svg>
                        {{ __('Facebook') }}
                    </button>

                    <button type="button" class="inline-flex items-center px-3 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-md text-sm" data-share="linkedin" aria-label="Teilen auf LinkedIn">
                        <!-- LinkedIn SVG -->
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M4.98 3.5C4.98 4.88 3.88 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1 4.98 2.12 4.98 3.5zM.5 8.5h4V24h-4zM8.5 8.5h3.84v2.08h.05c.54-1.02 1.86-2.08 3.83-2.08 4.1 0 4.86 2.7 4.86 6.21V24h-4v-7.44c0-1.78-.03-4.08-2.49-4.08-2.49 0-2.87 1.95-2.87 3.96V24h-4z"/></svg>
                        {{ __('LinkedIn') }}
                    </button>

                    <button type="button" class="inline-flex items-center px-3 py-2 bg-green-500 hover:bg-green-600 text-white rounded-md text-sm" data-share="whatsapp" aria-label="Teilen auf WhatsApp">
                        <!-- WhatsApp SVG -->
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.52 3.48A11.9 11.9 0 0012.02 0C5.6 0 .6 5 0 11.38 0 13.1.43 14.74 1.24 16.2L0 24l7.94-1.18c1.44.79 3.07 1.2 4.76 1.2 6.42 0 11.42-5 12.02-11.38.1-.6.16-1.2.16-1.78 0-1.02-.08-2.02-.56-2.94zM12.02 20.06c-1.34 0-2.65-.36-3.78-1.04l-.27-.16-4.7.7.9-4.57-.18-.28A7.66 7.66 0 014.12 6.98c0-4.08 3.32-7.4 7.4-7.4 1.98 0 3.84.77 5.24 2.16A7.33 7.33 0 0120.44 12c0 4.07-3.32 7.4-8.42 8.06z"/></svg>
                        {{ __('WhatsApp') }}
                    </button>

                    <button type="button" id="copyLinkBtn" class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-md text-sm" aria-label="Link kopieren">
                        <!-- Copy SVG -->
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16h8M8 12h8M8 8h8M4 6h.01M4 10h.01M4 14h.01M4 18h.01"/></svg>
                        <span id="copyLinkText">{{ __('Link kopieren') }}</span>
                    </button>
                </div>
                @else
                    <p class="text-sm text-gray-500">{{ __('Diese Stelle ist noch nicht veröffentlicht und kann nicht geteilt werden.') }}</p>
                @endif
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

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const shareUrl = @json($shareUrl ?? route('public.jobs.show', $jobPosting));
                const shareText = @json($shareText ?? ($jobPosting->title . ' - ' . $jobPosting->facility->name));

                function openPopup(url) {
                    const width = 650;
                    const height = 450;
                    const left = (screen.width / 2) - (width / 2);
                    const top = (screen.height / 2) - (height / 2);
                    window.open(url, 'shareWindow', `toolbar=0,status=0,width=${width},height=${height},top=${top},left=${left}`);
                }

                document.querySelectorAll('[data-share]').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const provider = this.getAttribute('data-share');
                        let url = '';

                        switch (provider) {
                            case 'twitter':
                                url = `https://twitter.com/intent/tweet?text=${encodeURIComponent(shareText)}&url=${encodeURIComponent(shareUrl)}`;
                                break;
                            case 'facebook':
                                url = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`;
                                break;
                            case 'linkedin':
                                url = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(shareUrl)}`;
                                break;
                            case 'whatsapp':
                                url = `https://api.whatsapp.com/send?text=${encodeURIComponent(shareText + ' ' + shareUrl)}`;
                                break;
                        }

                        if (url) {
                            openPopup(url);
                        }
                    });
                });

                // Copy link
                const copyBtn = document.getElementById('copyLinkBtn');
                if (copyBtn) {
                    copyBtn.addEventListener('click', async function () {
                        try {
                            await navigator.clipboard.writeText(shareUrl);
                            const textEl = document.getElementById('copyLinkText');
                            const original = textEl.innerText;
                            textEl.innerText = @json(__('Kopiert!'));
                             setTimeout(() => textEl.innerText = original, 2000);
                        } catch (e) {
                            // Fallback: select and prompt
                            const tempInput = document.createElement('input');
                            tempInput.value = shareUrl;
                            document.body.appendChild(tempInput);
                            tempInput.select();
                            try { document.execCommand('copy'); }
                            catch (err) { /* ignore */ }
                            document.body.removeChild(tempInput);
                            const textEl = document.getElementById('copyLinkText');
                            const original = textEl.innerText;
                            textEl.innerText = @json(__('Kopiert!'));
                             setTimeout(() => textEl.innerText = original, 2000);
                        }
                    });
                }
            });
        </script>
    @endpush
</x-layouts.public>
