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
    </div>

    @forelse($organizations as $organization)
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
                            {{ __('Die Organisation "' . $organization->name . '" muss erst vom Administrator genehmigt werden, bevor Sie Credits kaufen, Einrichtungen erstellen, Stellenausschreibungen veröffentlichen oder Benutzer verwalten können.') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

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
                        <form class="max-w-3xl mb-10" action="{{ route('organizations.store') }}" method="POST" enctype="multipart/form-data">
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

                            <x-forms.header-image-upload />

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
