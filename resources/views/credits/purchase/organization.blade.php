@props(['organization', 'packages', 'balance'])

<x-layouts.app>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Guthaben aufladen</h2>
                        <div class="space-x-4">
                            <a href="{{ route('credits.organization.transfer', $organization) }}" class="text-blue-600 hover:text-blue-800">
                                Guthaben umbuchen
                            </a>
                            <a href="{{ route('credits.organization.transactions', $organization) }}" class="text-blue-600 hover:text-blue-800">
                                Transaktionshistorie
                            </a>
                        </div>
                    </div>

                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Aktueller Kontostand</p>
                                <p class="text-3xl font-bold text-green-600">{{ number_format($balance, 0, ',', '.') }}</p>
                                <p class="text-sm text-gray-500">Guthaben</p>
                            </div>
                            <div class="text-green-600">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Organisation</h3>
                        <p class="text-gray-600">{{ $organization->name }}</p>
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
                        <form action="{{ route('credits.organization.purchase.store', $organization) }}" method="POST">
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
                                        <div class="border-2 border-gray-200 rounded-lg p-6 peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-green-300 transition {{ !$package->can_be_purchased ? 'bg-gray-50' : '' }}">
                                            <div class="text-center">
                                                <h4 class="text-xl font-bold text-gray-900 mb-2">{{ $package->name }}</h4>
                                                @if($package->description)
                                                    <p class="text-sm text-gray-600 mb-4">{{ $package->description }}</p>
                                                @endif
                                                <div class="mb-4">
                                                    <span class="text-4xl font-bold text-green-600">{{ number_format($package->credits, 0, ',', '.') }}</span>
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
                                <textarea name="note" id="note" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Optionale Anmerkungen zur Bestellung..."></textarea>
                                @error('note')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-between items-center">
                                <a href="{{ url()->previous() }}" class="text-gray-600 hover:text-gray-800">
                                    Abbrechen
                                </a>
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition">
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

