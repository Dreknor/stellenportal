<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Logs'), 'url' => route('admin.logs.index')],
            ['label' => __('Übersicht')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Systemlogs') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Anwendungs-Protokolleinträge aus der Datenbank') }}</p>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-lg p-3">
                    <x-fas-database class="w-6 h-6 text-blue-600 dark:text-blue-300"/>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Gesamt') }}</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($stats['total']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 dark:bg-red-900 rounded-lg p-3">
                    <x-fas-exclamation-circle class="w-6 h-6 text-red-600 dark:text-red-300"/>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Fehler') }}</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($stats['error']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900 rounded-lg p-3">
                    <x-fas-exclamation-triangle class="w-6 h-6 text-yellow-600 dark:text-yellow-300"/>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Warnungen') }}</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($stats['warning']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-lg p-3">
                    <x-fas-info-circle class="w-6 h-6 text-blue-600 dark:text-blue-300"/>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Info') }}</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($stats['info']) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter and Actions --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <form method="GET" action="{{ route('admin.logs.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Search --}}
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('Suche') }}
                    </label>
                    <input type="text"
                           name="search"
                           id="search"
                           value="{{ request('search') }}"
                           placeholder="{{ __('Nachricht durchsuchen...') }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100">
                </div>

                {{-- Level Filter --}}
                <div>
                    <label for="level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('Log-Level') }}
                    </label>
                    <select name="level"
                            id="level"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100">
                        <option value="all">{{ __('Alle') }}</option>
                        @foreach($levels as $level)
                            <option value="{{ strtolower($level) }}" {{ request('level') === strtolower($level) ? 'selected' : '' }}>
                                {{ $level }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Channel Filter --}}
                <div>
                    <label for="channel" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('Kanal') }}
                    </label>
                    <select name="channel"
                            id="channel"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100">
                        <option value="all">{{ __('Alle') }}</option>
                        @foreach($channels as $channel)
                            <option value="{{ $channel }}" {{ request('channel') === $channel ? 'selected' : '' }}>
                                {{ $channel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Actions --}}
                <div class="flex items-end gap-2">
                    <x-button type="primary" tag="button" submit="true" class="flex-1">
                        <x-fas-filter class="w-3 mr-2"/>
                        {{ __('Filtern') }}
                    </x-button>
                    @if(request()->hasAny(['search', 'level', 'channel']))
                        <x-button type="secondary" tag="a" :href="route('admin.logs.index')">
                            <x-fas-times class="w-3"/>
                        </x-button>
                    @endif
                </div>
            </div>
        </form>

        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 flex flex-wrap gap-2">
            <x-button type="secondary" size="sm" tag="a" :href="route('admin.logs.export', request()->query())">
                <x-fas-download class="w-3 mr-2"/>
                {{ __('Export (JSON)') }}
            </x-button>

            <button type="button"
                    onclick="if(confirm('{{ __('Sind Sie sicher, dass Sie alte Log-Einträge löschen möchten?') }}')) { document.getElementById('clear-form').submit(); }"
                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 dark:bg-red-900 dark:text-red-100 dark:hover:bg-red-800 rounded-md transition-colors">
                <x-fas-trash class="w-3 mr-2"/>
                {{ __('Alte Einträge löschen') }}
            </button>

            <form id="clear-form"
                  method="POST"
                  action="{{ route('admin.logs.clear') }}"
                  class="hidden">
                @csrf
                @method('DELETE')
                <input type="hidden" name="days" value="30">
            </form>
        </div>
    </div>

    {{-- Log Entries Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-24">{{ __('Level') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">{{ __('Datum/Zeit') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-24">{{ __('Kanal') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Nachricht') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-20">{{ __('Details') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $badgeColors = [
                                        'DEBUG' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                        'INFO' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                        'NOTICE' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300',
                                        'WARNING' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                        'ERROR' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                                        'CRITICAL' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                        'ALERT' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                        'EMERGENCY' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                    ];
                                    $colorClass = $badgeColors[$log->level_name] ?? $badgeColors['DEBUG'];
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $colorClass }}">
                                    <x-dynamic-component :component="$log->level_icon" class="w-3 h-3 mr-1"/>
                                    {{ $log->level_name }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                <div class="flex flex-col">
                                    <span>{{ $log->created_at->format('d.m.Y') }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $log->created_at->format('H:i:s') }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 dark:bg-gray-700">
                                    {{ $log->channel ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                <div class="max-w-2xl truncate" title="{{ $log->message }}">
                                    {{ Str::limit($log->message, 150) }}
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                <x-button type="secondary" size="sm" tag="a" :href="route('admin.logs.show', $log->id)">
                                    <x-fas-eye class="w-3"/>
                                </x-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <x-fas-inbox class="w-12 h-12 mx-auto mb-4 opacity-50"/>
                                    <p class="text-lg font-medium">{{ __('Keine Log-Einträge gefunden') }}</p>
                                    <p class="text-sm mt-1">{{ __('Es wurden keine Log-Einträge in der Datenbank gefunden.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>

