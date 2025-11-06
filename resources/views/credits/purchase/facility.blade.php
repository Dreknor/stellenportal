@props(['facility', 'packages', 'balance'])

<x-layouts.app>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Guthaben aufladen</h2>
                        <a href="{{ route('credits.facility.transactions', $facility) }}" class="text-blue-600 hover:text-blue-800">
                            Transaktionshistorie anzeigen
                        </a>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Aktueller Kontostand</p>
                                <p class="text-3xl font-bold text-blue-600">{{ number_format($balance, 0, ',', '.') }}</p>
                                <p class="text-sm text-gray-500">Guthaben</p>
                            </div>
                            <div class="text-blue-600">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Einrichtung</h3>
                        <p class="text-gray-600">{{ $facility->name }}</p>
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

                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Guthaben-Paket auswählen</h3>

                    @if($packages->isEmpty())
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <p class="text-yellow-800">Momentan sind keine Guthaben-Pakete verfügbar.</p>
                        </div>
                    @else
                        <form action="{{ route('credits.facility.purchase.store', $facility) }}" method="POST">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                                @foreach($packages as $package)
                                    <label class="cursor-pointer {{ !$package->can_be_purchased ? 'opacity-60 cursor-not-allowed' : '' }}">
                                        <input type="radio"
                                               name="credit_package_id"
                                               value="{{ $package->id }}"
                                               class="peer sr-only"
                                               {{ !$package->can_be_purchased ? 'disabled' : '' }}
                                               required>
                                        <div class="border-2 border-gray-200 rounded-lg p-6 peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-blue-300 transition {{ !$package->can_be_purchased ? 'bg-gray-50' : '' }}">
                                            <div class="text-center">
                                                <h4 class="text-xl font-bold text-gray-900 mb-2">{{ $package->name }}</h4>
                                                @if($package->description)
                                                    <p class="text-sm text-gray-600 mb-4">{{ $package->description }}</p>
                                                @endif
                                                <div class="mb-4">
                                                    <span class="text-4xl font-bold text-blue-600">{{ number_format($package->credits, 0, ',', '.') }}</span>
                                                    <span class="text-gray-600">Guthaben</span>
                                                </div>
                                                <div class="text-2xl font-bold text-gray-900 mb-2">
                                                    {{ number_format($package->price, 2, ',', '.') }} €
                                                </div>
                                                <div class="text-sm text-gray-500 mb-2">
                                                    {{ number_format($package->pricePerCredit, 2, ',', '.') }} € pro Guthaben
                                                </div>

                                                @if($package->purchase_limit_per_organization)
                                                    @if(!$package->can_be_purchased)
                                                        <div class="mt-3 px-3 py-2 bg-red-100 text-red-700 text-sm rounded-md">
                                                            <svg class="w-4 h-4 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                            </svg>
                                                            Kauflimit erreicht
                                                        </div>
                                                    @else
                                                        <div class="mt-3 px-3 py-2 bg-yellow-50 text-yellow-700 text-xs rounded-md">
                                                            <svg class="w-4 h-4 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                            </svg>
                                                            Noch {{ $package->remaining_purchases }}x verfügbar
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <div class="mb-6">
                                <label for="note" class="block text-sm font-medium text-gray-700 mb-2">
                                    Notiz (optional)
                                </label>
                                <textarea name="note" id="note" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Optionale Anmerkungen zur Bestellung..."></textarea>
                                @error('note')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-between items-center">
                                <a href="{{ url()->previous() }}" class="text-gray-600 hover:text-gray-800">
                                    Abbrechen
                                </a>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition">
                                    Guthaben kaufen
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

