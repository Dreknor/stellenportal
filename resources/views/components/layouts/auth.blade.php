<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Anmelden' }} - {{ config('app.name') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/Stellenportal-Logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/Stellenportal-Logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {!! RecaptchaV3::initJs() !!}

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .hero-bg {
            background: linear-gradient(rgb(0 50 100 / 10%), rgb(0 50 100 / 55%)), url('/img/header_04.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
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
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('img/Stellenportal-Logo.png') }}" alt="Stellenportal Logo" class="h-12">
                    <span class="ml-3 text-2xl font-bold text-gray-800">{{ config('app.name') }}</span>

                </a>

                <div class="flex items-center gap-4">
                    <a href="{{ route('home') }}"
                        class="px-6 py-2.5 text-gray-700 hover:text-blue-600 font-medium transition-colors">
                        Startseite
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content with Hero Background -->
    <div class="hero-bg min-h-screen flex items-center justify-center pt-20">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="w-full max-w-md mx-auto">
                {{ $slot }}
            </div>
        </div>
    </div>

    <!-- Footer -->
    <x-layouts.app.footer />
</body>

</html>
