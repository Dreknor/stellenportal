<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Stellenausschreibungen'), 'url' => route('admin.job-postings.index')],
            ['label' => __('Übersicht')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Stellenausschreibungen') }}</h1>
    </div>

    <!-- Search and Filter Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <form method="GET" action="{{ route('admin.job-postings.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Suche') }}</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="{{ __('Titel oder Beschreibung...') }}">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Status') }}</label>
                    <select name="status" id="status" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">{{ __('Alle Status') }}</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>{{ __('Entwurf') }}</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Aktiv') }}</option>
                        <option value="paused" {{ request('status') === 'paused' ? 'selected' : '' }}>{{ __('Pausiert') }}</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>{{ __('Abgelaufen') }}</option>
                    </select>
                </div>
                <div>
                    <label for="facility" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Einrichtung') }}</label>
                    <select name="facility" id="facility" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">{{ __('Alle Einrichtungen') }}</option>
                        @foreach($facilities as $facility)
                            <option value="{{ $facility->id }}" {{ request('facility') == $facility->id ? 'selected' : '' }}>
                                {{ $facility->name }} ({{ $facility->organization->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex gap-2">
                <x-button type="primary" native-type="submit">
                    <x-fas-search class="w-3 mr-2"/>
                    {{ __('Suchen') }}
                </x-button>
                <x-button type="secondary" tag="a" :href="route('admin.job-postings.index')">
                    {{ __('Zurücksetzen') }}
                </x-button>
            </div>
        </form>
    </div>

    <!-- Job Postings Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Titel') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Einrichtung') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Veröffentlicht') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Läuft ab') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Aktionen') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($jobPostings as $jobPosting)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $jobPosting->title }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $jobPosting->employment_type }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $jobPosting->facility->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $jobPosting->facility->organization->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($jobPosting->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                        {{ __('Aktiv') }}
                                    </span>
                                @elseif($jobPosting->status === 'draft')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300">
                                        {{ __('Entwurf') }}
                                    </span>
                                @elseif($jobPosting->status === 'paused')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                        {{ __('Pausiert') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                        {{ __('Abgelaufen') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $jobPosting->published_at?->format('d.m.Y') ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $jobPosting->expires_at?->format('d.m.Y') ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    @can('admin view job postings')
                                        <x-button type="secondary" size="sm" tag="a" :href="route('admin.job-postings.show', $jobPosting)">
                                            <x-fas-eye class="w-3"/>
                                        </x-button>
                                    @endcan
                                    @can('admin edit job postings')
                                        <x-button type="primary" size="sm" tag="a" :href="route('admin.job-postings.edit', $jobPosting)">
                                            <x-fas-edit class="w-3"/>
                                        </x-button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <x-fas-briefcase class="w-12 h-12 mx-auto mb-4 opacity-50"/>
                                    <p class="text-lg font-medium">{{ __('Keine Stellenausschreibungen gefunden') }}</p>
                                    <p class="text-sm mt-1">{{ __('Es wurden keine Stellenausschreibungen gefunden, die Ihren Suchkriterien entsprechen.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($jobPostings->hasPages())
        <div class="mt-6">
            {{ $jobPostings->links() }}
        </div>
    @endif
</x-layouts.app>

