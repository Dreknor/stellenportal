<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Footer-Einstellungen'), 'url' => route('admin.footer-settings.index')],
            ['label' => __('Übersicht')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Footer-Einstellungen') }}</h1>
        @can('admin edit organizations')
            <x-button type="primary" tag="a" :href="route('admin.footer-settings.create')">
                <x-fas-plus class="w-4 mr-2"/>
                {{ __('Neue Footer-Einstellung') }}
            </x-button>
        @endcan
    </div>

    <!-- Footer Settings Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('ID') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Logo') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Inhalt') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Links') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Erstellt am') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Aktionen') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($footerSettings as $setting)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">#{{ $setting->id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($setting->logo_path)
                                    <img src="{{ $setting->logo_url }}" alt="Footer Logo" class="h-10 w-auto">
                                @else
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Kein Logo') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-gray-100 max-w-xs truncate">
                                    {{ $setting->content ? Str::limit($setting->content, 50) : '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ $setting->links ? count($setting->links) : 0 }} {{ __('Link(s)') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($setting->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                        <x-fas-check-circle class="w-3 mr-1"/>
                                        {{ __('Aktiv') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        <x-fas-circle class="w-3 mr-1"/>
                                        {{ __('Inaktiv') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $setting->created_at->format('d.m.Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    @can('admin edit organizations')
                                        @if(!$setting->is_active)
                                            <form method="POST" action="{{ route('admin.footer-settings.activate', $setting) }}" class="inline">
                                                @csrf
                                                <x-button type="success" size="sm" native-type="submit">
                                                    <x-fas-check class="w-3 mr-1"/>
                                                    {{ __('Aktivieren') }}
                                                </x-button>
                                            </form>
                                        @endif
                                        <x-button type="primary" size="sm" tag="a" :href="route('admin.footer-settings.edit', $setting)">
                                            <x-fas-edit class="w-3"/>
                                        </x-button>
                                    @endcan
                                    @can('admin delete organizations')
                                        <form method="POST" action="{{ route('admin.footer-settings.destroy', $setting) }}" class="inline"
                                              onsubmit="return confirm('{{ __('Möchten Sie diese Footer-Einstellung wirklich löschen?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <x-button type="danger" size="sm" native-type="submit">
                                                <x-fas-trash class="w-3"/>
                                            </x-button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <x-fas-inbox class="w-12 h-12 mx-auto mb-4 opacity-50"/>
                                    <p class="text-lg font-medium">{{ __('Keine Footer-Einstellungen vorhanden') }}</p>
                                    <p class="text-sm mt-2">{{ __('Erstellen Sie eine neue Footer-Einstellung.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($footerSettings->hasPages())
            <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $footerSettings->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>

