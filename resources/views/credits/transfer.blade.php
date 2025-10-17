@props(['organization', 'facilities', 'balance'])

<x-layouts.app>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Guthaben umbuchen</h2>
                        <a href="{{ route('credits.organization.transactions', $organization) }}" class="text-blue-600 hover:text-blue-800">
                            Zurück zur Historie
                        </a>
                    </div>

                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Verfügbares Guthaben (Organisation)</p>
                                <p class="text-3xl font-bold text-green-600">{{ number_format($balance, 0, ',', '.') }}</p>
                                <p class="text-sm text-gray-500">{{ $organization->name }}</p>
                            </div>
                            <div class="text-green-600">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                            </div>
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

                    @if($facilities->isEmpty())
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <p class="text-yellow-800">Dieser Organisation sind noch keine Einrichtungen zugeordnet.</p>
                        </div>
                    @elseif($balance <= 0)
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <p class="text-yellow-800">Sie haben kein Guthaben zum Umbuchen. Bitte laden Sie zuerst Guthaben auf.</p>
                            <a href="{{ route('credits.organization.purchase', $organization) }}" class="text-blue-600 hover:text-blue-800 font-semibold mt-2 inline-block">
                                Jetzt Guthaben aufladen →
                            </a>
                        </div>
                    @else
                        <form action="{{ route('credits.organization.transfer.store', $organization) }}" method="POST">
                            @csrf

                            <div class="mb-6">
                                <label for="facility_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Ziel-Einrichtung auswählen
                                </label>
                                <select name="facility_id" id="facility_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">-- Bitte wählen --</option>
                                    @foreach($facilities as $facility)
                                        <option value="{{ $facility->id }}">
                                            {{ $facility->name }} (aktuell: {{ number_format($facility->getCurrentCreditBalance(), 0, ',', '.') }} Guthaben)
                                        </option>
                                    @endforeach
                                </select>
                                @error('facility_id')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

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
                                    Grund der Umbuchung (optional)
                                </label>
                                <textarea name="note" id="note" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="z.B. Monatliche Zuteilung, Projektbudget, etc."></textarea>
                                @error('note')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-between items-center">
                                <a href="{{ route('credits.organization.transactions', $organization) }}" class="text-gray-600 hover:text-gray-800">
                                    Abbrechen
                                </a>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition">
                                    Guthaben umbuchen
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

