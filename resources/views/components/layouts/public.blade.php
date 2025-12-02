<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Stellenangebote' }} - {{ config('app.name') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/Stellenportal-Logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/Stellenportal-Logo.png') }}">

    <!-- SEO Meta Tags -->
    <meta name="description" content="{{ $metaDescription ?? 'AStellenportal für Schulen in Sachsen: Jobs für Lehrkräfte, Sozialarbeit, Pädagogik und Verwaltung an evangelischen und freien Schulträgern in Dresden, Leipzig und Chemnitz. Jetzt bewerben oder ausschreiben!'}}">
    <meta name="author" content="{{ config('app.name') }}">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $ogTitle ?? $title ?? 'Stellenangebote' }} - {{ config('app.name') }}">
    <meta property="og:description" content="{{ $ogDescription ?? $metaDescription ?? 'Aktuelle Stellenangebote für Lehrkräfte und pädagogisches Personal.' }}">
    <meta property="og:image" content="{{ $ogImage ?? asset('img/Stellenportal-Logo.png') }}">
    <meta property="og:locale" content="de_DE">
    <meta property="og:site_name" content="{{ config('app.name') }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ $title ?? 'Stellenangebote' }} - {{ config('app.name') }}">
    <meta name="twitter:description" content="{{ $metaDescription ?? 'Aktuelle Stellenangebote für Lehrkräfte und pädagogisches Personal.' }}">
    <meta name="twitter:image" content="{{ $ogImage ?? asset('img/Stellenportal-Logo.png') }}">

    @stack('structured-data')

    {!! RecaptchaV3::initJs() !!}

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    @stack('js')
    <script>
        window.setAppearance = function(appearance) {
            let setDark = () => document.documentElement.classList.add('dark')
            let setLight = () => document.documentElement.classList.remove('dark')
            let setButtons = (appearance) => {
                document.querySelectorAll('button[onclick^="setAppearance"]').forEach((button) => {
                    button.setAttribute('aria-pressed', String(appearance === button.value))
                })
            }
            if (appearance === 'system') {
                let media = window.matchMedia('(prefers-color-scheme: dark)')
                window.localStorage.removeItem('appearance')
                media.matches ? setDark() : setLight()
            } else if (appearance === 'dark') {
                window.localStorage.setItem('appearance', 'dark')
                setDark()
            } else if (appearance === 'light') {
                window.localStorage.setItem('appearance', 'light')
                setLight()
            }
            if (document.readyState === 'complete') {
                setButtons(appearance)
            } else {
                document.addEventListener("DOMContentLoaded", () => setButtons(appearance))
            }
        }
        window.setAppearance(window.localStorage.getItem('appearance') || 'system')
    </script>

    <!-- Leaflet.js for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
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
    </style>
</head>

<body class="antialiased">
    <!-- Navigation -->
    <nav class="fixed w-full top-0 z-50 bg-white shadow-md">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="flex items-center">
                        <img src="{{ asset('img/Stellenportal-Logo.png') }}" alt="{{ config('app.name') }} Logo" class="h-12">
                        <span class="ml-3 text-xl sm:text-2xl font-bold text-gray-800 hidden sm:inline">{{ config('app.name') }}</span>
                    </a>
                </div>
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
                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center gap-4" x-data="{ openDropdown: null }">
                    <a href="{{ route('public.jobs.index') }}" class="px-4 py-2 text-gray-700 hover:text-blue-600 font-medium transition-colors" aria-label="Stellenangebote">
                        Stellenangebote
                    </a>
                    <a href="{{ route('public.pricing') }}" class="px-4 py-2 text-gray-700 hover:text-blue-600 font-medium transition-colors" aria-label="Preise">
                        Preise
                    </a>



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
        <div class="lg:hidden hidden" id="mobile-menu" x-data="{ openMobileDropdown: null }">
            <div class="px-2 pt-2 pb-3 space-y-1 bg-white border-t border-gray-200">
                <a href="{{ route('public.jobs.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition-colors" aria-label="Stellenangebote">
                    Stellenangebote
                </a>
                <a href="{{ route('public.pricing') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition-colors" aria-label="Preise">
                    Preise
                </a>

                @if(isset($headerMenu) && $headerMenu->count() > 0)
                    @foreach($headerMenu as $menuItem)
                        @if($menuItem->children->count() > 0)
                            <!-- Mobile Dropdown/Accordion Menu Item -->
                            <div>
                                <button @click="openMobileDropdown = openMobileDropdown === {{ $menuItem->id }} ? null : {{ $menuItem->id }}"
                                        class="w-full flex items-center justify-between px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition-colors">
                                    <span>{{ $menuItem->label }}</span>
                                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': openMobileDropdown === {{ $menuItem->id }} }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="openMobileDropdown === {{ $menuItem->id }}"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 -translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="pl-6 space-y-1"
                                     style="display: none;">
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
                                                   class="block px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-gray-50 transition-colors">
                                                    {{ $child->label }}
                                                </a>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <!-- Single Mobile Menu Item -->
                            @php
                                $menuUrl = $menuItem->url;
                                if ($menuItem->page_id && $menuItem->page) {
                                    $menuUrl = route('pages.show', $menuItem->page->slug);
                                }
                            @endphp
                            @if($menuUrl)
                                <a href="{{ $menuUrl }}"
                                   target="{{ $menuItem->target }}"
                                   class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition-colors">
                                    {{ $menuItem->label }}
                                </a>
                            @endif
                        @endif
                    @endforeach
                @endif

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

    <main class="min-h-screen pt-20" style="background-image: var(--bg-image, url('{{ asset('img/HG-blau-soft.jpg') }}')); background-size: cover; background-position: center; background-attachment: fixed;">
        <style>
            .dark main {
                --bg-image: url('{{ asset('img/bg-dark.png') }}');
            }
        </style>
        <div class="container mx-auto px-4 py-8">
            {{ $slot }}
        </div>
    </main>

    <x-layouts.app.footer />

    @stack('scripts')

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

                    // Toggle aria-expanded
                    mobileMenuButton.setAttribute('aria-expanded', !isExpanded);

                    // Toggle icons
                    if (menuIconClosed && menuIconOpen) {
                        menuIconClosed.classList.toggle('hidden');
                        menuIconClosed.classList.toggle('block');
                        menuIconOpen.classList.toggle('hidden');
                        menuIconOpen.classList.toggle('block');
                    }
                });

                // Close mobile menu when clicking a link
                const mobileMenuLinks = mobileMenu.querySelectorAll('a');
                mobileMenuLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        mobileMenu.classList.add('hidden');
                        mobileMenuButton.setAttribute('aria-expanded', 'false');
                        if (menuIconClosed && menuIconOpen) {
                            menuIconClosed.classList.remove('hidden');
                            menuIconClosed.classList.add('block');
                            menuIconOpen.classList.add('hidden');
                            menuIconOpen.classList.remove('block');
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>
