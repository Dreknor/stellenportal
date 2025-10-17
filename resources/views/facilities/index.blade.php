<x-layouts.app>
    @php
        $crumbs = [
            ['label' =>  __('Einrichtungen'), 'url' => route('facilities.index')],
            ['label' =>  __('Übersicht')],
        ];
        $userHasOrganizations = auth()->user()->organizations->isNotEmpty();
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Einrichtungen') }}</h1>
        @if($userHasOrganizations)
            <x-button type="primary" tag="a" :href="route('facilities.create')">
                <x-fas-plus class="w-3 mr-3"/>
                {{ __('Neue Einrichtung') }}
            </x-button>
        @endif
    </div>

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
                        @if($userHasOrganizations)
                            {{ __('Beginnen Sie, indem Sie Ihre erste Einrichtung erstellen.') }}
                        @else
                            {{ __('Sie müssen einer Organisation zugeordnet sein, um Einrichtungen anzulegen.') }}
                        @endif
                    </p>
                    @if($userHasOrganizations)
                        <div class="mt-6">
                            <x-button type="primary" tag="a" :href="route('facilities.create')">
                                <x-fas-plus  class="w-3 mr-3" />
                                {{ __('Erste Einrichtung erstellen') }}
                            </x-button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforelse
</x-layouts.app>
