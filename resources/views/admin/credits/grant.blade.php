<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Credits'), 'url' => route('admin.credits.index')],
            ['label' => __('Guthaben gewähren')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Guthaben gewähren') }}</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('admin.credits.grant.store') }}">
            @csrf

            <div class="space-y-6">
                <div>
                    <label for="target_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Zieltyp') }}</label>
                    <select name="target_type" id="target_type" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            onchange="updateTargetOptions()">
                        <option value="">{{ __('Bitte wählen') }}</option>
                        <option value="organization" {{ old('target_type') === 'organization' ? 'selected' : '' }}>{{ __('Organisation') }}</option>
                        <option value="facility" {{ old('target_type') === 'facility' ? 'selected' : '' }}>{{ __('Einrichtung') }}</option>
                    </select>
                    @error('target_type')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div id="organization-select" style="display: none;">
                    <label for="organization_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Organisation') }}</label>
                    <select name="organization_id" id="organization_id"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">{{ __('Bitte wählen') }}</option>
                        @foreach($organizations as $organization)
                            <option value="{{ $organization->id }}" {{ old('target_id') == $organization->id ? 'selected' : '' }}>
                                {{ $organization->name }} ({{ $organization->creditBalance->balance ?? 0 }} Credits)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="facility-select" style="display: none;">
                    <label for="facility_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Einrichtung') }}</label>
                    <select name="facility_id" id="facility_id"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">{{ __('Bitte wählen') }}</option>
                        @foreach($facilities as $facility)
                            <option value="{{ $facility->id }}" {{ old('target_id') == $facility->id ? 'selected' : '' }}>
                                {{ $facility->name }} - {{ $facility->organization->name }} ({{ $facility->creditBalance->balance ?? 0 }} Credits)
                            </option>
                        @endforeach
                    </select>
                </div>

                <input type="hidden" name="target_id" id="target_id" value="{{ old('target_id') }}">

                @error('target_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror

                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Anzahl Credits') }}</label>
                    <input type="number" name="amount" id="amount" value="{{ old('amount') }}" required min="1"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Beschreibung') }}</label>
                    <textarea name="description" id="description" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="{{ __('Optional: Grund für die Gutschrift...') }}">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-2">
                    <x-button type="primary" native-type="submit">
                        <x-fas-check class="w-3 mr-2"/>
                        {{ __('Guthaben gewähren') }}
                    </x-button>
                    <x-button type="secondary" tag="a" :href="route('admin.credits.index')">
                        {{ __('Abbrechen') }}
                    </x-button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function updateTargetOptions() {
            const targetType = document.getElementById('target_type').value;
            const orgSelect = document.getElementById('organization-select');
            const facSelect = document.getElementById('facility-select');
            const orgInput = document.getElementById('organization_id');
            const facInput = document.getElementById('facility_id');
            const targetIdInput = document.getElementById('target_id');

            orgSelect.style.display = 'none';
            facSelect.style.display = 'none';
            orgInput.removeAttribute('required');
            facInput.removeAttribute('required');

            if (targetType === 'organization') {
                orgSelect.style.display = 'block';
                orgInput.setAttribute('required', 'required');
                orgInput.addEventListener('change', function() {
                    targetIdInput.value = this.value;
                });
            } else if (targetType === 'facility') {
                facSelect.style.display = 'block';
                facInput.setAttribute('required', 'required');
                facInput.addEventListener('change', function() {
                    targetIdInput.value = this.value;
                });
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateTargetOptions();
            const oldTargetType = '{{ old('target_type') }}';
            if (oldTargetType) {
                document.getElementById('target_type').value = oldTargetType;
                updateTargetOptions();
            }
        });
    </script>
</x-layouts.app>

