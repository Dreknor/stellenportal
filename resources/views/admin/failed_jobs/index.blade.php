<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Fehlgeschlagene Jobs'), 'url' => route('admin.failed-jobs.index')],
            ['label' => __('Übersicht')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Fehlgeschlagene Jobs') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Übersicht aller fehlgeschlagenen Queue-Jobs') }}</p>
    </div>

    <!-- Filter Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <form method="GET" action="{{ route('admin.failed-jobs.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="q" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Suche') }}</label>
                    <input type="text" name="q" id="q" value="{{ $filters['q'] ?? '' }}"
                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="{{ __('Exception oder Payload...') }}">
                </div>
                <div>
                    <label for="queue" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Queue') }}</label>
                    <select name="queue" id="queue" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">{{ __('Alle Queues') }}</option>
                        @foreach(($queues ?? []) as $qname)
                            <option value="{{ $qname }}" {{ (isset($filters['queue']) && $filters['queue'] === $qname) ? 'selected' : '' }}>{{ $qname }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Von') }}</label>
                    <input type="date" name="date_from" id="date_from" value="{{ $filters['date_from'] ?? '' }}"
                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Bis') }}</label>
                    <input type="date" name="date_to" id="date_to" value="{{ $filters['date_to'] ?? '' }}"
                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
            <div class="flex gap-2">
                <x-button type="primary" native-type="submit">
                    <x-fas-search class="w-3 mr-2"/>
                    {{ __('Suchen') }}
                </x-button>
                <x-button type="secondary" tag="a" :href="route('admin.failed-jobs.index')">{{ __('Zurücksetzen') }}</x-button>
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('ID') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Queue') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Fehlerzeit') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Exception') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Aktionen') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($jobs as $job)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $job->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $job->queue ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($job->failed_at)->format('d.m.Y H:i:s') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ Str::limit($job->exception, 80) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <x-button type="secondary" size="sm" tag="a" :href="route('admin.failed-jobs.show', $job->id)">
                                    <x-fas-eye class="w-3"/>
                                </x-button>
                                <form method="POST" action="{{ route('admin.failed-jobs.retry', $job->id) }}" class="inline-block">@csrf
                                    <x-button type="primary" size="sm" native-type="submit">{{ __('Neu starten') }}</x-button>
                                </form>
                                <form method="POST" action="{{ route('admin.failed-jobs.destroy', $job->id) }}" class="inline-block" onsubmit="return confirm('{{ __('Möchten Sie diesen fehlgeschlagenen Job wirklich löschen?') }}');">
                                    @method('DELETE')
                                    @csrf
                                    <x-button type="secondary" size="sm" native-type="submit">{{ __('Löschen') }}</x-button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <x-fas-bug class="w-12 h-12 mx-auto mb-4 opacity-50"/>
                                    <p class="text-lg font-medium">{{ __('Keine fehlgeschlagenen Jobs') }}</p>
                                    <p class="text-sm mt-1">{{ __('Es gibt aktuell keine fehlgeschlagenen Queue-Jobs.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Simple pagination controls -->
        <div class="mt-4 flex items-center justify-between">
            <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('Anzeigen') }} {{ count($jobs) }} / {{ $total }}</div>
            <div>
                @if($page > 1)
                    <a href="?page={{ $page - 1 }}" class="text-sm text-blue-600">&laquo; {{ __('Zurück') }}</a>
                @endif
                @if($page * $perPage < $total)
                    <a href="?page={{ $page + 1 }}" class="ml-3 text-sm text-blue-600">{{ __('Weiter') }} &raquo;</a>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
