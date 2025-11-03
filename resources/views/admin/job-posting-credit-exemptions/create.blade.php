<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Guthabenausnahmen'), 'url' => route('admin.job-posting-credit-exemptions.index')],
            ['label' => __('Neue Ausnahme erstellen')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Neue Guthabenausnahme erstellen') }}</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 max-w-2xl">
        <form method="POST" action="{{ route('admin.job-posting-credit-exemptions.store') }}">
            @csrf

            <div class="space-y-6">
                <!-- Employment Type -->
                <div>
                    <label for="employment_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Beschäftigungsart') }} <span class="text-red-500">*</span>
                    </label>
                    <select id="employment_type" name="employment_type" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">{{ __('Bitte wählen...') }}</option>
                        @foreach($employmentTypes as $value => $label)
                            <option value="{{ $value }}" {{ old('employment_type') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('employment_type')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Applies To -->
                <div>
                    <label for="applies_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Gilt für') }} <span class="text-red-500">*</span>
                    </label>
                    <select id="applies_to" name="applies_to" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">{{ __('Bitte wählen...') }}</option>
                        @foreach($appliesTo as $value => $label)
                            <option value="{{ $value }}" {{ old('applies_to') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('applies_to')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Is Active -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="is_active" class="font-medium text-gray-700 dark:text-gray-300">
                            {{ __('Ausnahme ist aktiv') }}
                        </label>
                        <p class="text-gray-500 dark:text-gray-400">
                            {{ __('Wenn aktiviert, wird diese Ausnahme sofort angewendet.') }}
                        </p>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Beschreibung') }}
                    </label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="{{ __('Optionale Beschreibung für interne Notizen...') }}">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Eine kurze Erläuterung, warum diese Ausnahme erstellt wurde.') }}
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <x-button type="secondary" tag="a" :href="route('admin.job-posting-credit-exemptions.index')">
                        {{ __('Abbrechen') }}
                    </x-button>
                    <x-button type="primary" submit>
                        <x-fas-save class="w-4 h-4 mr-2"/>
                        {{ __('Ausnahme erstellen') }}
                    </x-button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.app>

