<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Benutzer'), 'url' => route('admin.users.index')],
            ['label' => $user->first_name . ' ' . $user->last_name],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $user->first_name }} {{ $user->last_name }}</h1>
        <div class="flex gap-2">
            @can('admin impersonate users')
                @if($user->id !== auth()->id() && !$user->hasRole(['Super Admin', 'Admin']))
                    <form action="{{ route('admin.users.impersonate', $user) }}" method="POST" class="inline">
                        @csrf
                        <x-button type="warning" native-type="submit"
                            onclick="return confirm('Möchten Sie sich als {{ $user->name }} anmelden?')">
                            <x-fas-user-secret class="w-3 mr-3"/>
                            {{ __('Als Benutzer anmelden') }}
                        </x-button>
                    </form>
                @endif
            @endcan
            @can('admin edit users')
                <x-button type="primary" tag="a" :href="route('admin.users.edit', $user)">
                    <x-fas-edit class="w-3 mr-3"/>
                    {{ __('Bearbeiten') }}
                </x-button>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Persönliche Informationen') }}</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Vorname') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->first_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Nachname') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->last_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('E-Mail') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('E-Mail Verifizierung') }}</dt>
                        <dd class="mt-1">
                            @if($user->email_verified_at)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                    <x-fas-check-circle class="w-3 mr-1"/>
                                    {{ $user->email_verified_at->format('d.m.Y H:i') }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                    <x-fas-exclamation-circle class="w-3 mr-1"/>
                                    {{ __('Nicht verifiziert') }}
                                </span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Erstellt am') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->created_at->format('d.m.Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Aktualisiert am') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->updated_at->format('d.m.Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Organizations -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Organisationen') }}</h2>
                @forelse($user->organizations as $organization)
                    <div class="mb-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-gray-100">{{ $organization->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $organization->email }}</p>
                            </div>
                            <x-button type="secondary" size="sm" tag="a" :href="route('admin.organizations.show', $organization)">
                                <x-fas-arrow-right class="w-3"/>
                            </x-button>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Keine Organisationen zugeordnet') }}</p>
                @endforelse
            </div>

            <!-- Facilities -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Einrichtungen') }}</h2>
                @forelse($user->facilities as $facility)
                    <div class="mb-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-gray-100">{{ $facility->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $facility->organization->name }}</p>
                            </div>
                            <x-button type="secondary" size="sm" tag="a" :href="route('admin.facilities.show', $facility)">
                                <x-fas-arrow-right class="w-3"/>
                            </x-button>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Keine Einrichtungen zugeordnet') }}</p>
                @endforelse
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Roles & Permissions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Rollen') }}</h2>
                @forelse($user->roles as $role)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 mb-2">
                        {{ $role->name }}
                    </span>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Keine Rollen zugeordnet') }}</p>
                @endforelse

                <h3 class="text-md font-semibold text-gray-800 dark:text-gray-100 mt-6 mb-3">{{ __('Berechtigungen') }}</h3>
                <div class="space-y-1 max-h-64 overflow-y-auto">
                    @forelse($user->getAllPermissions() as $permission)
                        <div class="text-xs text-gray-600 dark:text-gray-400 flex items-center">
                            <x-fas-check class="w-3 mr-2 text-green-500"/>
                            {{ $permission->name }}
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Keine Berechtigungen') }}</p>
                    @endforelse
                </div>
            </div>

            <!-- Actions -->
            @can('admin delete users')
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Aktionen') }}</h2>
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('{{ __('Sind Sie sicher, dass Sie diesen Benutzer löschen möchten?') }}')">
                        @csrf
                        @method('DELETE')
                        <x-button type="danger" native-type="submit" class="w-full">
                            <x-fas-trash class="w-3 mr-2"/>
                            {{ __('Benutzer löschen') }}
                        </x-button>
                    </form>
                </div>
            @endcan
        </div>
    </div>
</x-layouts.app>

