<x-layouts.app>
    @php
        $crumbs = [
            ['label' =>  __('Einrichtungen'), 'url' => route('facilities.index')],
            ['label' =>  __('Übersicht')],
        ];
        $userHasOrganizations = auth()->user()->organizations->isNotEmpty();
        $userHasApprovedOrganizations = auth()->user()->organizations()->where('is_approved', true)->exists();
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Einrichtungen') }}</h1>
        @if($userHasApprovedOrganizations)
            <x-button type="primary" tag="a" :href="route('facilities.create')">
                <x-fas-plus class="w-3 mr-3"/>
                {{ __('Neue Einrichtung') }}
            </x-button>
        @endif
    </div>

    @if($userHasOrganizations && !$userHasApprovedOrganizations)
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
                        {{ __('Ihre Organisation(en) müssen erst vom Administrator genehmigt werden, bevor Sie Einrichtungen erstellen können.') }}
                        <a href="{{ route('organizations.index') }}" class="underline hover:text-yellow-800 dark:hover:text-yellow-100">
                            {{ __('Zu den Organisationen') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    @endif

    @forelse($facilities as $facility)
        <div class="mb-6">
            <x-facility.card :facility="$facility" :show_actions="true" :editUrl="false" />
        </div>
    @empty
        <div class="p-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Keine Einrichtungen vorhanden') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        @if($userHasApprovedOrganizations)
                            {{ __('Beginnen Sie, indem Sie Ihre erste Einrichtung erstellen.') }}
                        @elseif($userHasOrganizations)
                            {{ __('Ihre Organisation(en) müssen erst vom Administrator genehmigt werden, bevor Sie Einrichtungen anlegen können.') }}
                        @else
                            {{ __('Sie müssen einer Organisation zugeordnet sein, um Einrichtungen anzulegen.') }}
                        @endif
                    </p>
                    @if($userHasApprovedOrganizations)
                        <div class="mt-6">
                            <x-button type="primary" tag="a" :href="route('facilities.create')">
                                <x-fas-plus  class="w-3 mr-3" />
                                {{ __('Erste Einrichtung erstellen') }}
                            </x-button>
                        </div>
                    @elseif($userHasOrganizations)
                        <div class="mt-6">
                            <x-button type="secondary" tag="a" :href="route('organizations.index')">
                                {{ __('Zu den Organisationen') }}
                            </x-button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforelse
</x-layouts.app>
