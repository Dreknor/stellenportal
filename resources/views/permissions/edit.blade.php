@props(['permission'])

<x-layouts.app>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Berechtigung bearbeiten</h2>
                        <a href="{{ route('permissions.index') }}" class="text-gray-600 hover:text-gray-800">
                            Zurück zur Übersicht
                        </a>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <p class="text-sm text-yellow-800">
                            <strong>Achtung:</strong> Das Ändern des Namens einer Berechtigung kann Auswirkungen auf bestehende Funktionalität haben.
                        </p>
                    </div>

                    <form action="{{ route('permissions.update', $permission) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Berechtigungsname <span class="text-red-600">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $permission->name) }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500" placeholder="z.B. view reports">
                            <p class="text-sm text-gray-500 mt-1">Format: "aktion kategorie" (z.B. "view users", "edit posts")</p>
                            @error('name')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <h3 class="font-semibold text-gray-700 mb-2">Verwendung</h3>
                            <p class="text-sm text-gray-600">
                                Diese Berechtigung wird in <strong>{{ $permission->roles->count() }}</strong> {{ Str::plural('Rolle', $permission->roles->count()) }} verwendet.
                            </p>
                        </div>

                        <div class="flex justify-between items-center border-t pt-6">
                            <a href="{{ route('permissions.index') }}" class="text-gray-600 hover:text-gray-800">
                                Abbrechen
                            </a>
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition">
                                Änderungen speichern
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
