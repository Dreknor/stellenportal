<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Einrichtungen'), 'url' => route('admin.facilities.index')],
            ['label' => __('Übersicht')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Einrichtungsverwaltung') }}</h1>
    </div>

    <!-- Search and Filter Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <form method="GET" action="{{ route('admin.facilities.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Suche') }}</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="{{ __('Name, E-Mail oder Telefon...') }}">
                </div>
                <div>
                    <label for="organization" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Organisation') }}</label>
                    <select name="organization" id="organization" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">{{ __('Alle Organisationen') }}</option>
                        @foreach($organizations as $organization)
                            <option value="{{ $organization->id }}" {{ request('organization') == $organization->id ? 'selected' : '' }}>
                                {{ $organization->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex gap-2">
                <x-button type="primary" native-type="submit">
                    <x-fas-search class="w-3 mr-2"/>
                    {{ __('Suchen') }}
                </x-button>
                <x-button type="secondary" tag="a" :href="route('admin.facilities.index')">
                    {{ __('Zurücksetzen') }}
                </x-button>
            </div>
        </form>
    </div>

    <!-- Facilities Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Name') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Organisation') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Kontakt') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Guthaben') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Standort') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Aktionen') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($facilities as $facility)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $facility->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $facility->organization->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $facility->email ?? '-' }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $facility->phone ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $facility->creditBalance->balance ?? 0 }} {{ __('Credits') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($facility->address)
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $facility->address->city }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $facility->address->postal_code }}</div>
                                @else
                                    <span class="text-sm text-gray-500 dark:text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    @can('admin view facilities')
                                        <x-button type="secondary" size="sm" tag="a" :href="route('admin.facilities.show', $facility)">
                                            <x-fas-eye class="w-3"/>
                                        </x-button>
                                    @endcan
                                    @can('admin edit facilities')
                                        <x-button type="primary" size="sm" tag="a" :href="route('admin.facilities.edit', $facility)">
                                            <x-fas-edit class="w-3"/>
                                        </x-button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <x-fas-building class="w-12 h-12 mx-auto mb-4 opacity-50"/>
                                    <p class="text-lg font-medium">{{ __('Keine Einrichtungen gefunden') }}</p>
                                    <p class="text-sm mt-1">{{ __('Es wurden keine Einrichtungen gefunden, die Ihren Suchkriterien entsprechen.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($facilities->hasPages())
        <div class="mt-6">
            {{ $facilities->links() }}
        </div>
    @endif
</x-layouts.app>

