<x-layouts.app>
    @php
        $crumbs = [
            ['label' =>  __('Einrichtungen'), 'url' => route('facilities.index')],
            ['label' =>  __('Neue Einrichtung erstellen')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <x-help-link section="facilities" />

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Neue Einrichtung erstellen') }}</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <form class="max-w-3xl" action="{{ route('facilities.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Träger') }} <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="organization_id"
                        required
                        class="w-full px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">{{ __('Bitte wählen...') }}</option>
                        @foreach($organizations as $organization)
                            <option value="{{ $organization->id }}" {{ old('organization_id') == $organization->id ? 'selected' : '' }}>
                                {{ $organization->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('organization_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <x-forms.input
                        label="Name der Einrichtung"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        required
                    />
                </div>

                <x-forms.address />

                <div class="mb-4">
                    <x-forms.input
                        label="E-Mail der Einrichtung"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                    />
                </div>

                <div class="mb-4">
                    <x-forms.input
                        label="Telefon"
                        name="phone"
                        type="text"
                        value="{{ old('phone') }}"
                    />
                </div>

                <div class="mb-4">
                    <x-forms.input
                        label="Website"
                        name="website"
                        type="url"
                        value="{{ old('website') }}"
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
                        placeholder="{{ __('Beschreibung (optional)') }}"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <x-forms.header-image-upload />

                <x-forms.logo-upload />

                <div class="flex gap-4">
                    <x-button type="primary">{{ __('Speichern') }}</x-button>
                    <x-button type="secondary" tag="a" :href="route('facilities.index')">
                        {{ __('Abbrechen') }}
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>

