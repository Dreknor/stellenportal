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
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Logs') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Anwendung-Logdateien anzeigen und herunterladen') }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Datei') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Größe') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Letzte Änderung') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Aktionen') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($files as $f)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $f['name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ number_format($f['size'] / 1024, 2) }} KB</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $f['modified'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <x-button type="secondary" size="sm" tag="a" :href="route('admin.logs.show', $f['name'])">
                                    <x-fas-eye class="w-3"/>
                                </x-button>
                                <x-button type="secondary" size="sm" tag="a" :href="route('admin.logs.download', $f['name'])">
                                    <x-fas-download class="w-3"/>
                                </x-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <x-fas-file-alt class="w-12 h-12 mx-auto mb-4 opacity-50"/>
                                    <p class="text-lg font-medium">{{ __('Keine Logdateien gefunden') }}</p>
                                    <p class="text-sm mt-1">{{ __('Es konnten keine Logdateien im Verzeichnis storage/logs gefunden werden.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>

