<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Einrichtungen'), 'url' => route('admin.facilities.index')],
            ['label' => $facility->name],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $facility->name }}</h1>
        @can('admin edit facilities')
            <x-button type="primary" tag="a" :href="route('admin.facilities.edit', $facility)">
                <x-fas-edit class="w-3 mr-3"/>
                {{ __('Bearbeiten') }}
            </x-button>
        @endcan
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Facility Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Einrichtungsinformationen') }}</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Name') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $facility->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Organisation') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            <a href="{{ route('admin.organizations.show', $facility->organization) }}" class="text-blue-600 hover:underline">
                                {{ $facility->organization->name }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('E-Mail') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $facility->email ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Telefon') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $facility->phone ?? '-' }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Website') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            @if($facility->website)
                                <a href="{{ $facility->website }}" target="_blank" class="text-blue-600 hover:underline">{{ $facility->website }}</a>
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Address -->
            @if($facility->address)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Adresse') }}</h2>
                    <div class="text-sm text-gray-900 dark:text-gray-100">
                        <p>{{ $facility->address->street }} {{ $facility->address->street_number }}</p>
                        <p>{{ $facility->address->postal_code }} {{ $facility->address->city }}</p>
                        <p>{{ $facility->address->country }}</p>
                    </div>
                </div>
            @endif

            <!-- Users -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Zugeordnete Benutzer') }} ({{ $facility->users->count() }})</h2>
                @forelse($facility->users as $user)
                    <div class="mb-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg flex justify-between items-center">
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-gray-100">{{ $user->first_name }} {{ $user->last_name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                        </div>
                        <x-button type="secondary" size="sm" tag="a" :href="route('admin.users.show', $user)">
                            <x-fas-arrow-right class="w-3"/>
                        </x-button>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Keine Benutzer zugeordnet') }}</p>
                @endforelse
            </div>

            <!-- Job Postings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Stellenausschreibungen') }} ({{ $facility->jobPostings->count() }})</h2>
                @forelse($facility->jobPostings->take(5) as $jobPosting)
                    <div class="mb-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg flex justify-between items-center">
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-gray-100">{{ $jobPosting->title }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $jobPosting->status }}</p>
                        </div>
                        <x-button type="secondary" size="sm" tag="a" :href="route('admin.job-postings.show', $jobPosting)">
                            <x-fas-arrow-right class="w-3"/>
                        </x-button>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Keine Stellenausschreibungen vorhanden') }}</p>
                @endforelse
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Credit Balance -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Guthaben') }}</h2>
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                    {{ $facility->creditBalance->balance ?? 0 }}
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Credits verfügbar') }}</p>
            </div>

            <!-- Actions -->
            @can('admin delete facilities')
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Aktionen') }}</h2>
                    <form method="POST" action="{{ route('admin.facilities.destroy', $facility) }}" onsubmit="return confirm('{{ __('Sind Sie sicher, dass Sie diese Einrichtung löschen möchten?') }}')">
                        @csrf
                        @method('DELETE')
                        <x-button type="danger" native-type="submit" class="w-full">
                            <x-fas-trash class="w-3 mr-2"/>
                            {{ __('Einrichtung löschen') }}
                        </x-button>
                    </form>
                </div>
            @endcan
        </div>
    </div>
</x-layouts.app>

