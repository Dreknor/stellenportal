<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Preise & Guthaben-Pakete - {{ config('app.name') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/Stellenportal-Logo.png') }}">

    <!-- SEO -->
    <meta name="description" content="Transparente Preise für Stellenanzeigen. Wählen Sie das passende Guthaben-Paket für Ihre Einrichtung.">
    <meta name="robots" content="index, follow">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-3">
                    <img src="{{ asset('img/Stellenportal-Logo.png') }}" alt="Logo" class="h-10 w-10">
                    <span class="text-xl font-bold text-gray-900">{{ config('app.name') }}</span>
                </a>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('public.jobs.index') }}" class="text-gray-600 hover:text-gray-900 font-medium">Stellenangebote</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 font-medium">Anmelden</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition">Registrieren</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Transparente Preise für Stellenanzeigen</h1>
            <p class="text-xl md:text-2xl text-blue-100 max-w-3xl mx-auto mb-8">
                Wählen Sie das passende Guthaben-Paket für Ihre Organisation oder Einrichtung
            </p>
            <div class="inline-flex items-center bg-blue-700/50 rounded-lg px-6 py-3 backdrop-blur-sm">
                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-lg">Keine versteckten Kosten • Flexible Pakete • Einfache Verwaltung</span>
            </div>
        </div>
    </section>

    <!-- Pricing Cards -->
    <section class="py-16 -mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Standard Packages -->
            @if($standardPackages->isNotEmpty())
                <div class="mb-16">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold text-gray-900 mb-3">Standard-Pakete</h2>
                        <p class="text-lg text-gray-600">Für alle Organisationen und Einrichtungen</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($standardPackages as $index => $package)
                            <div class="relative bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-shadow duration-300 {{ $index === 1 ? 'ring-4 ring-blue-500 transform lg:scale-105' : '' }}">
                                @if($index === 1)
                                    <div class="absolute top-0 right-0 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-2 rounded-bl-2xl font-bold text-sm">
                                        BELIEBT
                                    </div>
                                @endif

                                @if($package->purchase_limit_per_organization)
                                    <div class="absolute top-0 left-0 bg-gradient-to-r from-yellow-500 to-orange-500 text-white px-4 py-1 rounded-br-2xl text-xs font-bold">
                                        Limitiert: Max. {{ $package->purchase_limit_per_organization }}x
                                    </div>
                                @endif

                                <div class="p-8">
                                    <!-- Package Name -->
                                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $package->name }}</h3>

                                    @if($package->description)
                                        <p class="text-gray-600 mb-6 min-h-[48px]">{{ $package->description }}</p>
                                    @else
                                        <div class="mb-6 min-h-[48px]"></div>
                                    @endif

                                    <!-- Credits -->
                                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 mb-6">
                                        <div class="flex items-baseline justify-center">
                                            <span class="text-5xl font-bold text-blue-600">{{ number_format($package->credits, 0, ',', '.') }}</span>
                                            <span class="text-xl text-blue-700 ml-2">Guthaben</span>
                                        </div>
                                    </div>

                                    <!-- Price -->
                                    <div class="text-center mb-6">
                                        <div class="flex items-baseline justify-center mb-1">
                                            <span class="text-4xl font-bold text-gray-900">{{ number_format($package->price, 2, ',', '.') }}</span>
                                            <span class="text-xl text-gray-600 ml-1">€</span>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ number_format($package->pricePerCredit, 2, ',', '.') }} € pro Guthaben
                                        </div>
                                    </div>

                                    <!-- Features -->
                                    <div class="space-y-3 mb-8">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-gray-700">Guthaben für mehrere Stellenanzeigen nutzbar</span>
                                        </div>
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-gray-700">Guthaben zwischen Einrichtungen übertragbar</span>
                                        </div>
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-gray-700">Unbegrenzte Gültigkeit</span>
                                        </div>
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-gray-700">Rechnung per E-Mail</span>
                                        </div>
                                    </div>

                                    <!-- CTA Button -->
                                    @auth
                                        <a href="{{ route('dashboard') }}" class="block w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-center py-3 px-6 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                                            Jetzt kaufen
                                        </a>
                                    @else
                                        <a href="{{ route('register') }}" class="block w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-center py-3 px-6 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                                            Registrieren & kaufen
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Cooperative Packages -->
            @if($cooperativePackages->isNotEmpty())
                <div class="mb-16">
                    <div class="text-center mb-12">
                        <div class="inline-flex items-center bg-gradient-to-r from-purple-100 to-purple-200 rounded-full px-6 py-2 mb-4">
                            <svg class="w-5 h-5 text-purple-700 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="font-bold text-purple-900">Exklusiv für Evangelische Schulen in Sachsen</span>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-3">Evangelische Schulen-Pakete</h2>
                        <p class="text-lg text-gray-600">Besondere Konditionen für Evangelisches Schulen im Bereich der Evangelischen Schulstiftung Sachsen</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($cooperativePackages as $index => $package)
                            <div class="relative bg-gradient-to-br from-purple-50 to-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-shadow duration-300 border-2 border-purple-200">
                                <div class="absolute top-0 right-0 bg-gradient-to-r from-purple-600 to-purple-700 text-white px-6 py-2 rounded-bl-2xl font-bold text-sm">
                                    Ev. Schulen Sachsen
                                </div>

                                @if($package->purchase_limit_per_organization)
                                    <div class="absolute top-12 right-0 bg-gradient-to-r from-yellow-500 to-orange-500 text-white px-4 py-1 rounded-l-xl text-xs font-bold">
                                        Max. {{ $package->purchase_limit_per_organization }}x
                                    </div>
                                @endif

                                <div class="p-8 pt-12">
                                    <!-- Package Name -->
                                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $package->name }}</h3>

                                    @if($package->description)
                                        <p class="text-gray-600 mb-6 min-h-[48px]">{{ $package->description }}</p>
                                    @else
                                        <div class="mb-6 min-h-[48px]"></div>
                                    @endif

                                    <!-- Credits -->
                                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 mb-6 border border-purple-200">
                                        <div class="flex items-baseline justify-center">
                                            <span class="text-5xl font-bold text-purple-600">{{ number_format($package->credits, 0, ',', '.') }}</span>
                                            <span class="text-xl text-purple-700 ml-2">Guthaben</span>
                                        </div>
                                    </div>

                                    <!-- Price -->
                                    <div class="text-center mb-6">
                                        <div class="flex items-baseline justify-center mb-1">
                                            <span class="text-4xl font-bold text-gray-900">{{ number_format($package->price, 2, ',', '.') }}</span>
                                            <span class="text-xl text-gray-600 ml-1">€</span>
                                        </div>
                                        <div class="text-sm text-purple-600 font-semibold">
                                            {{ number_format($package->pricePerCredit, 2, ',', '.') }} € pro Guthaben
                                        </div>
                                    </div>

                                    <!-- Features -->
                                    <div class="space-y-3 mb-8">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-purple-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-gray-700 font-medium">Exklusiv für Genossenschaftsmitglieder</span>
                                        </div>
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-purple-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-gray-700">Vergünstigte Konditionen</span>
                                        </div>
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-purple-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-gray-700">Alle Standard-Vorteile inklusive</span>
                                        </div>
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-purple-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-gray-700">Priority Support</span>
                                        </div>
                                    </div>

                                    <!-- CTA Button -->
                                    @auth
                                        <a href="{{ route('dashboard') }}" class="block w-full bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white text-center py-3 px-6 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                                            Jetzt kaufen
                                        </a>
                                    @else
                                        <a href="{{ route('register') }}" class="block w-full bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white text-center py-3 px-6 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                                            Mitglied werden & kaufen
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="bg-white py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">Häufig gestellte Fragen</h2>

            <div class="space-y-6">
                <div class="bg-gray-50 rounded-xl p-6 hover:shadow-md transition">
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Wie funktioniert das Guthaben-System?</h3>
                    <p class="text-gray-600">Sie kaufen ein Guthaben-Paket und können dieses für das Veröffentlichen von Stellenanzeigen verwenden. Das Guthaben wird beim Erstellen einer Anzeige automatisch abgebucht und verfällt nicht.</p>
                </div>

                <div class="bg-gray-50 rounded-xl p-6 hover:shadow-md transition">
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Kann ich Guthaben zwischen Einrichtungen übertragen?</h3>
                    <p class="text-gray-600">Ja, als Organisation können Sie Guthaben flexibel zwischen Ihren Einrichtungen umbuchen. So behalten Sie die volle Kontrolle über Ihr Budget.</p>
                </div>

                <div class="bg-gray-50 rounded-xl p-6 hover:shadow-md transition">
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Was kostet eine Stellenanzeige?</h3>
                    <p class="text-gray-600">Die Kosten werden in Guthaben berechnet. Details zu den Kosten pro Stellenanzeige finden Sie in Ihrem Dashboard nach der Registrierung.</p>
                </div>


                <div class="bg-gray-50 rounded-xl p-6 hover:shadow-md transition">
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Welche Zahlungsmöglichkeiten gibt es?</h3>
                    <p class="text-gray-600">Nach dem Kauf erhalten Sie eine Rechnung per E-Mail. Die Zahlung erfolgt bequem per Überweisung.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">Bereit, die passenden Mitarbeitenden zu finden?</h2>
            <p class="text-xl text-blue-100 mb-8">Registrieren Sie sich jetzt kostenlos und veröffentlichen Sie Ihre erste Stellenanzeige!</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @guest
                    <a href="{{ route('register') }}" class="bg-white text-blue-600 hover:bg-gray-100 px-8 py-4 rounded-xl font-bold text-lg transition-all shadow-lg hover:shadow-xl">
                        Jetzt kostenlos registrieren
                    </a>
                    <a href="{{ route('login') }}" class="border-2 border-white text-white hover:bg-white hover:text-blue-600 px-8 py-4 rounded-xl font-bold text-lg transition-all">
                        Bereits registriert? Anmelden
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="bg-white text-blue-600 hover:bg-gray-100 px-8 py-4 rounded-xl font-bold text-lg transition-all shadow-lg hover:shadow-xl">
                        Zum Dashboard
                    </a>
                @endguest
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-white font-bold mb-4">{{ config('app.name') }}</h3>
                    <p class="text-sm">Das Stellenportal für evangelische Schulen und Einrichtungen in Sachsen.</p>
                </div>
                <div>
                    <h3 class="text-white font-bold mb-4">Links</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-white transition">Startseite</a></li>
                        <li><a href="{{ route('public.jobs.index') }}" class="hover:text-white transition">Stellenangebote</a></li>
                        <li><a href="{{ route('public.pricing') }}" class="hover:text-white transition">Preise</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white font-bold mb-4">Rechtliches</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">Impressum</a></li>
                        <li><a href="#" class="hover:text-white transition">Datenschutz</a></li>
                        <li><a href="#" class="hover:text-white transition">AGB</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Alle Rechte vorbehalten.</p>
            </div>
        </div>
    </footer>
</body>
</html>

