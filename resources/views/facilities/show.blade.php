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
