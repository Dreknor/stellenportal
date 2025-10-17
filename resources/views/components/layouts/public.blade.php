<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Stellenangebote' }} - {{ config('app.name') }}</title>

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
    <nav class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="{{ route('public.jobs.index') }}" class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                    {{ config('app.name') }}
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                        {{ __('Zum Dashboard') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                        {{ __('Anmelden') }}
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8">
        {{ $slot }}
    </main>

    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-12">
        <div class="container mx-auto px-4 py-6 text-center text-gray-600 dark:text-gray-400">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('Alle Rechte vorbehalten.') }}</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
