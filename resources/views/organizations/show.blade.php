<x-layouts.app>
    @php
        $crumbs = [
            ['label' =>  __('Träger'), 'url' => route('organizations.index')],
            ['label' =>  $organization->name],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <x-help-link section="organizations" />

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $organization->name }}</h1>
    </div>

    @if(!$organization->is_approved)
        <div class="mb-6 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700 dark:text-yellow-200">
                        <strong>{{ __('Organisation wartet auf Bestätigung') }}</strong><br>
                        {{ __('Diese Organisation muss erst vom Administrator genehmigt werden, bevor Sie Credits kaufen, Einrichtungen erstellen, Stellenausschreibungen veröffentlichen oder Benutzer verwalten können.') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

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

                    @if($organization->is_approved)
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
                    @else
                        <button disabled class="w-full px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 rounded-lg text-sm cursor-not-allowed">
                            <x-fas-plus class="w-4 h-4 mr-2 inline" />
                            {{ __('Guthaben aufladen') }}
                        </button>
                        <button disabled class="w-full px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 rounded-lg text-sm cursor-not-allowed">
                            <x-fas-exchange-alt class="w-4 h-4 mr-2 inline" />
                            {{ __('Guthaben umbuchen') }}
                        </button>
                        <button disabled class="w-full px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 rounded-lg text-sm cursor-not-allowed">
                            <x-fas-list class="w-4 h-4 mr-2 inline" />
                            {{ __('Transaktionshistorie') }}
                        </button>
                    @endif
                            </svg>
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
                    @if($organization->is_approved)
                        <x-button type="primary" tag="a" :href="route('organizations.users.index', $organization)" size="sm">
                            {{ __('Verwalten') }}
                        </x-button>
                    @else
                        <button disabled class="px-3 py-1.5 bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 rounded-lg text-sm cursor-not-allowed">
                            {{ __('Verwalten') }}
                        </button>
                    @endif
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
                        @if($organization->is_approved)
                            <div class="mt-4">
                                <x-button type="primary" tag="a" :href="route('organizations.users.index', $organization)">
                                    {{ __('Benutzer hinzufügen') }}
                                </x-button>
                            </div>
                        @endif
                    </div>
                @endif
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
                @if($organization->is_approved)
                    <x-button type="success" tag="a" :href="route('facilities.create')" size="sm">
                        <x-fas-plus class="w-4 h-4 mr-1" />
                        {{ __('Neue Einrichtung') }}
                    </x-button>
                @else
                    <button disabled class="px-3 py-1.5 bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 rounded-lg text-sm cursor-not-allowed flex items-center">
                        <x-fas-plus class="w-4 h-4 mr-1" />
                        {{ __('Neue Einrichtung') }}
                    </button>
                @endif
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
                    @if($organization->is_approved)
                        <div class="mt-4">
                            <x-button type="success" tag="a" :href="route('facilities.create')">
                                <x-fas-plus class="w-4 h-4 mr-1" />
                                {{ __('Erste Einrichtung erstellen') }}
                            </x-button>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
