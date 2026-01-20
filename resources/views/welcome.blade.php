<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - **Lehrkräfte & Jobs Schulen Sachsen: Freie Trägerschaft</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/Stellenportal-Logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/Stellenportal-Logo.png') }}">

    <!-- SEO Meta Tags -->
    <meta name="description" content="Das Stellenportal für evangelische Schulen und Einrichtungen in Sachsen. Finden Sie Lehrkräfte, Schulbegleiterinnen, Referendarinnen und Praktikanten für das Hauptfach: Mensch.">
    <meta name="keywords" content="Stellenportal, evangelische Schulen, Lehrkräfte, Schulbegleitung, Referendariat, Praktikum, Sachsen, Stellenangebote, Lehrer Jobs, Pädagogik">
    <meta name="author" content="{{ config('app.name') }}">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="googlebot" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="Hauptfach: Mensch - Stellenportal für evangelische Schulen">
    <meta property="og:description" content="Das Stellenportal bringt evangelische Schulen und Einrichtungen mit passenden Mitarbeitenden zusammen. Jetzt kostenlos registrieren!">
    <meta property="og:image" content="{{ asset('img/header_04.jpg') }}">
    <meta property="og:locale" content="de_DE">
    <meta property="og:site_name" content="{{ config('app.name') }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="Hauptfach: Mensch - Stellenportal für evangelische Schulen">
    <meta name="twitter:description" content="Das Stellenportal bringt evangelische Schulen und Einrichtungen mit passenden Mitarbeitenden zusammen.">
    <meta name="twitter:image" content="{{ asset('img/header_04.jpg') }}">

    <!-- Structured Data / Schema.org -->
    @php
        $websiteSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => config('app.name'),
            'url' => url('/'),
            'description' => 'Stellenportal für evangelische Schulen und Einrichtungen in Sachsen',
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => route('public.jobs.index') . '?search={search_term_string}',
                'query-input' => 'required name=search_term_string'
            ]
        ];

        $organizationSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => config('app.name'),
            'url' => url('/'),
            'logo' => asset('img/Stellenportal-Logo.png'),
            'description' => 'Professionelle Plattform für Stellenanzeigen im Bildungsbereich',
            'address' => [
                '@type' => 'PostalAddress',
                'addressCountry' => 'DE',
                'addressRegion' => 'Sachsen'
            ]
        ];
    @endphp

    <script type="application/ld+json">{!! json_encode($websiteSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    <script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

    {!! RecaptchaV3::initJs() !!}


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .hero-bg {
            background: linear-gradient(rgb(0 50 100 / 10%), rgb(0 50 100 / 55%)), url('/img/header_01.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4);
        }

        .feature-section {
            background-image: url('/img/HG-blau-soft.jpg');
            background-size: cover;
            background-position: center;
            position: relative;
        }


    </style>
</head>

<body class="antialiased">
    <!-- Navigation -->
    <nav class="fixed w-full top-0 z-50 bg-white shadow-md">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center">
                    <img src="{{ asset('img/Stellenportal-Logo.png') }}" alt="{{ config('app.name') }} Logo" class="h-12">
                    <span class="ml-3 text-xl sm:text-2xl font-bold text-gray-800 hidden sm:inline">{{ config('app.name') }}</span>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center gap-4" x-data="{ openDropdown: null }">
                    <a href="{{ route('public.jobs.index') }}" class="px-4 py-2 text-gray-700 hover:text-blue-600 font-medium transition-colors" aria-label="Stellenangebote">
                        Stellenangebote
                    </a>
                    <a href="{{ route('public.pricing') }}" class="px-4 py-2 text-gray-700 hover:text-blue-600 font-medium transition-colors" aria-label="Preise">
                        Preise
                    </a>

                    @if(isset($headerMenu) && $headerMenu->count() > 0)
                        @foreach($headerMenu as $menuItem)
                            @if($menuItem->children->count() > 0)
                                <!-- Dropdown Menu Item -->
                                <div class="relative" @click.away="openDropdown = null">
                                    <button @click="openDropdown = openDropdown === {{ $menuItem->id }} ? null : {{ $menuItem->id }}"
                                            class="px-4 py-2 text-gray-700 hover:text-blue-600 font-medium transition-colors flex items-center gap-1"
                                            :aria-expanded="openDropdown === {{ $menuItem->id }}">
                                        {{ $menuItem->label }}
                                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openDropdown === {{ $menuItem->id }} }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div x-show="openDropdown === {{ $menuItem->id }}"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                                         style="display: none;">
                                        <div class="py-1">
                                            @foreach($menuItem->children as $child)
                                                @if($child->is_active)
                                                    @php
                                                        $childUrl = $child->url;
                                                        if ($child->page_id && $child->page) {
                                                            $childUrl = route('pages.show', $child->page->slug);
                                                        }
                                                    @endphp
                                                    @if($childUrl)
                                                        <a href="{{ $childUrl }}"
                                                           target="{{ $child->target }}"
                                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600 transition-colors">
                                                            {{ $child->label }}
                                                        </a>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Single Menu Item -->
                                @php
                                    $menuUrl = $menuItem->url;
                                    if ($menuItem->page_id && $menuItem->page) {
                                        $menuUrl = route('pages.show', $menuItem->page->slug);
                                    }
                                @endphp
                                @if($menuUrl)
                                    <a href="{{ $menuUrl }}"
                                       target="{{ $menuItem->target }}"
                                       class="px-4 py-2 text-gray-700 hover:text-blue-600 font-medium transition-colors">
                                        {{ $menuItem->label }}
                                    </a>
                                @endif
                            @endif
                        @endforeach
                    @endif

                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-6 py-2.5 text-gray-700 hover:text-blue-600 font-medium transition-colors" aria-label="Zum Dashboard">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-6 py-2.5 text-gray-700 hover:text-blue-600 font-medium transition-colors" aria-label="Anmelden">
                            Anmelden
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary px-6 py-2.5 text-white rounded-lg font-medium" aria-label="Jetzt registrieren">
                                Registrieren
                            </a>
                        @endif
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <button type="button" class="lg:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-600" aria-controls="mobile-menu" aria-expanded="false" id="mobile-menu-button">
                    <span class="sr-only">Hauptmenü öffnen</span>
                    <!-- Icon when menu is closed -->
                    <svg class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" id="menu-icon-closed">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <!-- Icon when menu is open -->
                    <svg class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" id="menu-icon-open">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="lg:hidden hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 bg-white border-t border-gray-200">
                <a href="{{ route('public.jobs.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition-colors" aria-label="Stellenangebote">
                    Stellenangebote
                </a>
                <a href="{{ route('public.pricing') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition-colors" aria-label="Preise">
                    Preise
                </a>
                @auth
                    <a href="{{ url('/dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition-colors" aria-label="Zum Dashboard">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition-colors" aria-label="Anmelden">
                        Anmelden
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="block mx-3 my-2 px-6 py-2.5 bg-blue-600 text-white text-center rounded-lg font-medium hover:bg-blue-700 transition-colors" aria-label="Jetzt registrieren">
                            Registrieren
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-bg min-h-screen flex items-center justify-center text-white pt-20">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                    Willkommen beim Stellenportal
                </h1>
                <h2 class="text-xl md:text-2xl mb-8 text-blue-100">
                    Fachkräfte finden für das Hauptfach: Mensch
                </h2>
                <p class="text-lg mb-12 text-blue-50 max-w-2xl mx-auto">
                    Ob Lehrkräfte, Schulbegleiterinnen, Referendarinnen oder Praktikanten – das Stellenportal, ursprünglich entwickelt für evangelische Schulen, bringt jede Einrichtung mit passenden Mitarbeitenden zusammen.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @guest
                    <a href="{{ route('public.jobs.index') }}" class="btn-primary px-8 py-4 text-white rounded-lg font-semibold text-lg inline-block">
                        Zu den Stellenangeboten
                    </a>
                    @else
                    <a href="{{ url('/dashboard') }}" class="btn-primary px-8 py-4 text-white rounded-lg font-semibold text-lg inline-block">
                        Zum Dashboard
                    </a>
                    @endguest
                </div>
            </div>
        </div>
    </section>

    <!-- Hauptfach Mensch Video Section -->
    <section class="py-20 bg-white" aria-labelledby="hauptfach-mensch-heading">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-12">
                    <h2 id="hauptfach-mensch-heading" class="text-4xl font-bold text-gray-900 mb-6">Hauptfach: Mensch</h2>
                    <p class="text-xl text-gray-600 mb-8">
                        Erfahren Sie mehr über die namensgebende Aktion "Hauptfach Mensch" und was evangelische Schulen in Sachsen besonders macht.
                    </p>
                </div>

                <!-- Video Embed -->
                <div class="mb-8">
                    <div class="relative w-full" style="padding-bottom: 56.25%;">
                        <iframe
                            class="absolute top-0 left-0 w-full h-full rounded-lg shadow-lg border-0"
                            src="https://www.youtube-nocookie.com/embed/SjijPYrIxVs?si=_VkGEmcOOuvv6vxw"
                            title="Hauptfach Mensch - YouTube Video"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin"
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>

                <!-- Link zur Aktion -->
                <div class="text-center">
                    <p class="text-gray-600 mb-4">
                        Mehr Informationen zur Aktion "Hauptfach Mensch":
                    </p>
                    <a href="https://www.ev-schulen-sachsen.de/hauptfach-mensch-1"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                        Zur Aktion "Hauptfach Mensch"
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-gray-50 feature-section" >
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Lehrer*in sein – und freie Arbeitsplatzwahl? Das gibt's nur bei den freien Schulen. Auch aktuell suchen wir Lehrkräfte, Pädagogen, Mitarbeiter, Unterstützer – und das ist nur ein Vorteil, der bei uns auf Sie wartet …
                </p>
            </div>
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Ihre Vorteile an unseren Schulen und Einrichtungen</h2>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <article class="feature-card bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="h-48 bg-cover bg-center" style="background-image: url('{{ asset('img/bg_schueler.jpg') }}');" aria-hidden="true"></div>
                    <div class="p-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">„Hauptfach Mensch"</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Schwerpunkt evangelischer Schulen ist es, individuelleres Lernen zu ermöglichen. Deshalb sind wir bemüht, verschiedenen Lerngeschwindigkeiten mehr Rechnung zu tragen. Unsere Lehrer*innen genießen aus diesem Grund mehr Freiheiten, Schule und Unterricht selbst zu gestalten. Eingeschlossen ist dabei explizit Freiraum für eigene Projekte. Im Blickpunkt steht jederzeit, Kinder und Jugendliche zu verantwortungsvollen Individuen zu befähigen, die an Ihre Mitmenschen und die Umwelt denken. Dabei dürfen sie Fehler machen und sollen genau darin erkennen, dass eigene Unzulänglichkeiten zum Leben dazu gehören und deshalb ganz normal sind.
                        </p>
                    </div>
                </article>

                <!-- Feature 2 -->
                <article class="feature-card bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="h-48 bg-cover bg-center" style="background-image: url('{{ asset('img/bg_vorbereitungsdienst_2.jpg') }}');" aria-hidden="true"></div>
                    <div class="p-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Freie Wahl des Arbeitsplatzes</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Unsere 89 allgemeinbildenden und beruflichen Schulen sind über den gesamten Freistaat Sachsen verteilt. Sowohl hinsichtlich der Schularten als auch bezüglich Ihrer pädagogischen Ausrichtungen und ihrer Lage im städtischen oder ländlichen Bereich sind die einzelnen Einrichtungen völlig verschieden. Bei uns bewerben Sie sich direkt am jeweiligen Standort und entscheiden damit selbst, wo Sie arbeiten. Auf Wunsch sind auch Wechsel zwischen verschiedenen Schulen möglich.
                        </p>
                    </div>
                </article>

                <!-- Feature 3 -->
                <article class="feature-card bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="h-48 bg-cover bg-center" style="background-image: url('{{ asset('img/bg_studenten.jpg') }}');" aria-hidden="true"></div>
                    <div class="p-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Flexible Bedingungen</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Je nach Wunsch und Bedarf können unsere Schulträger*innen und Schulleiter*innen Sie auch in Teil- oder Vollzeit beschäftigen. Mancherorts wird sogar ein unterrichtsfreier Tag zur Vorbereitung von Schulstunden gewährt. Anders als im Staatsdienst sind Träger freier Schulen in der Lage, Arbeitsverträge flexibler gestalten zu können. Damit können beispielsweise Pendelstrecken pro Woche reduziert werden. Vieles ist denkbar, bewerben Sie sich einfach bei uns.
                        </p>
                    </div>
                </article>
            </div>
        </div>
    </section>


    <!-- Latest Job Postings Section -->
    @if($latestJobs->isNotEmpty())
        <section class="py-20 bg-gray-50 feature-section" aria-labelledby="latest-jobs-heading">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 id="latest-jobs-heading" class="text-4xl font-bold text-gray-900 mb-4">Aktuelle Stellenangebote</h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                        Entdecken Sie unsere neuesten Stellenangebote und finden Sie Ihre passende Position
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                    @foreach($latestJobs as $job)
                        <article class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300" itemscope itemtype="https://schema.org/JobPosting">
                            <div class="p-6">
                                <!-- Job Header -->
                                <div class="mb-4">
                                    <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2" itemprop="title">
                                        {{ $job->title }}
                                    </h3>
                                    <div class="flex items-center text-sm text-gray-600 mb-2" itemprop="hiringOrganization" itemscope itemtype="https://schema.org/Organization">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <span class="font-medium" itemprop="name">{{ $job->facility->name }}</span>
                                    </div>
                                    @if($job->facility->address)
                                        <div class="flex items-center text-sm text-gray-600" itemprop="jobLocation" itemscope itemtype="https://schema.org/Place">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            <span itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
                                                <span itemprop="addressLocality">{{ $job->facility->address->city }}</span>
                                                <meta itemprop="streetAddress" content="{{ $job->facility->address->street }} {{ $job->facility->address->number }}">
                                                <meta itemprop="postalCode" content="{{ $job->facility->address->zip_code }}">
                                                <meta itemprop="addressCountry" content="DE">
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Job Type Badge -->
                                <div class="mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800" itemprop="employmentType">
                                @switch($job->employment_type)
                                    @case('full_time')
                                        Vollzeit
                                        @break
                                    @case('part_time')
                                        Teilzeit
                                        @break
                                    @case('mini_job')
                                        Minijob
                                        @break
                                    @case('internship')
                                        Praktikum
                                        @break
                                    @case('apprenticeship')
                                        Ausbildung
                                        @break
                                    @case('volunteer')
                                        Ehrenamt
                                        @break
                                    @default
                                        {{ $job->employment_type }}
                                @endswitch
                            </span>
                                    @if($job->job_category)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 ml-2">
                                {{ $job->job_category }}
                            </span>
                                    @endif
                                </div>

                                <!-- Job Description Preview -->
                                <p class="text-gray-600 text-sm mb-4 line-clamp-3" itemprop="description">
                                    {{ Str::limit(strip_tags($job->description), 120) }}
                                </p>

                                <!-- Published Date -->
                                <div class="text-xs text-gray-500 mb-4">
                                    <time datetime="{{ $job->published_at->toIso8601String() }}" itemprop="datePosted">
                                        Veröffentlicht am {{ $job->published_at->format('d.m.Y') }}
                                    </time>
                                </div>

                                <!-- View Details Button -->
                                <a href="{{ route('public.jobs.show', $job) }}" class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors" aria-label="Details zur Stelle {{ $job->title }} ansehen">
                                    Details ansehen
                                </a>

                                <meta itemprop="validThrough" content="{{ $job->published_at->addMonths(3)->toIso8601String() }}">
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- View All Jobs Button -->
                <div class="text-center">
                    <a href="{{ route('public.jobs.index') }}" class="inline-flex items-center px-8 py-4 bg-blue-600 text-white rounded-lg font-semibold text-lg hover:bg-blue-700 transition-colors" aria-label="Alle Stellenangebote ansehen">
                        Alle Stellenangebote ansehen
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </section>
    @endif

    <!-- How it Works Section -->
    <section class="py-20 bg-white" aria-labelledby="how-it-works-heading">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 id="how-it-works-heading" class="text-4xl font-bold text-gray-900 mb-4">So funktioniert's als Schulträger</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    In zwei einfachen Schritten zur erfolgreichen Stellenanzeige
                </p>
            </div>

            <div class="max-w-4xl mx-auto">
                <div class="space-y-12">
                    <!-- Step 1 -->
                    <div class="flex flex-col md:flex-row items-center gap-8">
                        <div class="flex-shrink-0">
                            <div class="w-20 h-20 bg-blue-600 text-white rounded-full flex items-center justify-center text-3xl font-bold">
                                1
                            </div>
                        </div>
                        <div class="flex-grow text-center md:text-left">
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">Organisation & Einrichtung anlegen</h3>
                            <p class="text-gray-600 text-lg">
                                Erstellen Sie Ihre Trägerorganisation und fügen Sie Ihre Einrichtungen mit allen relevanten Informationen hinzu.
                            </p>
                        </div>
                    </div>
                    <!-- Step 2 -->
                    <div class="flex flex-col md:flex-row items-center gap-8">
                        <div class="flex-shrink-0">
                            <div class="w-20 h-20 bg-blue-600 text-white rounded-full flex items-center justify-center text-3xl font-bold">
                                2
                            </div>
                        </div>
                        <div class="flex-grow text-center md:text-left">
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">Stellenanzeigen erstellen & veröffentlichen</h3>
                            <p class="text-gray-600 text-lg">
                                Erstellen Sie professionelle Stellenanzeigen und veröffentlichen Sie diese für 3 Monate. Verwalten Sie den Status jederzeit.
                            </p>
                        </div>
                    </div>
                    <div class="text-center mt-8">
                        @guest
                            <a href="{{ route('register') }}" class="btn-primary px-8 py-4 text-white rounded-lg font-semibold text-lg inline-block">
                                Jetzt kostenlos starten
                            </a>
                            <a href="{{ route('login') }}" class="px-8 py-4 bg-white text-blue-900 rounded-lg font-semibold text-lg hover:bg-blue-50 transition-colors inline-block">
                                Anmelden
                            </a>
                        @else
                            <a href="{{ url('/dashboard') }}" class="btn-primary px-8 py-4 text-white rounded-lg font-semibold text-lg inline-block">
                                Zum Dashboard
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- CTA Section -->
    <section class="hero-bg py-20 text-white" aria-labelledby="cta-heading">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="max-w-3xl mx-auto">
                <h2 id="cta-heading" class="text-4xl font-bold mb-6">Bereit loszulegen?</h2>
                <p class="text-xl mb-8 text-blue-100">
                    Registrieren Sie sich jetzt kostenlos und starten Sie mit der Verwaltung Ihrer Stellenanzeigen.
                </p>
                @guest
                <a href="{{ route('register') }}" class="btn-primary px-10 py-4 text-white rounded-lg font-semibold text-lg inline-block">
                    Kostenlos registrieren
                </a>
                @else
                <a href="{{ url('/dashboard') }}" class="btn-primary px-10 py-4 text-white rounded-lg font-semibold text-lg inline-block">
                    Zum Dashboard
                </a>
                @endguest
            </div>
        </div>
    </section>

    <!-- Footer -->
    <x-layouts.app.footer />

    <!-- Mobile Menu Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const menuIconClosed = document.getElementById('menu-icon-closed');
            const menuIconOpen = document.getElementById('menu-icon-open');

            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    const isExpanded = mobileMenuButton.getAttribute('aria-expanded') === 'true';

                    // Toggle menu visibility
                    mobileMenu.classList.toggle('hidden');

                    // Toggle icons
                    menuIconClosed.classList.toggle('hidden');
                    menuIconOpen.classList.toggle('hidden');

                    // Update aria-expanded
                    mobileMenuButton.setAttribute('aria-expanded', !isExpanded);
                });

                // Close menu when clicking on a link
                const mobileMenuLinks = mobileMenu.querySelectorAll('a');
                mobileMenuLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        mobileMenu.classList.add('hidden');
                        menuIconClosed.classList.remove('hidden');
                        menuIconOpen.classList.add('hidden');
                        mobileMenuButton.setAttribute('aria-expanded', 'false');
                    });
                });
            }
        });
    </script>
</body>

</html>
