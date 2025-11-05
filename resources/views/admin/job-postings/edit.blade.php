<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Stellenausschreibungen'), 'url' => route('admin.job-postings.index')],
            ['label' => $jobPosting->title, 'url' => route('admin.job-postings.show', $jobPosting)],
            ['label' => __('Bearbeiten')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Stellenausschreibung bearbeiten') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Bearbeiten Sie die Details dieser Stellenausschreibung') }}</p>
    </div>

    @if(session('success'))
        <x-alerts.alert type="success" class="mb-6">
            {{ session('success') }}
        </x-alerts.alert>
    @endif

    @if(session('error'))
        <x-alerts.alert type="error" class="mb-6">
            {{ session('error') }}
        </x-alerts.alert>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('admin.job-postings.update', $jobPosting) }}">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Facility (read-only) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Einrichtung') }}
                    </label>
                    <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center">
                            <x-fas-building class="w-4 h-4 mr-2 text-gray-500 dark:text-gray-400" />
                            <span class="text-gray-800 dark:text-gray-100 font-medium">{{ $jobPosting->facility->name }}</span>
                            @if($jobPosting->facility->organization)
                                <span class="text-gray-500 dark:text-gray-400 ml-2">
                                    ({{ $jobPosting->facility->organization->name }})
                                </span>
                            @endif
                        </div>
                        @if($jobPosting->facility->address)
                            <div class="flex items-center mt-1 text-sm text-gray-600 dark:text-gray-400">
                                <x-fas-map-marker-alt class="w-3 h-3 mr-2" />
                                <span>{{ $jobPosting->facility->address->street }} {{ $jobPosting->facility->address->number }}, {{ $jobPosting->facility->address->zip_code }} {{ $jobPosting->facility->address->city }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Stellentitel') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title', $jobPosting->title) }}" required
                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-green-500 dark:focus:ring-green-600 @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Employment Type -->
                <div>
                    <label for="employment_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Beschäftigungsart') }} <span class="text-red-500">*</span>
                    </label>
                    <select id="employment_type" name="employment_type" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-green-500 dark:focus:ring-green-600 @error('employment_type') border-red-500 @enderror">
                        <option value="full_time" {{ old('employment_type', $jobPosting->employment_type) === 'full_time' ? 'selected' : '' }}>{{ __('Vollzeit') }}</option>
                        <option value="part_time" {{ old('employment_type', $jobPosting->employment_type) === 'part_time' ? 'selected' : '' }}>{{ __('Teilzeit') }}</option>
                        <option value="mini_job" {{ old('employment_type', $jobPosting->employment_type) === 'mini_job' ? 'selected' : '' }}>{{ __('Minijob') }}</option>
                        <option value="internship" {{ old('employment_type', $jobPosting->employment_type) === 'internship' ? 'selected' : '' }}>{{ __('Praktikum') }}</option>
                        <option value="apprenticeship" {{ old('employment_type', $jobPosting->employment_type) === 'apprenticeship' ? 'selected' : '' }}>{{ __('Ausbildung') }}</option>
                        <option value="volunteer" {{ old('employment_type', $jobPosting->employment_type) === 'volunteer' ? 'selected' : '' }}>{{ __('Ehrenamt') }}</option>
                    </select>
                    @error('employment_type')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Stellenbeschreibung') }} <span class="text-red-500">*</span>
                    </label>
                    <textarea id="description" name="description" rows="6" required
                              class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-green-500 dark:focus:ring-green-600 @error('description') border-red-500 @enderror">{{ old('description', $jobPosting->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Requirements -->
                <div>
                    <label for="requirements" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Anforderungen') }}
                    </label>
                    <textarea id="requirements" name="requirements" rows="4"
                              class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-green-500 dark:focus:ring-green-600 @error('requirements') border-red-500 @enderror">{{ old('requirements', $jobPosting->requirements) }}</textarea>
                    @error('requirements')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Benefits -->
                <div>
                    <label for="benefits" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Wir bieten') }}
                    </label>
                    <textarea id="benefits" name="benefits" rows="4"
                              class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-green-500 dark:focus:ring-green-600 @error('benefits') border-red-500 @enderror">{{ old('benefits', $jobPosting->benefits) }}</textarea>
                    @error('benefits')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Salary Range -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Gehaltsinformationen') }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="salary_min" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Mindestgehalt (€)') }}
                            </label>
                            <input type="number" id="salary_min" name="salary_min" value="{{ old('salary_min', $jobPosting->salary_min) }}" step="0.01" min="0"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-green-500 dark:focus:ring-green-600 @error('salary_min') border-red-500 @enderror">
                            @error('salary_min')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="salary_max" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Höchstgehalt (€)') }}
                            </label>
                            <input type="number" id="salary_max" name="salary_max" value="{{ old('salary_max', $jobPosting->salary_max) }}" step="0.01" min="0"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-green-500 dark:focus:ring-green-600 @error('salary_max') border-red-500 @enderror">
                            @error('salary_max')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Kontaktinformationen') }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Kontakt E-Mail') }}
                            </label>
                            <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email', $jobPosting->contact_email) }}"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-green-500 dark:focus:ring-green-600 @error('contact_email') border-red-500 @enderror">
                            @error('contact_email')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="contact_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Kontakt Telefon') }}
                            </label>
                            <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $jobPosting->contact_phone) }}"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-green-500 dark:focus:ring-green-600 @error('contact_phone') border-red-500 @enderror">
                            @error('contact_phone')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <span class="font-medium">{{ __('Status:') }}</span>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-{{ $jobPosting->getStatusColor() }}-100 text-{{ $jobPosting->getStatusColor() }}-800 dark:bg-{{ $jobPosting->getStatusColor() }}-900/30 dark:text-{{ $jobPosting->getStatusColor() }}-300">
                            {{ $jobPosting->getStatusLabel() }}
                        </span>
                    </div>

                    <div class="flex gap-4">
                        <x-button tag="a" :href="route('admin.job-postings.show', $jobPosting)" type="secondary">
                            {{ __('Abbrechen') }}
                        </x-button>
                        <x-button type="primary">
                            <x-fas-save class="w-4 h-4 mr-2" />
                            {{ __('Änderungen speichern') }}
                        </x-button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Additional Admin Info -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Zusätzliche Informationen') }}</h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Erstellt am') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $jobPosting->created_at->format('d.m.Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Zuletzt aktualisiert') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $jobPosting->updated_at->format('d.m.Y H:i') }}</dd>
                </div>
                @if($jobPosting->published_at)
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Veröffentlicht am') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $jobPosting->published_at->format('d.m.Y H:i') }}</dd>
                </div>
                @endif
                @if($jobPosting->expires_at)
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Läuft ab am') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $jobPosting->expires_at->format('d.m.Y H:i') }}</dd>
                </div>
                @endif
            </dl>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Schnellaktionen') }}</h3>
            <div class="space-y-3">
                <x-button tag="a" :href="route('public.jobs.show', $jobPosting)" type="secondary" class="w-full justify-center" target="_blank">
                    <x-fas-external-link-alt class="w-4 h-4 mr-2" />
                    {{ __('Öffentliche Ansicht') }}
                </x-button>
                <x-button tag="a" :href="route('admin.job-postings.show', $jobPosting)" type="secondary" class="w-full justify-center">
                    <x-fas-eye class="w-4 h-4 mr-2" />
                    {{ __('Zur Detailansicht') }}
                </x-button>
            </div>
        </div>
    </div>
</x-layouts.app>

