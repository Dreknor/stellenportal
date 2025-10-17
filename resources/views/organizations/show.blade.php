<x-layouts.app>
    @php
        $crumbs = [
            ['label' =>  __('Träger'), 'url' => route('organizations.index')],
            ['label' =>  $organization->name],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $organization->name }}</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Organization Card -->
        <div>
            <x-organization.card :organization="$organization" :showActions="true" :editUrl="true" />
        </div>

        <!-- Credits Overview -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden h-fit">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                        {{ __('Guthaben') }}
                    </h2>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg p-6 mb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Aktueller Kontostand') }}</p>
                            <p class="text-4xl font-bold text-green-600 dark:text-green-400">
                                {{ number_format($organization->getCurrentCreditBalance(), 0, ',', '.') }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Guthaben') }}</p>
                        </div>
                        <div class="text-green-600 dark:text-green-400">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-2">
                    <x-button type="success" tag="a" :href="route('credits.organization.purchase', $organization)" size="sm" class="w-full justify-center">
                        <x-fas-plus class="w-4 h-4 mr-2" />
                        {{ __('Guthaben aufladen') }}
                    </x-button>
                    <x-button type="primary" tag="a" :href="route('credits.organization.transfer', $organization)" size="sm" class="w-full justify-center">
                        <x-fas-exchange-alt class="w-4 h-4 mr-2" />
                        {{ __('Guthaben umbuchen') }}
                    </x-button>
                    <x-button type="secondary" tag="a" :href="route('credits.organization.transactions', $organization)" size="sm" class="w-full justify-center">
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
                    <x-button type="primary" tag="a" :href="route('organizations.users.index', $organization)" size="sm">
                        {{ __('Verwalten') }}
                    </x-button>
                </div>

                @if($users->count() > 0)
                    <div class="space-y-3">
                        @foreach($users as $user)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200 flex items-center justify-center text-sm font-semibold">
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
                            <x-button type="primary" tag="a" :href="route('organizations.users.index', $organization)">
                                {{ __('Benutzer hinzufügen') }}
                            </x-button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Facilities Overview -->
    <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                    {{ __('Einrichtungen') }}
                    <span class="text-sm text-gray-500 dark:text-gray-400 font-normal ml-2">({{ $facilities->count() }})</span>
                </h2>
                <x-button type="success" tag="a" :href="route('facilities.create')" size="sm">
                    <x-fas-plus class="w-4 h-4 mr-1" />
                    {{ __('Neue Einrichtung') }}
                </x-button>
            </div>

            @if($facilities->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($facilities as $facility)
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $facility->name }}
                                    </h3>
                                    @if($facility->description)
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ \Illuminate\Support\Str::limit($facility->description, 100) }}
                                        </p>
                                    @endif
                                    @if($facility->address)
                                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                            <x-fas-map-marker-alt class="w-3 h-3 inline" />
                                            {{ $facility->address->city }}
                                        </p>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <x-button type="secondary" tag="a" :href="route('facilities.show', $facility)" size="sm">
                                        {{ __('Details') }}
                                    </x-button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Noch keine Einrichtungen vorhanden') }}
                    </p>
                    <div class="mt-4">
                        <x-button type="success" tag="a" :href="route('facilities.create')">
                            <x-fas-plus class="w-4 h-4 mr-1" />
                            {{ __('Erste Einrichtung erstellen') }}
                        </x-button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
