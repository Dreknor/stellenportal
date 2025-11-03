@props(['package'])

<x-layouts.app>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Guthaben-Paket bearbeiten</h2>
                        <a href="{{ route('credits.packages.index') }}" class="text-gray-600 hover:text-gray-800">
                            Zurück zur Übersicht
                        </a>
                    </div>

                    <form action="{{ route('credits.packages.update', $package) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Grunddaten -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Grunddaten</h3>

                            <div class="space-y-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Paketname <span class="text-red-600">*</span>
                                    </label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $package->name) }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="z.B. Starter-Paket">
                                    @error('name')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                        Beschreibung
                                    </label>
                                    <textarea name="description" id="description" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Kurze Beschreibung des Pakets">{{ old('description', $package->description) }}</textarea>
                                    @error('description')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Preis und Guthaben -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Preis und Guthaben</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="credits" class="block text-sm font-medium text-gray-700 mb-1">
                                        Anzahl Guthaben <span class="text-red-600">*</span>
                                    </label>
                                    <input type="number" name="credits" id="credits" value="{{ old('credits', $package->credits) }}" min="1" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="z.B. 100">
                                    @error('credits')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">
                                        Preis in EUR <span class="text-red-600">*</span>
                                    </label>
                                    <input type="number" name="price" id="price" value="{{ old('price', $package->price) }}" min="0.01" step="0.01" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="z.B. 49.99">
                                    @error('price')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Einstellungen -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Einstellungen</h3>

                            <div class="space-y-3">
                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $package->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">Paket ist aktiv (kann gekauft werden)</span>
                                    </label>
                                    @error('is_active')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="flex items-start">
                                        <input type="checkbox" name="for_cooperative_members" value="1" {{ old('for_cooperative_members', $package->for_cooperative_members) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 mt-0.5">
                                        <span class="ml-2">
                                            <span class="text-sm text-gray-700 block">Nur für Genossenschaftsmitglieder</span>
                                            <span class="text-xs text-gray-500 block mt-0.5">Wenn aktiviert, ist dieses Paket nur für Organisationen sichtbar, die Mitglied der Genossenschaft sind.</span>
                                        </span>
                                    </label>
                                    @error('for_cooperative_members')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Hinweis -->
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        Änderungen an diesem Paket wirken sich nicht auf bereits getätigte Käufe aus. Neue Bestellungen verwenden die aktualisierten Werte.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Aktionen -->
                        <div class="flex justify-between items-center pt-4">
                            <a href="{{ route('credits.packages.index') }}" class="text-gray-600 hover:text-gray-800">
                                Abbrechen
                            </a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition">
                                Änderungen speichern
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

