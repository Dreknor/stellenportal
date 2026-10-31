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
    <meta name="description" content="{{ $metaDescription ?? 'Stellenportal für Schulen in Sachsen: Jobs für Lehrkräfte, Sozialarbeit, Pädagogik und Verwaltung an evangelischen und freien Schulträgern in Dresden, Leipzig und Chemnitz. Jetzt bewerben oder ausschreiben!'}}">
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

    @stack('head')
    @stack('structured-data')

    {!! RecaptchaV3::initJs() !!}

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- FontAwesome (local) -->
    <link rel="stylesheet" href="{{ asset('fonts/fontawesome/css/fontawesome-basic.css') }}">

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

<body class="antialiased bg-sand-50 text-gray-800 dark:bg-gray-900 dark:text-gray-100">
    <x-layouts.public.nav />

    <main id="main-content" class="min-h-screen">
        {{ $hero ?? '' }}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{ $slot }}
        </div>
    </main>

    <x-layouts.app.footer />

    @stack('scripts')

</body>
</html>
