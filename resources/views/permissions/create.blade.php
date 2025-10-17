<x-layouts.app>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Neue Berechtigung erstellen</h2>
                        <a href="{{ route('permissions.index') }}" class="text-gray-600 hover:text-gray-800">
                            Zurück zur Übersicht
                        </a>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <p class="text-sm text-blue-800">
                            <strong>Hinweis:</strong> Berechtigungsnamen sollten klein geschrieben sein und Leerzeichen enthalten.
                            Beispiele: "view users", "create posts", "manage settings"
                        </p>
                    </div>

                    <form action="{{ route('permissions.store') }}" method="POST">
                        @csrf

                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Berechtigungsname <span class="text-red-600">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500" placeholder="z.B. view reports">
                            <p class="text-sm text-gray-500 mt-1">Format: "aktion kategorie" (z.B. "view users", "edit posts")</p>
                            @error('name')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-between items-center border-t pt-6">
                            <a href="{{ route('permissions.index') }}" class="text-gray-600 hover:text-gray-800">
                                Abbrechen
                            </a>
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition">
                                Berechtigung erstellen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

