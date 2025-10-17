<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Stellenausschreibungen'), 'url' => route('job-postings.index')],
            ['label' => __('Übersicht')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Stellenausschreibungen') }}</h1>
            <x-button tag="a" :href="route('job-postings.create')" type="primary">
                <x-fas-plus class="w-4 h-4 mr-2" />
                {{ __('Neue Stellenausschreibung') }}
            </x-button>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form method="GET" action="{{ route('job-postings.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Status') }}</label>
                <select name="status" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                    <option value="">{{ __('Alle Status') }}</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>{{ __('Entwurf') }}</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Aktiv') }}</option>
                    <option value="paused" {{ request('status') === 'paused' ? 'selected' : '' }}>{{ __('Pausiert') }}</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>{{ __('Abgelaufen') }}</option>
                </select>
            </div>
            <div class="flex items-end">
                <x-button type="primary" class="w-full">
                    {{ __('Filtern') }}
                </x-button>
            </div>
        </form>
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

    <!-- Job Postings List -->
    <div class="space-y-4">
        @forelse($jobPostings as $jobPosting)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                <a href="{{ route('job-postings.show', $jobPosting) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                    {{ $jobPosting->title }}
                                </a>
                            </h3>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-{{ $jobPosting->getStatusColor() }}-100 text-{{ $jobPosting->getStatusColor() }}-800 dark:bg-{{ $jobPosting->getStatusColor() }}-900/30 dark:text-{{ $jobPosting->getStatusColor() }}-300">
                                {{ $jobPosting->getStatusLabel() }}
                            </span>
                        </div>

                        <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400 mb-3">
                            <div class="flex items-center">
                                <x-fas-building class="w-4 h-4 mr-1" />
                                {{ $jobPosting->facility->name }}
                            </div>
                            <div class="flex items-center">
                                <x-fas-briefcase class="w-4 h-4 mr-1" />
                                {{ $jobPosting->getEmploymentTypeLabel() }}
                            </div>
                            @if($jobPosting->job_category)
                                <div class="flex items-center">
                                    <x-fas-tag class="w-4 h-4 mr-1" />
                                    {{ $jobPosting->job_category }}
                                </div>
                            @endif
                        </div>

                        <p class="text-gray-600 dark:text-gray-400 line-clamp-2">
                            {{ Str::limit(strip_tags($jobPosting->description), 200) }}
                        </p>

                        @if($jobPosting->published_at)
                            <div class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('Veröffentlicht am') }}: {{ $jobPosting->published_at->format('d.m.Y') }}
                                @if($jobPosting->expires_at)
                                    | {{ __('Läuft ab am') }}: {{ $jobPosting->expires_at->format('d.m.Y') }}
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="ml-4 flex flex-col gap-2">
                        <x-button tag="a" :href="route('job-postings.show', $jobPosting)" type="secondary" size="sm">
                            {{ __('Details') }}
                        </x-button>
                        @can('update', $jobPosting)
                            <x-button tag="a" :href="route('job-postings.edit', $jobPosting)" type="secondary" size="sm">
                                {{ __('Bearbeiten') }}
                            </x-button>
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                <x-fas-briefcase class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                <p class="text-gray-500 dark:text-gray-400 text-lg">{{ __('Noch keine Stellenausschreibungen vorhanden.') }}</p>
                <p class="text-gray-400 dark:text-gray-500 mt-2">{{ __('Erstellen Sie Ihre erste Stellenausschreibung.') }}</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($jobPostings->hasPages())
        <div class="mt-6">
            {{ $jobPostings->links() }}
        </div>
    @endif
</x-layouts.app>

