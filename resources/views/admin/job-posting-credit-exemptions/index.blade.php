<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Guthabenausnahmen')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Guthabenausnahmen für Stellenausschreibungen') }}</h1>
        @can('admin grant credits')
            <x-button type="primary" tag="a" :href="route('admin.job-posting-credit-exemptions.create')">
                <x-fas-plus class="w-3 mr-3"/>
                {{ __('Neue Ausnahme erstellen') }}
            </x-button>
        @endcan
    </div>

    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <x-fas-info-circle class="h-5 w-5 text-blue-400"/>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                    {{ __('Was sind Guthabenausnahmen?') }}
                </h3>
                <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                    <p>{{ __('Guthabenausnahmen ermöglichen es, für bestimmte Beschäftigungsarten keine Credits für Veröffentlichung oder Verlängerung von Stellenausschreibungen zu verlangen. Sie können festlegen, ob die Ausnahme für alle Organisationen oder nur für Genossenschaftsmitglieder gilt.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Beschäftigungsart') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Gilt für') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Beschreibung') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Aktionen') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($exemptions as $exemption)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $exemption->getEmploymentTypeLabel() }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ $exemption->getAppliesToLabel() }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($exemption->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                        {{ __('Aktiv') }}
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        {{ __('Inaktiv') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">
                                    {{ $exemption->description ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                @can('admin grant credits')
                                    <form method="POST" action="{{ route('admin.job-posting-credit-exemptions.toggle', $exemption) }}" class="inline">
                                        @csrf
                                        <x-button type="{{ $exemption->is_active ? 'warning' : 'success' }}" size="sm">
                                            @if($exemption->is_active)
                                                <x-fas-pause class="w-3"/>
                                            @else
                                                <x-fas-play class="w-3"/>
                                            @endif
                                        </x-button>
                                    </form>
                                    <x-button type="secondary" size="sm" tag="a" :href="route('admin.job-posting-credit-exemptions.edit', $exemption)">
                                        <x-fas-edit class="w-3"/>
                                    </x-button>
                                    <form method="POST" action="{{ route('admin.job-posting-credit-exemptions.destroy', $exemption) }}" class="inline" onsubmit="return confirm('{{ __('Sind Sie sicher, dass Sie diese Ausnahme löschen möchten?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <x-button type="danger" size="sm">
                                            <x-fas-trash class="w-3"/>
                                        </x-button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                <x-fas-inbox class="mx-auto h-12 w-12 text-gray-400 mb-4"/>
                                <p class="font-medium">{{ __('Keine Guthabenausnahmen vorhanden') }}</p>
                                <p class="mt-1">{{ __('Erstellen Sie eine neue Ausnahme, um bestimmte Beschäftigungsarten von der Guthabenpflicht zu befreien.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($exemptions->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $exemptions->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>

