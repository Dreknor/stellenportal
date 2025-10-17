<x-layouts.app>
    @php
        $crumbs = [
            ['label' =>  __('Profil'), 'url' => route('settings.profile.edit')],
            ['label' =>  __('Profil aktualisieren')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Profil') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Aktualisieren Sie Ihren Namen und Ihre E-Mail-Adresse') }}</p>
    </div>

    <div class="p-6">
        <div class="flex flex-col md:flex-row gap-6">
            @include('settings.partials.navigation')

            <div class="flex-1">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                    <div class="p-6">
                        <form class="max-w-md mb-10" action="{{ route('settings.profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-4">
                                <x-forms.input label="Vorname" name="first_name" type="text"
                                               value="{{ old('first_name', $user->first_name) }}" />
                            </div>
                            <div class="mb-4">
                                <x-forms.input label="Nachname" name="last_name" type="text"
                                               value="{{ old('last_name', $user->last_name) }}" />
                            </div>

                            <div class="mb-6">
                                <x-forms.input label="E-Mail" name="email" type="email"
                                               value="{{ old('email', $user->email) }}" />
                            </div>

                            <div>
                                <x-button type="primary">{{ __('Speichern') }}</x-button>
                            </div>
                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
