@props(['facility', 'organization', 'balance', 'organizationBalance'])

<x-layouts.app>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Guthaben an Träger übertragen</h2>
                        <a href="{{ route('credits.facility.transactions', $facility) }}" class="text-blue-600 hover:text-blue-800">
                            Zurück zur Historie
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Verfügbares Guthaben (Einrichtung)</p>
                                    <p class="text-3xl font-bold text-blue-600">{{ number_format($balance, 0, ',', '.') }}</p>
                                    <p class="text-sm text-gray-500">{{ $facility->name }}</p>
                                </div>
                                <div class="text-blue-600">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Guthaben (Träger)</p>
                                    <p class="text-3xl font-bold text-green-600">{{ number_format($organizationBalance, 0, ',', '.') }}</p>
                                    <p class="text-sm text-gray-500">{{ $organization->name }}</p>
                                </div>
                                <div class="text-green-600">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6 flex items-center justify-center">
                        <div class="text-gray-400">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($balance <= 0)
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <p class="text-yellow-800">Sie haben kein Guthaben zum Übertragen.</p>
                            <a href="{{ route('credits.facility.purchase', $facility) }}" class="text-blue-600 hover:text-blue-800 font-semibold mt-2 inline-block">
                                Jetzt Guthaben aufladen →
                            </a>
                        </div>
                    @else
                        <form action="{{ route('credits.facility.transfer-to-organization.store', $facility) }}" method="POST">
                            @csrf

                            <div class="mb-6">
                                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                                    Anzahl Guthaben
                                </label>
                                <input type="number" name="amount" id="amount" min="1" max="{{ $balance }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="z.B. 100">
                                <p class="text-sm text-gray-500 mt-1">Maximal {{ number_format($balance, 0, ',', '.') }} Guthaben verfügbar</p>
                                @error('amount')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label for="note" class="block text-sm font-medium text-gray-700 mb-2">
                                    Grund der Übertragung (optional)
                                </label>
                                <textarea name="note" id="note" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="z.B. Nicht benötigtes Guthaben, Projektabschluss, etc."></textarea>
                                @error('note')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800">Hinweis zur Übertragung</h3>
                                        <div class="mt-2 text-sm text-blue-700">
                                            <p>Das Guthaben wird von dieser Einrichtung (<strong>{{ $facility->name }}</strong>) an den Träger (<strong>{{ $organization->name }}</strong>) übertragen. Diese Aktion kann nicht rückgängig gemacht werden.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-between items-center">
                                <a href="{{ route('credits.facility.transactions', $facility) }}" class="text-gray-600 hover:text-gray-800">
                                    Abbrechen
                                </a>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition">
                                    Guthaben an Träger übertragen
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

