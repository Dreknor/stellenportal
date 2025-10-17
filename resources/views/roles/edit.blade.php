@props(['role', 'permissions', 'rolePermissions'])

<x-layouts.app>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Rolle bearbeiten</h2>
                        <a href="{{ route('roles.index') }}" class="text-gray-600 hover:text-gray-800">
                            Zurück zur Übersicht
                        </a>
                    </div>

                    @if($role->name === 'Super Admin')
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                            <p class="text-yellow-800">
                                <strong>Hinweis:</strong> Die Super Admin Rolle kann bearbeitet, aber nicht gelöscht werden.
                            </p>
                        </div>
                    @endif

                    <form action="{{ route('roles.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Rollenname <span class="text-red-600">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="z.B. Administrator">
                            @error('name')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Berechtigungen zuweisen
                            </label>

                            @foreach($permissions as $group => $groupPermissions)
                                <div class="mb-4 border rounded-lg p-4">
                                    <h3 class="font-semibold text-gray-700 mb-3 capitalize">{{ $group }}</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($groupPermissions as $permission)
                                            <label class="flex items-center">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                <span class="ml-2 text-sm text-gray-700">{{ $permission->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach

                            @error('permissions')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-between items-center border-t pt-6">
                            <a href="{{ route('roles.index') }}" class="text-gray-600 hover:text-gray-800">
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

