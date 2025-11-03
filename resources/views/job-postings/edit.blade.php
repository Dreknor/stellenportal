<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Stellenausschreibungen'), 'url' => route('job-postings.index')],
            ['label' => $jobPosting->title, 'url' => route('job-postings.show', $jobPosting)],
            ['label' => __('Bearbeiten')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Stellenausschreibung bearbeiten') }}</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('job-postings.update', $jobPosting) }}">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Stellentitel') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title', $jobPosting->title) }}" required
                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 @error('title') border-red-500 @enderror">
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
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 @error('employment_type') border-red-500 @enderror">
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

                <!-- Job Category -->
                <div>
                    <label for="job_category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Berufsgruppe / Kategorie') }}
                    </label>
                    <input type="text" id="job_category" name="job_category" value="{{ old('job_category', $jobPosting->job_category) }}"
                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 @error('job_category') border-red-500 @enderror">
                    @error('job_category')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Stellenbeschreibung') }} <span class="text-red-500">*</span>
                    </label>
                    <textarea id="description" name="description" rows="6" required
                              class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 @error('description') border-red-500 @enderror">{{ old('description', $jobPosting->description) }}</textarea>
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
                              class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 @error('requirements') border-red-500 @enderror">{{ old('requirements', $jobPosting->requirements) }}</textarea>
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
                              class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 @error('benefits') border-red-500 @enderror">{{ old('benefits', $jobPosting->benefits) }}</textarea>
                    @error('benefits')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact Information -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Kontaktinformationen') }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="contact_person" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Ansprechpartner') }}
                            </label>
                            <input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person', $jobPosting->contact_person) }}"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        </div>

                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Kontakt E-Mail') }}
                            </label>
                            <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email', $jobPosting->contact_email) }}"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        </div>

                        <div>
                            <label for="contact_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Kontakt Telefon') }}
                            </label>
                            <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $jobPosting->contact_phone) }}"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <x-button tag="a" :href="route('job-postings.show', $jobPosting)" type="secondary">
                        {{ __('Abbrechen') }}
                    </x-button>
                    <x-button type="primary">
                        {{ __('Speichern') }}
                    </x-button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.app>

