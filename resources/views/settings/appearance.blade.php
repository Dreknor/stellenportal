<x-layouts.app>
    @php
        $crumbs = [
            ['label' =>  __('Profil'), 'url' => route('settings.profile.edit')],
            ['label' =>  __('Erscheinungsbild')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Erscheinungsbild') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">
            {{ __('Aktualisieren Sie die Einstellungen für das Erscheinungsbild Ihres Kontos') }}
        </p>
    </div>

    <div class="p-6">
        <div class="flex flex-col md:flex-row gap-6">
            @include('settings.partials.navigation')

            <div class="flex-1">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                    <div class="p-6">
                        <div class="mb-4">
                            <label for="theme"
                                   class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Theme') }}</label>
                            <div class="inline-flex rounded-md shadow-sm" role="group">
                                <button onclick="setAppearance('light')"
                                        class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-l-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
                                    {{ __('Hell') }}
                                </button>
                                <button onclick="setAppearance('dark')"
                                        class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
                                    {{ __('Dunkel') }}
                                </button>
                                <button onclick="setAppearance('system')"
                                        class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-r-md hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
                                    {{ __('System') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
