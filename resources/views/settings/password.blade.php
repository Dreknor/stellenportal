<x-layouts.app>
    @php
        $crumbs = [
            ['label' =>  __('Profil'), 'url' => route('settings.profile.edit')],
            ['label' =>  __('Passwort')]
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>


    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Passwort aktualisieren') }}</h1>
        @if(auth()->user()->change_password)
            <x-alerts.infobox class="mt-3" type="warning">
                {{ __('Ihr Passwort muss geändert werden, bevor Sie fortfahren können.') }}
            </x-alerts.infobox>
        @endif
        <p class="text-gray-600 dark:text-gray-400 mt-1">
            {{ __('Stellen Sie sicher, dass Ihr Konto ein langes, zufälliges Passwort verwendet, um sicher zu bleiben.') }}
        </p>
    </div>

    <div class="p-6">
        <div class="flex flex-col md:flex-row gap-6">
            @include('settings.partials.navigation')

            <div class="flex-1">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                    <div class="p-6">
                        <form class="max-w-md mb-10" action="{{ route('settings.password.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-4">
                                <x-forms.input label="Aktuelles Passwort" name="current_password" type="password" />
                            </div>

                            <div class="mb-6">
                                <x-forms.input label="Neues Passwort" name="password" type="password" />
                            </div>

                            <div class="mb-6">
                                <x-forms.input label="Passwort bestätigen" name="password_confirmation" type="password" />
                            </div>

                            <div>
                                <x-button type="primary">{{ __('Passwort aktualisieren') }}</x-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
