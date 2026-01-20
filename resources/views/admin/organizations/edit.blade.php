<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Organisationen'), 'url' => route('admin.organizations.index')],
            ['label' => $organization->name, 'url' => route('admin.organizations.show', $organization)],
            ['label' => __('Bearbeiten')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Organisation bearbeiten') }}</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('admin.organizations.update', $organization) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Name') }}</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $organization->name) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('E-Mail') }}</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $organization->email) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Telefon') }}</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $organization->phone) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="website" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Website') }}</label>
                    <input type="url" name="website" id="website" value="{{ old('website', $organization->website) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="https://beispiel.de">
                    @error('website')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Beschreibung') }}</label>
                    <textarea name="description" id="description" rows="4"
                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $organization->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Optionale Beschreibung der Organisation') }}</p>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Bilder') }}</h3>

                    <div class="mb-4">
                        <label for="header_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Headerbild') }}
                        </label>
                        @if($organization->getFirstMediaUrl('header_image'))
                            <div class="mb-4">
                                <img src="{{ $organization->getFirstMediaUrl('header_image') }}" alt="Header Image" class="max-w-md rounded-lg shadow-md">
                                <div class="mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="remove_header_image" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Headerbild entfernen') }}</span>
                                    </label>
                                </div>
                            </div>
                        @endif
                        <input type="file" name="header_image" id="header_image" accept="image/jpeg,image/png,image/jpg,image/webp"
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-300">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Großes Bild für die Anzeige bei Stellenausschreibungen. Erlaubte Formate: JPG, PNG, WEBP. Maximale Größe: 5 MB') }}</p>
                        @error('header_image')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Logo') }}
                        </label>
                        @if($organization->getFirstMediaUrl('logo'))
                            <div class="mb-4">
                                <img src="{{ $organization->getFirstMediaUrl('logo') }}" alt="Logo" class="max-w-xs rounded-lg shadow-md">
                                <div class="mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="remove_logo" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Logo entfernen') }}</span>
                                    </label>
                                </div>
                            </div>
                        @endif
                        <input type="file" name="logo" id="logo" accept="image/jpeg,image/png,image/jpg,image/webp"
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-300">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Logo als Icon für die Anzeige. Erlaubte Formate: JPG, PNG, WEBP. Maximale Größe: 5 MB') }}</p>
                        @error('logo')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Mitgliedschaft') }}</h3>
                    <div>
                        <label class="flex items-start">
                            <input type="checkbox" name="is_cooperative_member" value="1" {{ old('is_cooperative_member', $organization->is_cooperative_member) ? 'checked' : '' }}
                                   class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <div class="ml-3">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Genossenschaftsmitglied') }}</span>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Wenn aktiviert, hat diese Organisation Zugang zu speziellen Guthaben-Paketen für Genossenschaftsmitglieder.') }}</p>
                            </div>
                        </label>
                        @error('is_cooperative_member')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Informationen') }}</h3>
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <x-fas-info-circle class="h-5 w-5 text-blue-600 dark:text-blue-400"/>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-800 dark:text-blue-200">
                                    {{ __('Der Slug wird automatisch aus dem Namen generiert und kann nicht manuell geändert werden.') }}
                                </p>
                                <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                                    {{ __('Aktueller Slug:') }} <span class="font-mono">{{ $organization->slug }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2">
                    <x-button type="primary" native-type="submit">
                        <x-fas-save class="w-3 mr-2"/>
                        {{ __('Speichern') }}
                    </x-button>
                    <x-button type="secondary" tag="a" :href="route('admin.organizations.show', $organization)">
                        {{ __('Abbrechen') }}
                    </x-button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.app>

