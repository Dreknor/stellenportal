<x-layouts.app>
    @php
        $crumbs = [
            ['label' =>  __('Einrichtungen'), 'url' => route('facilities.index')],
            ['label' =>  $facility->name],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $facility->name }}</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Facility Card -->
        <div>
            <x-facility.card :facility="$facility" :showActions="true" :editUrl="true" />
        </div>

        <!-- Credits Overview -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden h-fit mb-6">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                        {{ __('Guthaben') }}
                    </h2>
                </div>

                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-6 mb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Aktueller Kontostand') }}</p>
                            <p class="text-4xl font-bold text-blue-600 dark:text-blue-400">
                                {{ number_format($facility->getCurrentCreditBalance(), 0, ',', '.') }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Guthaben') }}</p>
                        </div>
                        <div class="text-blue-600 dark:text-blue-400">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-2">
                    <x-button type="success" tag="a" :href="route('credits.facility.purchase', $facility)" size="sm" class="w-full justify-center">
                        <x-fas-plus class="w-4 h-4 mr-2" />
                        {{ __('Guthaben aufladen') }}
                    </x-button>
                    <x-button type="secondary" tag="a" :href="route('credits.facility.transactions', $facility)" size="sm" class="w-full justify-center">
                        <x-fas-list class="w-4 h-4 mr-2" />
                        {{ __('Transaktionshistorie') }}
                    </x-button>
                </div>
            </div>
        </div>

        <!-- Users Overview -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden h-fit">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                        {{ __('Zugeordnete Benutzer') }}
                        <span class="text-sm text-gray-500 dark:text-gray-400 font-normal ml-2">({{ $users->count() }})</span>
                    </h2>
                    <x-button type="primary" tag="a" :href="route('facilities.users.index', $facility)" size="sm">
                        {{ __('Verwalten') }}
                    </x-button>
                </div>

                @if($users->count() > 0)
                    <div class="space-y-3">
                        @foreach($users as $user)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200 flex items-center justify-center text-sm font-semibold">
                                            {{ $user->initials() }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $user->name }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $user->email }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Gesamt:') }} {{ $users->count() }} {{ $users->count() === 1 ? __('Benutzer') : __('Benutzer') }}
                            </span>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Noch keine Benutzer zugeordnet') }}
                        </p>
                        <div class="mt-4">
                            <x-button type="primary" tag="a" :href="route('facilities.users.index', $facility)">
                                {{ __('Benutzer hinzufügen') }}
                            </x-button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
