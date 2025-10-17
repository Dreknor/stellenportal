<x-layouts.app>
    @php
        $crumbs = [
            ['label' =>  __('Träger'), 'url' => route('organizations.index')],
            ['label' =>  $organization->name, 'url' => route('organizations.show', $organization)],
            ['label' =>  __('Bearbeiten')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Träger bearbeiten') }}</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <form class="max-w-3xl" action="{{ route('organizations.update', $organization) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <x-forms.input
                        label="Name des Trägers"
                        name="name"
                        type="text"
                        value="{{ old('name', $organization->name) }}"
                        required
                    />
                </div>

                <x-forms.address :address="$organization->address" />

                <div class="mb-4">
                    <x-forms.input
                        label="E-Mail des Trägers"
                        name="email"
                        type="email"
                        value="{{ old('email', $organization->email) }}"
                    />
                </div>

                <div class="mb-4">
                    <x-forms.input
                        label="Telefon"
                        name="phone"
                        type="text"
                        value="{{ old('phone', $organization->phone) }}"
                    />
                </div>

                <div class="mb-4">
                    <x-forms.input
                        label="Website"
                        name="website"
                        type="url"
                        value="{{ old('website', $organization->website) }}"
                    />
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Beschreibung') }}
                    </label>
                    <textarea
                        name="description"
                        rows="4"
                        class="w-full px-4 py-1.5 rounded-lg text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >{{ old('description', $organization->description) }}</textarea>
                </div>

                <x-forms.header-image-upload :model="$organization" />

                <div class="flex gap-4">
                    <x-button type="primary">{{ __('Speichern') }}</x-button>
                    <x-button type="secondary" tag="a" :href="route('organizations.show', $organization)">
                        {{ __('Abbrechen') }}
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
