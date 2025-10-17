<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Organisationen'), 'url' => route('admin.organizations.index')],
            ['label' => __('Übersicht')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Organisationsverwaltung') }}</h1>
    </div>

    <!-- Search Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <form method="GET" action="{{ route('admin.organizations.index') }}" class="space-y-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Suche') }}</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       placeholder="{{ __('Name, E-Mail oder Telefon...') }}">
            </div>
            <div class="flex gap-2">
                <x-button type="primary" native-type="submit">
                    <x-fas-search class="w-3 mr-2"/>
                    {{ __('Suchen') }}
                </x-button>
                <x-button type="secondary" tag="a" :href="route('admin.organizations.index')">
                    {{ __('Zurücksetzen') }}
                </x-button>
            </div>
        </form>
    </div>

    <!-- Organizations Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Name') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Kontakt') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Benutzer') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Einrichtungen') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Guthaben') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Aktionen') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($organizations as $organization)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $organization->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $organization->email ?? '-' }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $organization->phone ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $organization->users->count() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $organization->facilities->count() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $organization->creditBalance->balance ?? 0 }} {{ __('Credits') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($organization->is_approved)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                        <x-fas-check-circle class="w-3 mr-1"/>
                                        {{ __('Bestätigt') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                        <x-fas-clock class="w-3 mr-1"/>
                                        {{ __('Ausstehend') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    @can('admin view organizations')
                                        <x-button type="secondary" size="sm" tag="a" :href="route('admin.organizations.show', $organization)">
                                            <x-fas-eye class="w-3"/>
                                        </x-button>
                                    @endcan
                                    @can('admin edit organizations')
                                        <x-button type="primary" size="sm" tag="a" :href="route('admin.organizations.edit', $organization)">
                                            <x-fas-edit class="w-3"/>
                                        </x-button>
                                        @if($organization->is_approved)
                                            <form method="POST" action="{{ route('admin.organizations.unapprove', $organization) }}" class="inline">
                                                @csrf
                                                <x-button type="warning" size="sm" native-type="submit" onclick="return confirm('{{ __('Bestätigung zurückziehen?') }}')">
                                                    <x-fas-times-circle class="w-3"/>
                                                </x-button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.organizations.approve', $organization) }}" class="inline">
                                                @csrf
                                                <x-button type="success" size="sm" native-type="submit">
                                                    <x-fas-check-circle class="w-3"/>
                                                </x-button>
                                            </form>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <x-fas-building class="w-12 h-12 mx-auto mb-4 opacity-50"/>
                                    <p class="text-lg font-medium">{{ __('Keine Organisationen gefunden') }}</p>
                                    <p class="text-sm mt-1">{{ __('Es wurden keine Organisationen gefunden, die Ihren Suchkriterien entsprechen.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($organizations->hasPages())
        <div class="mt-6">
            {{ $organizations->links() }}
        </div>
    @endif
</x-layouts.app>
