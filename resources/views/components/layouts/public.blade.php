<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Stellenangebote' }} - {{ config('app.name') }}</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="{{ $metaDescription ?? 'Aktuelle Stellenangebote für Lehrkräfte, Schulbegleitung und pädagogisches Personal an evangelischen Schulen und Einrichtungen in Sachsen.' }}">
    <meta name="keywords" content="Stellenangebote, Lehrkräfte, evangelische Schulen, Jobs Sachsen, Pädagogik, Schulbegleitung, Lehrer Jobs">
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
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <nav class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700" role="navigation" aria-label="Hauptnavigation">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="{{ route('public.jobs.index') }}" class="flex items-center space-x-3" aria-label="Zur Startseite">
                    <img src="{{ asset('img/Stellenportal-Logo.png') }}" alt="{{ config('app.name') }} Logo" class="h-10">
                    <span class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                        {{ config('app.name') }}
                    </span>
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:underline" aria-label="Zum Dashboard">
                        {{ __('Zum Dashboard') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-blue-600 dark:text-blue-400 hover:underline" aria-label="Anmelden">
                        {{ __('Anmelden') }}
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="min-h-screen" style="background-image: var(--bg-image, url('{{ asset('img/HG-blau-soft.jpg') }}')); background-size: cover; background-position: center; background-attachment: fixed;">
        <style>
            .dark main {
                --bg-image: url('{{ asset('img/bg-dark.png') }}');
            }
        </style>
        <div class="container mx-auto px-4 py-8">
            {{ $slot }}
        </div>
    </main>

    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-12" role="contentinfo">
        <div class="container mx-auto px-4 py-6 text-center text-gray-600 dark:text-gray-400">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('Alle Rechte vorbehalten.') }}</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
