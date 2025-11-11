<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Credits'), 'url' => route('admin.credits.index')],
            ['label' => __('Guthaben entziehen')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Guthaben entziehen') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Guthaben für unbezahlte Rechnungen oder Stornierungen entziehen') }}</p>
    </div>

    <!-- Warning Box -->
    <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 mb-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700 dark:text-red-300 font-medium">
                    {{ __('Achtung: Diese Aktion kann nicht rückgängig gemacht werden!') }}
                </p>
                <p class="text-sm text-red-600 dark:text-red-400 mt-1">
                    {{ __('Stellen Sie sicher, dass der Grund für den Entzug dokumentiert ist.') }}
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('admin.credits.revoke.store') }}" onsubmit="return confirm('Sind Sie sicher, dass Sie Guthaben entziehen möchten? Diese Aktion kann nicht rückgängig gemacht werden.');">
            @csrf

            <div class="space-y-6">
                <div>
                    <label for="target_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Zieltyp') }}</label>
                    <select name="target_type" id="target_type" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-red-500 focus:ring-red-500"
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
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-red-500 focus:ring-red-500">
                        <option value="">{{ __('Bitte wählen') }}</option>
                        @foreach($organizations as $organization)
                            <option value="{{ $organization->id }}" data-balance="{{ $organization->creditBalance->balance ?? 0 }}" {{ old('target_id') == $organization->id ? 'selected' : '' }}>
                                {{ $organization->name }} ({{ $organization->creditBalance->balance ?? 0 }} Credits verfügbar)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="facility-select" style="display: none;">
                    <label for="facility_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Einrichtung') }}</label>
                    <select name="facility_id" id="facility_id"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-red-500 focus:ring-red-500">
                        <option value="">{{ __('Bitte wählen') }}</option>
                        @foreach($facilities as $facility)
                            <option value="{{ $facility->id }}" data-balance="{{ $facility->creditBalance->balance ?? 0 }}" {{ old('target_id') == $facility->id ? 'selected' : '' }}>
                                {{ $facility->name }} - {{ $facility->organization->name }} ({{ $facility->creditBalance->balance ?? 0 }} Credits verfügbar)
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
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-red-500 focus:ring-red-500"
                           onchange="checkBalance()">
                    <p id="balance-warning" class="mt-1 text-sm text-red-600 dark:text-red-400" style="display: none;">
                        {{ __('Achtung: Der eingegebene Betrag überschreitet das verfügbare Guthaben!') }}
                    </p>
                    <p id="balance-info" class="mt-1 text-sm text-gray-500 dark:text-gray-400" style="display: none;"></p>
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('Grund für den Entzug') }} <span class="text-red-600">*</span>
                    </label>
                    <textarea name="reason" id="reason" rows="3" required
                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-red-500 focus:ring-red-500"
                              placeholder="{{ __('z.B. Rechnung #12345 nicht bezahlt, Stornierung von Paket XYZ, etc.') }}">{{ old('reason') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Bitte geben Sie einen detaillierten Grund an. Diese Information wird in der Transaktionshistorie gespeichert.') }}
                    </p>
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-2">
                    <x-button type="danger" native-type="submit">
                        <x-fas-minus-circle class="w-3 mr-2"/>
                        {{ __('Guthaben entziehen') }}
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
                    checkBalance();
                });
            } else if (targetType === 'facility') {
                facSelect.style.display = 'block';
                facInput.setAttribute('required', 'required');
                facInput.addEventListener('change', function() {
                    targetIdInput.value = this.value;
                    checkBalance();
                });
            }
        }

        function checkBalance() {
            const targetType = document.getElementById('target_type').value;
            const amount = parseInt(document.getElementById('amount').value) || 0;
            const balanceWarning = document.getElementById('balance-warning');
            const balanceInfo = document.getElementById('balance-info');
            let balance = 0;

            if (targetType === 'organization') {
                const selectedOption = document.getElementById('organization_id').selectedOptions[0];
                balance = parseInt(selectedOption?.dataset?.balance || 0);
            } else if (targetType === 'facility') {
                const selectedOption = document.getElementById('facility_id').selectedOptions[0];
                balance = parseInt(selectedOption?.dataset?.balance || 0);
            }

            if (amount > 0 && balance > 0) {
                balanceInfo.textContent = `Verfügbares Guthaben: ${balance} Credits. Nach Entzug: ${balance - amount} Credits.`;
                balanceInfo.style.display = 'block';

                if (amount > balance) {
                    balanceWarning.style.display = 'block';
                } else {
                    balanceWarning.style.display = 'none';
                }
            } else {
                balanceWarning.style.display = 'none';
                balanceInfo.style.display = 'none';
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

