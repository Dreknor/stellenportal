<x-layouts.app>
    @php
        $crumbs = [
            ['label' =>  __('Träger'), 'url' => route('organizations.index')],
            ['label' =>  __('Übersicht')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Träger') }}</h1>


    @forelse($organizations as $organization)

        <div class="p-6">
            <x-organization.card :organization="$organization" :show_actions="!$isReadOnly" :editUrl="false" />
        </div>

    @empty
        <div class="p-6">
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                <div class="p-6">
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        {{ __('Es ist kein Träger mit Ihrem Konto verknüpft. Bitte legen Sie einen neuen Träger an') }}
                    </p>
                    <div class="mt-6">
                        <form class="max-w-3xl mb-10" action="{{ route('organizations.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <x-forms.input label="Name des Trägers" name="name" type="text"
                                               value="{{ old('name') }}" required/>
                            </div>

                            <x-forms.address />

                            <div class="mb-4">
                                <x-forms.input label="E-Mail des Träger" name="email" type="email"
                                               value="{{ old('email') }}" required />
                            </div>

                            <div class="mb-4">
                                <x-forms.input label="Telefon" name="telefon" type="text"
                                               value="{{ old('telefon') }}" />
                            </div>

                            <div class="mb-4">
                                <textarea name="description" rows="4" class="w-full px-4 py-1.5 rounded-lg text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Beschreibung (optional)">{{ old('description') }}</textarea>
                            </div>





                            <div>
                                <x-button type="primary">{{ __('Speichern') }}</x-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforelse

</x-layouts.app>
