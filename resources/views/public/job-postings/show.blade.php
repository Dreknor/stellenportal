<x-layouts.public>
    <x-slot:title>{{ $jobPosting->title }} - {{ $jobPosting->facility->name }}</x-slot:title>
    <x-slot:metaDescription>{{ $jobPosting->getSeoMetaDescription() }}</x-slot:metaDescription>
    <x-slot:ogType>article</x-slot:ogType>
    <x-slot:ogTitle>{{ $jobPosting->title }}</x-slot:ogTitle>
    <x-slot:ogDescription>{{ $jobPosting->getSeoMetaDescription() }}</x-slot:ogDescription>
    @if($jobPosting->facility->getFirstMediaUrl('header_image'))
        <x-slot:ogImage>{{ $jobPosting->facility->getFirstMediaUrl('header_image') }}</x-slot:ogImage>
    @endif

    @push('head')
    <meta name="keywords" content="{{ implode(', ', $jobPosting->getSeoKeywordsArray()) }}">
    @endpush

    @push('structured-data')

    @php
        $jobPostingSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'JobPosting',
        'title' => $jobPosting->title,
        'description' => strip_tags($jobPosting->description),
        'datePosted' => $jobPosting->published_at->toIso8601String(),
        'validThrough' => ($jobPosting->expires_at ?? $jobPosting->published_at->addMonths(3))->toIso8601String(),
        'employmentType' => $jobPosting->getSchemaEmploymentType(),
        'hiringOrganization' => [
            '@type' => 'Organization',
            'name' => $jobPosting->facility->name,
            'logo' => $jobPosting->facility->getFirstMediaUrl('logo') ?: null,
        ],
        'url' => route('public.jobs.show', $jobPosting),
    ];

    // jobLocation ist ein von Google gefordertes Pflichtfeld.
    // Adresse bevorzugt von der Einrichtung, sonst vom Träger (Organisation).
    $jobLocationAddress = $jobPosting->facility->address
        ?: ($jobPosting->facility->organization?->address);

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

        $jobPostingSchema['jobLocation'] = [
            '@type' => 'Place',
            'address' => $postalAddress,
        ];

        if ($jobLocationAddress->latitude && $jobLocationAddress->longitude) {
            $jobPostingSchema['jobLocation']['geo'] = [
                '@type' => 'GeoCoordinates',
                'latitude' => $jobLocationAddress->latitude,
                'longitude' => $jobLocationAddress->longitude,
            ];
        }
    } else {
        // Fallback: Wenn keine Adresse hinterlegt ist, mindestens das Land angeben,
        // damit das Pflichtfeld "jobLocation" niemals fehlt.
        $jobPostingSchema['jobLocation'] = [
            '@type' => 'Place',
            'address' => [
                '@type' => 'PostalAddress',
                'addressCountry' => 'DE',
            ],
        ];
    }

    if ($jobPosting->requirements) {
        $jobPostingSchema['qualifications'] = strip_tags($jobPosting->requirements);
    }

    if ($jobPosting->benefits) {
        $jobPostingSchema['jobBenefits'] = strip_tags($jobPosting->benefits);
    }

    // Add baseSalary if salary information is available
    if ($jobPosting->salary_min || $jobPosting->salary_max) {
        $baseSalary = [
            '@type' => 'MonetaryAmount',
            'currency' => 'EUR',
        ];

        if ($jobPosting->salary_min && $jobPosting->salary_max) {
            $baseSalary['value'] = [
                '@type' => 'QuantitativeValue',
                'minValue' => $jobPosting->salary_min,
                'maxValue' => $jobPosting->salary_max,
                'unitText' => 'YEAR'
            ];
        } elseif ($jobPosting->salary_min) {
            $baseSalary['value'] = [
                '@type' => 'QuantitativeValue',
                'minValue' => $jobPosting->salary_min,
                'unitText' => 'YEAR'
            ];
        } elseif ($jobPosting->salary_max) {
            $baseSalary['value'] = [
                '@type' => 'QuantitativeValue',
                'maxValue' => $jobPosting->salary_max,
                'unitText' => 'YEAR'
            ];
        }

        $jobPostingSchema['baseSalary'] = $baseSalary;
    }

    if ($jobPosting->contact_email) {
        $jobPostingSchema['applicationContact'] = [
            '@type' => 'ContactPoint',
            'email' => $jobPosting->contact_email,
            'telephone' => $jobPosting->contact_phone ?: null,
            'name' => $jobPosting->contact_person ?: null,
        ];
    }
    @endphp
    {{-- Google for Jobs: abgelaufene/inaktive Stellen dürfen kein JobPosting-Markup mehr tragen --}}
    @unless($isExpired)
    <script type="application/ld+json">{!! json_encode($jobPostingSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
    @endunless

    {{-- Separate Organization structured data for better Google visibility --}}
    @php
        // Verwende Einrichtungsdaten, falls vorhanden, sonst Trägerdaten
        $orgData = $jobPosting->facility;
        $parentOrg = $jobPosting->facility->organization;

        $organizationSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $orgData->name,
        ];

        // URL
        if ($orgData->website) {
            $organizationSchema['url'] = $orgData->website;
        } elseif ($parentOrg && $parentOrg->website) {
            $organizationSchema['url'] = $parentOrg->website;
        }

        // Logo
        $logo = $orgData->getFirstMediaUrl('logo') ?: ($parentOrg ? $parentOrg->getFirstMediaUrl('logo') : null);
        if ($logo) {
            $organizationSchema['logo'] = $logo;
        }

        // Image/Photo
        $image = $orgData->getFirstMediaUrl('header_image') ?: ($parentOrg ? $parentOrg->getFirstMediaUrl('header_image') : null);
        if ($image) {
            $organizationSchema['image'] = $image;
        }

        // Description
        if ($orgData->description) {
            $organizationSchema['description'] = strip_tags($orgData->description);
        } elseif ($parentOrg && $parentOrg->description) {
            $organizationSchema['description'] = strip_tags($parentOrg->description);
        }

        // Address - bevorzuge Einrichtungsadresse, sonst Trägeradresse
        $address = $orgData->address ?: ($parentOrg ? $parentOrg->address : null);
        if ($address) {
            $streetAddress = trim($address->street . ' ' . $address->number);
            $organizationSchema['address'] = [
                '@type' => 'PostalAddress',
                'streetAddress' => $streetAddress,
                'addressLocality' => $address->city,
                'addressRegion' => $address->getStateOrDefault(),
                'postalCode' => $address->zip_code,
                'addressCountry' => 'DE',
            ];

            // Geo-Koordinaten
            if ($address->latitude && $address->longitude) {
                $organizationSchema['geo'] = [
                    '@type' => 'GeoCoordinates',
                    'latitude' => (string) $address->latitude,
                    'longitude' => (string) $address->longitude,
                ];
            }
        }

        // Contact Point - E-Mail und Telefon
        $email = $orgData->email ?: ($parentOrg ? $parentOrg->email : null);
        $phone = $orgData->phone ?: ($parentOrg ? $parentOrg->phone : null);

        if ($email || $phone) {
            $organizationSchema['contactPoint'] = [
                '@type' => 'ContactPoint',
                'contactType' => 'customer service',
            ];
            if ($email) {
                $organizationSchema['contactPoint']['email'] = $email;
            }
            if ($phone) {
                $organizationSchema['contactPoint']['telephone'] = $phone;
            }
        }

        // Parent Organization - falls die Einrichtung zu einem Träger gehört
        if ($parentOrg && $orgData->id !== $parentOrg->id) {
            $organizationSchema['parentOrganization'] = [
                '@type' => 'Organization',
                'name' => $parentOrg->name,
            ];
            if ($parentOrg->website) {
                $organizationSchema['parentOrganization']['url'] = $parentOrg->website;
            }
            if ($parentOrg->getFirstMediaUrl('logo')) {
                $organizationSchema['parentOrganization']['logo'] = $parentOrg->getFirstMediaUrl('logo');
            }
        }
    @endphp
    <script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
    @endpush

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

        // Obfuscate email and phone for bot protection
        $obfuscatedEmail = $jobPosting->contact_email ? base64_encode($jobPosting->contact_email) : null;
        $obfuscatedPhone = $jobPosting->contact_phone ? base64_encode($jobPosting->contact_phone) : null;

        // Create display versions (partially hidden)
        $displayEmail = $jobPosting->contact_email ? preg_replace('/(?<=.{2}).(?=.*@)/u', '*', $jobPosting->contact_email) : null;
        $displayPhone = $jobPosting->contact_phone ? preg_replace('/(?<=.{3}).(?=.{2})/u', '*', $jobPosting->contact_phone) : null;
    @endphp

    @php
        $facilityLogo = $jobPosting->facility->getFirstMediaUrl('logo') ?: $jobPosting->facility->getFirstMediaUrl('header_image');
        $facilityHeader = $jobPosting->facility->getFirstMediaUrl('header_image') ?: $jobPosting->facility->getFirstMediaUrl('header') ?: $jobPosting->facility->getFirstMediaUrl('cover');
        $validThrough = $jobPosting->expires_at ?? $jobPosting->published_at->copy()->addMonths(3);
        $city = $jobPosting->facility->address?->city;
        $cardClasses = 'bg-white dark:bg-gray-800 rounded-2xl border border-sand-200 dark:border-gray-700';
        $sectionHeadingClasses = 'text-xl sm:text-2xl font-bold text-primary-800 dark:text-primary-200 mb-3';
        $textClasses = 'text-base leading-relaxed text-gray-700 dark:text-gray-300';
    @endphp

    @push('structured-data')
        @php
            $breadcrumbItems = [
                ['name' => config('app.name'), 'url' => url('/')],
                ['name' => 'Stellenangebote', 'url' => route('public.jobs.index')],
                ['name' => $jobPosting->title, 'url' => route('public.jobs.show', $jobPosting)],
            ];
            $breadcrumbSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => collect($breadcrumbItems)->values()->map(fn ($item, $index) => [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $item['name'],
                    'item' => $item['url'],
                ])->all(),
            ];
        @endphp
        <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
    @endpush

    <!-- Breadcrumb -->
    <nav aria-label="{{ __('Brotkrumen') }}" class="mb-5 text-sm text-gray-600 dark:text-gray-400">
        <ol class="flex flex-wrap items-center gap-2">
            <li><a href="{{ route('public.jobs.index') }}" class="text-primary-700 dark:text-primary-300 hover:underline">{{ __('Stellenangebote') }}</a></li>
            @if($city)
                <li aria-hidden="true">/</li>
                <li><a href="{{ route('public.jobs.index', ['location' => $city]) }}" class="text-primary-700 dark:text-primary-300 hover:underline">{{ $city }}</a></li>
            @endif
            <li aria-hidden="true">/</li>
            <li class="text-gray-800 dark:text-gray-200 truncate max-w-[60vw]" aria-current="page">{{ $jobPosting->title }}</li>
        </ol>
    </nav>

    <article itemscope itemtype="https://schema.org/JobPosting">
        <!-- Header card -->
        <header class="{{ $cardClasses }} overflow-hidden mb-6">
            @if($facilityHeader)
                <img src="{{ $facilityHeader }}" alt="{{ __('Bild der Einrichtung') }} {{ $jobPosting->facility->name }}"
                     class="w-full h-36 sm:h-52 object-cover" fetchpriority="high">
            @else
                <div class="w-full h-20 sm:h-28 bg-primary-100 dark:bg-primary-900/40" aria-hidden="true"></div>
            @endif

            <div class="px-5 sm:px-8 pb-6 flex flex-col md:flex-row md:items-end gap-4 md:gap-6">
                <div class="-mt-10 sm:-mt-12 flex-shrink-0">
                    @if($facilityLogo)
                        <img src="{{ $facilityLogo }}" alt="{{ $jobPosting->facility->name }} Logo"
                             class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover bg-white border-4 border-white dark:border-gray-800 shadow-md">
                    @else
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-primary-50 dark:bg-primary-900 border-4 border-white dark:border-gray-800 shadow-md text-primary-800 dark:text-primary-200 flex items-center justify-center text-2xl font-extrabold" aria-hidden="true">
                            @if($initials){{ $initials }}@else&nbsp;@endif
                        </div>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 mb-1" itemprop="hiringOrganization" itemscope itemtype="https://schema.org/Organization">
                        <span itemprop="name">{{ $jobPosting->facility->name }}</span>
                        @if($facilityLogo)
                            <meta itemprop="logo" content="{{ $facilityLogo }}">
                        @endif
                        @if($city)
                            <span aria-hidden="true">·</span> {{ $city }}
                        @endif
                    </p>
                    <h1 class="text-2xl sm:text-4xl font-extrabold leading-tight text-gray-900 dark:text-gray-100 break-words" itemprop="title">{{ $jobPosting->title }}</h1>
                </div>

                @if(!$isExpired)
                    <div class="flex flex-wrap gap-2 md:flex-shrink-0">
                        <a href="{{ route('public.jobs.pdf', $jobPosting) }}"
                           id="pdfDownloadBtn"
                           target="_blank"
                           class="inline-flex items-center gap-2 h-11 px-4 rounded-lg border border-sand-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 font-semibold hover:bg-sand-50 dark:hover:bg-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v11m0 0l-4-4m4 4l4-4M5 20h14"></path>
                            </svg>
                            {{ __('PDF') }}<span class="sr-only"> {{ __('der Stellenanzeige herunterladen') }}</span>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Key facts -->
            <dl class="grid grid-cols-2 lg:grid-cols-5 border-t border-sand-100 dark:border-gray-700 text-sm sm:text-base">
                <div class="px-5 sm:px-8 py-4 border-b lg:border-b-0 border-r border-sand-100 dark:border-gray-700">
                    <dt class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-0.5">{{ __('Beschäftigungsart') }}</dt>
                    <dd class="font-semibold text-gray-900 dark:text-gray-100">
                        {{ $jobPosting->getEmploymentTypeLabel() }}
                        <meta itemprop="employmentType" content="{{ $jobPosting->getSchemaEmploymentType() }}">
                    </dd>
                </div>
                <div class="px-5 sm:px-8 py-4 border-b lg:border-b-0 lg:border-r border-sand-100 dark:border-gray-700">
                    <dt class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-0.5">{{ __('Arbeitsort') }}</dt>
                    <dd class="font-semibold text-gray-900 dark:text-gray-100">{{ $city ?? '–' }}</dd>
                </div>
                <div class="px-5 sm:px-8 py-4 border-b lg:border-b-0 border-r border-sand-100 dark:border-gray-700">
                    <dt class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-0.5">{{ __('Kategorie') }}</dt>
                    <dd class="font-semibold text-gray-900 dark:text-gray-100">{{ $jobPosting->job_category ?: '–' }}</dd>
                </div>
                <div class="px-5 sm:px-8 py-4 border-b lg:border-b-0 lg:border-r border-sand-100 dark:border-gray-700">
                    <dt class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-0.5">{{ __('Veröffentlicht') }}</dt>
                    <dd class="font-semibold text-gray-900 dark:text-gray-100">
                        <time datetime="{{ $jobPosting->published_at->toIso8601String() }}" itemprop="datePosted">{{ $jobPosting->published_at->format('d.m.Y') }}</time>
                    </dd>
                </div>
                <div class="px-5 sm:px-8 py-4 col-span-2 lg:col-span-1">
                    <dt class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-0.5">{{ __('Bewerbung bis') }}</dt>
                    <dd class="font-semibold text-gray-900 dark:text-gray-100">
                        <time datetime="{{ $validThrough->toIso8601String() }}">{{ $validThrough->format('d.m.Y') }}</time>
                        <meta itemprop="validThrough" content="{{ $validThrough->toIso8601String() }}">
                    </dd>
                </div>
            </dl>
        </header>

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

        @if($isExpired)
            <div class="{{ $cardClasses }} p-6 sm:p-8 flex flex-col sm:flex-row gap-4 sm:items-center" role="status">
                <div class="w-12 h-12 rounded-full bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center flex-shrink-0">
                    <svg class="h-6 w-6 text-amber-800 dark:text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ __('Dieses Stellenangebot ist nicht mehr verfügbar') }}</h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Diese Stellenausschreibung wurde beendet oder ist abgelaufen. Bitte schauen Sie sich unsere aktuellen Stellenangebote an.') }}</p>
                </div>
                <a href="{{ route('public.jobs.index') }}" class="inline-flex justify-center items-center px-5 py-3 rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold">
                    {{ __('Zu den aktuellen Stellenangeboten') }}
                </a>
            </div>
        @else

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 items-start">
            <!-- Main Content -->
            <div class="lg:col-span-2 {{ $cardClasses }} p-5 sm:p-8 space-y-8">
                <section>
                    <h2 class="{{ $sectionHeadingClasses }}">{{ __('Stellenbeschreibung') }}</h2>
                    <div class="{{ $textClasses }}" itemprop="description">
                        {!! nl2br(e($jobPosting->description)) !!}
                    </div>
                </section>

                @if($jobPosting->requirements)
                    <section>
                        <h2 class="{{ $sectionHeadingClasses }}">{{ __('Das bringen Sie mit') }}</h2>
                        <div class="{{ $textClasses }}" itemprop="qualifications">
                            {!! nl2br(e($jobPosting->requirements)) !!}
                        </div>
                    </section>
                @endif

                @if($jobPosting->benefits)
                    <section>
                        <h2 class="{{ $sectionHeadingClasses }}">{{ __('Das bieten wir Ihnen') }}</h2>
                        <div class="{{ $textClasses }} bg-sand-50 dark:bg-gray-900/40 rounded-xl p-5" itemprop="jobBenefits">
                            {!! nl2br(e($jobPosting->benefits)) !!}
                        </div>
                    </section>
                @endif
            </div>

            <!-- Sidebar -->
            <aside class="space-y-6" aria-label="{{ __('Bewerbung und Einrichtung') }}">
                <!-- Apply Card -->
                <section class="bg-primary-800 text-white rounded-2xl p-6" aria-labelledby="apply-heading">
                    <h2 id="apply-heading" class="text-xl font-bold mb-2">{{ __('Interesse geweckt?') }}</h2>
                    <p class="text-primary-100 mb-5">{{ __('Bewerben Sie sich direkt bei der Einrichtung.') }}</p>

                    @if($jobPosting->contact_person || $jobPosting->contact_email || $jobPosting->contact_phone)
                        <div class="bg-white/10 rounded-xl p-4 mb-5 space-y-2">
                            @if($jobPosting->contact_person)
                                <p class="font-semibold">{{ $jobPosting->contact_person }}</p>
                            @endif

                            @if($jobPosting->contact_email)
                                <div class="flex items-start gap-2 text-sm">
                                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <button type="button"
                                            class="contact-reveal-btn text-white underline decoration-white/40 hover:decoration-white break-all text-left"
                                            data-contact-email="{{ $obfuscatedEmail }}"
                                            aria-label="E-Mail-Adresse anzeigen">
                                        <span class="contact-display">{{ $displayEmail }}</span>
                                        <span class="contact-hint ml-1 text-xs text-primary-200">({{ __('anzeigen') }})</span>
                                    </button>
                                </div>
                            @endif

                            @if($jobPosting->contact_phone)
                                <div class="flex items-start gap-2 text-sm">
                                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <button type="button"
                                            class="contact-reveal-btn text-white underline decoration-white/40 hover:decoration-white text-left"
                                            data-contact-phone="{{ $obfuscatedPhone }}"
                                            aria-label="Telefonnummer anzeigen">
                                        <span class="contact-display">{{ $displayPhone }}</span>
                                        <span class="contact-hint ml-1 text-xs text-primary-200">({{ __('anzeigen') }})</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-sm text-primary-100 mb-5">{{ __('Kontaktdaten siehe Einrichtung') }}</p>
                    @endif

                    @if($jobPosting->contact_email)
                        <button type="button"
                                data-contact-email="{{ $obfuscatedEmail }}"
                                class="contact-reveal-btn block w-full h-12 bg-white hover:bg-primary-50 text-primary-800 text-center font-bold rounded-xl transition-colors"
                                aria-label="Per E-Mail bewerben">
                            {{ __('Jetzt bewerben') }}
                        </button>
                    @endif

                    <p class="text-xs text-primary-200 text-center mt-4">{{ __('Ausschreibung läuft bis') }} {{ $validThrough->format('d.m.Y') }}</p>
                </section>

                <!-- Facility Info -->
                <section class="{{ $cardClasses }} overflow-hidden" aria-labelledby="facility-heading">
                    @if($jobPosting->facility->address && $jobPosting->facility->address->latitude && $jobPosting->facility->address->longitude)
                        <div id="facilityMap" class="h-48 z-0" role="img" aria-label="Karte mit Standort der Einrichtung"></div>
                    @endif
                    <div class="p-6 space-y-4">
                        <div>
                            <h2 id="facility-heading" class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $jobPosting->facility->name }}</h2>
                            @if($jobPosting->facility->organization)
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $jobPosting->facility->organization->name }}</p>
                            @endif
                        </div>

                        @if($jobPosting->facility->address)
                            <address class="not-italic text-gray-700 dark:text-gray-300">
                                {{ $jobPosting->facility->address->street }} {{ $jobPosting->facility->address->number }}<br>
                                {{ $jobPosting->facility->address->zip_code }} {{ $jobPosting->facility->address->city }}
                            </address>
                        @endif

                        @if($jobPosting->facility->description)
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ Str::limit(strip_tags($jobPosting->facility->description), 200) }}
                            </p>
                        @endif
                    </div>
                </section>

                <!-- Share Buttons -->
                @php
                    $shareUrl = route('public.jobs.show', $jobPosting);
                    $shareText = $jobPosting->title . ' - ' . $jobPosting->facility->name;
                    $shareButtonClasses = 'inline-flex items-center gap-2 h-11 px-3 rounded-lg border border-sand-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-800 dark:text-gray-200 hover:bg-sand-50 dark:hover:bg-gray-700';
                @endphp
                <section class="{{ $cardClasses }} p-6" aria-labelledby="share-heading">
                    <h2 id="share-heading" class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">{{ __('Stelle teilen') }}</h2>

                    {{-- Nur anzeigen, wenn die Stelle veröffentlicht wurde --}}
                    @if($jobPosting->published_at && $jobPosting->published_at->isPast())
                        <div class="flex flex-wrap gap-2">
                            <button type="button" class="{{ $shareButtonClasses }}" data-share="whatsapp" aria-label="Teilen auf WhatsApp">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.52 3.48A11.9 11.9 0 0012.02 0C5.6 0 .6 5 0 11.38 0 13.1.43 14.74 1.24 16.2L0 24l7.94-1.18c1.44.79 3.07 1.2 4.76 1.2 6.42 0 11.42-5 12.02-11.38.1-.6.16-1.2.16-1.78 0-1.02-.08-2.02-.56-2.94zM12.02 20.06c-1.34 0-2.65-.36-3.78-1.04l-.27-.16-4.7.7.9-4.57-.18-.28A7.66 7.66 0 014.12 6.98c0-4.08 3.32-7.4 7.4-7.4 1.98 0 3.84.77 5.24 2.16A7.33 7.33 0 0120.44 12c0 4.07-3.32 7.4-8.42 8.06z"/></svg>
                                WhatsApp
                            </button>
                            <button type="button" class="{{ $shareButtonClasses }}" data-share="linkedin" aria-label="Teilen auf LinkedIn">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M4.98 3.5C4.98 4.88 3.88 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1 4.98 2.12 4.98 3.5zM.5 8.5h4V24h-4zM8.5 8.5h3.84v2.08h.05c.54-1.02 1.86-2.08 3.83-2.08 4.1 0 4.86 2.7 4.86 6.21V24h-4v-7.44c0-1.78-.03-4.08-2.49-4.08-2.49 0-2.87 1.95-2.87 3.96V24h-4z"/></svg>
                                LinkedIn
                            </button>
                            <button type="button" class="{{ $shareButtonClasses }}" data-share="facebook" aria-label="Teilen auf Facebook">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M22 12.07C22 6.48 17.52 2 11.93 2 6.48 2 2 6.48 2 12.07c0 4.99 3.66 9.13 8.44 9.94v-7.03H8.08v-2.9h2.36V9.41c0-2.34 1.39-3.62 3.52-3.62.99 0 2.03.18 2.03.18v2.23h-1.14c-1.12 0-1.47.7-1.47 1.41v1.7h2.5l-.4 2.9h-2.1v7.03C18.34 21.2 22 17.06 22 12.07z"/></svg>
                                Facebook
                            </button>
                            <button type="button" class="{{ $shareButtonClasses }}" data-share="twitter" aria-label="Teilen auf X (Twitter)">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M19.633 7.997c.013.176.013.353.013.53 0 5.393-4.103 11.61-11.61 11.61-2.307 0-4.453-.676-6.253-1.847.329.038.657.051.998.051 1.91 0 3.668-.651 5.073-1.747-1.785-.038-3.294-1.21-3.816-2.829.25.038.501.064.767.064.373 0 .746-.05 1.094-.143-1.869-.374-3.272-2.029-3.272-4.014v-.051c.55.307 1.181.494 1.86.517-1.104-.736-1.828-1.989-1.828-3.409 0-.747.201-1.445.552-2.048 2.031 2.493 5.073 4.138 8.494 4.309-.064-.302-.101-.615-.101-.936 0-2.268 1.853-4.121 4.121-4.121 1.186 0 2.258.5 3.011 1.302.936-.176 1.8-.526 2.586-.998-.307.96-.96 1.766-1.816 2.277.829-.089 1.62-.32 2.356-.649-.55.821-1.248 1.542-2.049 2.115z"/></svg>
                                X
                            </button>
                            <button type="button" id="copyLinkBtn" class="{{ $shareButtonClasses }}" aria-label="Link kopieren">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14a4 4 0 005.66 0l3-3a4 4 0 00-5.66-5.66l-1 1M14 10a4 4 0 00-5.66 0l-3 3a4 4 0 005.66 5.66l1-1"/></svg>
                                <span id="copyLinkText">{{ __('Link kopieren') }}</span>
                            </button>
                        </div>
                    @else
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Diese Stelle ist noch nicht veröffentlicht und kann nicht geteilt werden.') }}</p>
                    @endif
                </section>
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
    @endif
    </article>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const trackingUrl = @json(route('public.jobs.track', $jobPosting));
                const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

                // Helper function to track interactions
                function trackInteraction(type) {
                    if (!csrfToken) {
                        console.warn('CSRF token not found');
                        return;
                    }

                    fetch(trackingUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ type: type })
                    }).catch(err => {
                        // Silently fail - don't interrupt user experience
                        console.debug('Tracking failed:', err);
                    });
                }

                // Track page view
                trackInteraction('view');

                // Prepare email subject
                const jobTitle = @json($jobPosting->title);
                const facilityName = @json($jobPosting->facility->name);
                const emailSubject = encodeURIComponent('Bewerbung: ' + jobTitle + ' - ' + facilityName);

                // Bot protection: Reveal contact info on user interaction
                document.querySelectorAll('.contact-reveal-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const email = this.getAttribute('data-contact-email');
                        const phone = this.getAttribute('data-contact-phone');
                        const displaySpan = this.querySelector('.contact-display');
                        const hintSpan = this.querySelector('.contact-hint');

                        if (email) {
                            // Decode email from base64
                            const decodedEmail = atob(email);

                            // Check if this is the main "Jetzt bewerben" button
                            if (this.classList.contains('block')) {
                                // Track apply button click
                                trackInteraction('apply_click');

                                // Main "Jetzt bewerben" button - open mail client directly with subject
                                window.location.href = 'mailto:' + decodedEmail + '?subject=' + emailSubject;
                            } else {
                                // Track email reveal
                                trackInteraction('email_reveal');

                                // Sidebar email display - show email and convert to link
                                displaySpan.textContent = decodedEmail;
                                if (hintSpan) hintSpan.remove();

                                // Convert button to link
                                const link = document.createElement('a');
                                link.href = 'mailto:' + decodedEmail + '?subject=' + emailSubject;
                                link.className = this.className.replace('contact-reveal-btn', '');
                                link.textContent = decodedEmail;
                                link.setAttribute('aria-label', 'E-Mail an ' + decodedEmail + ' senden');
                                this.replaceWith(link);
                            }
                        }

                        if (phone) {
                            // Track phone reveal
                            trackInteraction('phone_reveal');

                            // Decode phone from base64
                            const decodedPhone = atob(phone);

                            // For phone, open tel: link directly
                            if (this.classList.contains('block')) {
                                window.location.href = 'tel:' + decodedPhone;
                            } else {
                                // Sidebar phone display - show phone and convert to link
                                displaySpan.textContent = decodedPhone;
                                if (hintSpan) hintSpan.remove();

                                const link = document.createElement('a');
                                link.href = 'tel:' + decodedPhone;
                                link.className = this.className.replace('contact-reveal-btn', '');
                                link.textContent = decodedPhone;
                                link.setAttribute('aria-label', 'Anrufen unter ' + decodedPhone);
                                this.replaceWith(link);
                            }
                        }
                    }, { once: true }); // Only fire once per button
                });

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

                const pdfBtn = document.getElementById('pdfDownloadBtn');
                if (pdfBtn) {
                    pdfBtn.addEventListener('click', function() {
                        trackInteraction('download');
                    });
                }

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
