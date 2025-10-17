@props(['role'])

<x-layouts.app>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Rolle: {{ $role->name }}</h2>
                        <div class="space-x-2">
                            <a href="{{ route('roles.index') }}" class="text-gray-600 hover:text-gray-800">
                                Zurück zur Übersicht
                            </a>
                            @can('update', $role)
                                <a href="{{ route('roles.edit', $role) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                                    Bearbeiten
                                </a>
                            @endcan
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <div class="text-sm text-purple-600 font-semibold mb-1">Berechtigungen</div>
                            <div class="text-3xl font-bold text-purple-900">{{ $role->permissions->count() }}</div>
                        </div>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="text-sm text-green-600 font-semibold mb-1">Benutzer</div>
                            <div class="text-3xl font-bold text-green-900">{{ $role->users->count() }}</div>
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="text-sm text-blue-600 font-semibold mb-1">Erstellt am</div>
                            <div class="text-lg font-bold text-blue-900">{{ $role->created_at->format('d.m.Y') }}</div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Zugewiesene Berechtigungen</h3>
                        @if($role->permissions->isEmpty())
                            <p class="text-gray-500">Dieser Rolle sind noch keine Berechtigungen zugewiesen.</p>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($role->permissions as $permission)
                                    <div class="flex items-center bg-gray-50 rounded-lg p-3">
                                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm text-gray-700">{{ $permission->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Benutzer mit dieser Rolle</h3>
                        @if($role->users->isEmpty())
                            <p class="text-gray-500">Dieser Rolle sind noch keine Benutzer zugewiesen.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">E-Mail</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($role->users as $user)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $user->first_name }} {{ $user->last_name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $user->email }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
