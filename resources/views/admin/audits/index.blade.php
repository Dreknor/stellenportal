<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Audit-Logs'), 'url' => route('admin.audits.index')],
            ['label' => __('Übersicht')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Audit-Logs') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Übersicht aller Systemaktivitäten und Änderungen') }}</p>
    </div>

    <!-- Filter Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <form method="GET" action="{{ route('admin.audits.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Suche') }}</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="{{ __('Modell, Event oder IP...') }}">
                </div>
                <div>
                    <label for="event" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Event') }}</label>
                    <select name="event" id="event" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">{{ __('Alle Events') }}</option>
                        @foreach($eventTypes as $eventType)
                            <option value="{{ $eventType }}" {{ request('event') === $eventType ? 'selected' : '' }}>
                                {{ ucfirst($eventType) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="auditable_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Modell') }}</label>
                    <select name="auditable_type" id="auditable_type" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">{{ __('Alle Modelle') }}</option>
                        @foreach($auditableTypes as $type)
                            <option value="{{ $type }}" {{ request('auditable_type') === $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Benutzer ID') }}</label>
                    <input type="number" name="user_id" id="user_id" value="{{ request('user_id') }}"
                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="{{ __('Benutzer ID...') }}">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Von') }}</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Bis') }}</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
            <div class="flex gap-2">
                <x-button type="primary" native-type="submit">
                    <x-fas-search class="w-3 mr-2"/>
                    {{ __('Suchen') }}
                </x-button>
                <x-button type="secondary" tag="a" :href="route('admin.audits.index')">
                    {{ __('Zurücksetzen') }}
                </x-button>
            </div>
        </form>
    </div>

    <!-- Audits Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Zeitpunkt') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Event') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Modell') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Benutzer') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('IP-Adresse') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Aktionen') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($audits as $audit)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $audit->created_at->format('d.m.Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($audit->event === 'created')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                        <x-fas-plus class="w-3 mr-1"/>
                                        {{ __('Erstellt') }}
                                    </span>
                                @elseif($audit->event === 'updated')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                        <x-fas-edit class="w-3 mr-1"/>
                                        {{ __('Aktualisiert') }}
                                    </span>
                                @elseif($audit->event === 'deleted')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                        <x-fas-trash class="w-3 mr-1"/>
                                        {{ __('Gelöscht') }}
                                    </span>
                                @elseif($audit->event === 'restored')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">
                                        <x-fas-undo class="w-3 mr-1"/>
                                        {{ __('Wiederhergestellt') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300">
                                        {{ ucfirst($audit->event) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ class_basename($audit->auditable_type) }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    ID: {{ $audit->auditable_id }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($audit->user)
                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ $audit->user->first_name }} {{ $audit->user->last_name }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        ID: {{ $audit->user_id }}
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('System') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $audit->ip_address ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <x-button type="secondary" size="sm" tag="a" :href="route('admin.audits.show', $audit)">
                                    <x-fas-eye class="w-3"/>
                                </x-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <x-fas-clipboard-list class="w-12 h-12 mx-auto mb-4 opacity-50"/>
                                    <p class="text-lg font-medium">{{ __('Keine Audit-Logs gefunden') }}</p>
                                    <p class="text-sm mt-1">{{ __('Es wurden keine Audit-Einträge gefunden, die Ihren Suchkriterien entsprechen.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($audits->hasPages())
        <div class="mt-6">
            {{ $audits->links() }}
        </div>
    @endif
</x-layouts.app>

