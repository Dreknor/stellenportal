<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Stellenausschreibungen'), 'url' => route('admin.job-postings.index')],
            ['label' => $jobPosting->title ?? ''],
        ];

        // Get header image from facility
        $headerImage = $jobPosting->facility->getFirstMediaUrl('header_image') ?: $jobPosting->facility->getFirstMediaUrl('header') ?: $jobPosting->facility->getFirstMediaUrl('cover') ?: $jobPosting->facility->getFirstMediaUrl('logo');

        // Compute initials for placeholder (max 2 letters)
        $initials = '';
        $nameParts = preg_split('/\s+/', trim((string) $jobPosting->facility->name));
        if (!empty($nameParts)) {
            $firstTwo = array_slice($nameParts, 0, 2);
            foreach ($firstTwo as $part) {
                $initials .= mb_strtoupper(mb_substr($part, 0, 1));
            }
        }
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <!-- Facility Header Image Banner -->
    @if($headerImage)
        <div class="mb-6 rounded-lg overflow-hidden shadow-lg">
            <div class="h-48 md:h-64 bg-cover bg-center relative" style="background-image: url('{{ $headerImage }}')">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6">
                    <div class="flex items-center text-white mb-2">
                        <x-fas-building class="w-5 h-5 mr-2 opacity-90" />
                        <span class="text-lg font-semibold">{{ $jobPosting->facility->name }}</span>
                    </div>
                    @if($jobPosting->facility->address)
                        <div class="flex items-center text-white/90 text-sm">
                            <x-fas-map-marker-alt class="w-4 h-4 mr-2" />
                            <span>{{ $jobPosting->facility->address->city }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="mb-6 rounded-lg overflow-hidden shadow-lg">
            <div class="h-48 md:h-64 bg-gradient-to-r from-green-200 to-green-400 dark:from-gray-800 dark:to-gray-700 relative">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <div class="w-24 h-24 rounded-full bg-green-50 dark:bg-green-900 text-green-700 dark:text-green-200 flex items-center justify-center text-3xl font-bold mx-auto mb-4">
                            {!! $initials ?: '&nbsp;' !!}
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $jobPosting->facility->name }}</h2>
                        @if($jobPosting->facility->address)
                            <div class="flex items-center justify-center text-gray-700 dark:text-gray-300 text-sm mt-2">
                                <x-fas-map-marker-alt class="w-4 h-4 mr-2" />
                                <span>{{ $jobPosting->facility->address->city }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="mb-6">
        <div class="flex justify-between items-start">
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2">{{ $jobPosting->title }}</h1>
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 rounded-full text-sm font-semibold bg-{{ $jobPosting->getStatusColor() }}-100 text-{{ $jobPosting->getStatusColor() }}-800 dark:bg-{{ $jobPosting->getStatusColor() }}-900/30 dark:text-{{ $jobPosting->getStatusColor() }}-300">
                        {{ $jobPosting->getStatusLabel() }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                        {{ $jobPosting->getEmploymentTypeLabel() }}
                    </span>
                </div>
            </div>

            <div class="ml-4 flex gap-2">
                <x-button tag="a" :href="route('public.jobs.show', $jobPosting)" type="secondary" target="_blank">
                    <x-fas-external-link-alt class="w-4 h-4 mr-2" />
                    {{ __('Öffentliche Ansicht') }}
                </x-button>
                @can('admin edit job postings')
                    <x-button tag="a" :href="route('admin.job-postings.edit', $jobPosting)" type="secondary">
                        <x-fas-edit class="w-4 h-4 mr-2" />
                        {{ __('Bearbeiten') }}
                    </x-button>
                @endcan
            </div>
        </div>
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

    <!-- Statistics Section (Always visible for admins) -->
    @if($jobPosting->isActive() || $jobPosting->status === 'expired' || $jobPosting->status === 'paused')
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">
                <x-fas-chart-bar class="w-5 h-5 inline-block mr-2" />
                {{ __('Statistiken') }}
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                <!-- Views -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Aufrufe') }}</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($stats['views']) }}</p>
                        </div>
                        <div class="bg-blue-100 dark:bg-blue-900/30 rounded-full p-3">
                            <x-fas-eye class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        {{ number_format($uniqueVisitors) }} {{ __('eindeutig') }}
                    </p>
                </div>

                <!-- Apply Clicks -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Bewerbungen') }}</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($stats['apply_clicks']) }}</p>
                        </div>
                        <div class="bg-green-100 dark:bg-green-900/30 rounded-full p-3">
                            <x-fas-paper-plane class="w-6 h-6 text-green-600 dark:text-green-400" />
                        </div>
                    </div>
                    @if($stats['views'] > 0)
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            {{ number_format(($stats['apply_clicks'] / $stats['views']) * 100, 1) }}% {{ __('Conversion') }}
                        </p>
                    @endif
                </div>

                <!-- Email Reveals -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('E-Mail angezeigt') }}</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($stats['email_reveals']) }}</p>
                        </div>
                        <div class="bg-purple-100 dark:bg-purple-900/30 rounded-full p-3">
                            <x-fas-envelope class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                    </div>
                </div>

                <!-- Phone Reveals -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Telefon angezeigt') }}</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($stats['phone_reveals']) }}</p>
                        </div>
                        <div class="bg-yellow-100 dark:bg-yellow-900/30 rounded-full p-3">
                            <x-fas-phone class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                        </div>
                    </div>
                </div>

                <!-- Downloads -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('PDF-Downloads') }}</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($stats['downloads']) }}</p>
                        </div>
                        <div class="bg-red-100 dark:bg-red-900/30 rounded-full p-3">
                            <x-fas-download class="w-6 h-6 text-red-600 dark:text-red-400" />
                        </div>
                    </div>
                </div>

                <!-- Total Interactions -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Interaktionen') }}</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($stats['total_interactions']) }}</p>
                        </div>
                        <div class="bg-indigo-100 dark:bg-indigo-900/30 rounded-full p-3">
                            <x-fas-mouse-pointer class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Stellenbeschreibung') }}</h2>
                <div class="prose dark:prose-invert max-w-none">
                    {!! nl2br(e($jobPosting->description)) !!}
                </div>
            </div>

            @if($jobPosting->requirements)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Anforderungen') }}</h2>
                    <div class="prose dark:prose-invert max-w-none">
                        {!! nl2br(e($jobPosting->requirements)) !!}
                    </div>
                </div>
            @endif

            @if($jobPosting->benefits)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Wir bieten') }}</h2>
                    <div class="prose dark:prose-invert max-w-none">
                        {!! nl2br(e($jobPosting->benefits)) !!}
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Admin Actions Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
                    <x-fas-cog class="w-5 h-5 inline-block mr-2" />
                    {{ __('Admin-Aktionen') }}
                </h3>

                <div class="space-y-3">
                    @if($jobPosting->isDraft())
                        @can('admin publish job postings')
                            <form method="POST" action="{{ route('admin.job-postings.publish', $jobPosting) }}">
                                @csrf
                                <x-button type="success" class="w-full justify-center">
                                    <x-fas-check class="w-4 h-4 mr-2" />
                                    {{ __('Veröffentlichen') }}
                                </x-button>
                            </form>
                        @endcan
                    @elseif($jobPosting->isActive())
                        @can('admin publish job postings')
                            <form method="POST" action="{{ route('admin.job-postings.pause', $jobPosting) }}">
                                @csrf
                                <x-button type="warning" class="w-full justify-center">
                                    <x-fas-pause class="w-4 h-4 mr-2" />
                                    {{ __('Pausieren') }}
                                </x-button>
                            </form>
                        @endcan
                    @elseif($jobPosting->status === 'paused')
                        @can('admin publish job postings')
                            <form method="POST" action="{{ route('admin.job-postings.resume', $jobPosting) }}">
                                @csrf
                                <x-button type="success" class="w-full justify-center">
                                    <x-fas-play class="w-4 h-4 mr-2" />
                                    {{ __('Fortsetzen') }}
                                </x-button>
                            </form>
                        @endcan
                    @endif

                    @can('admin delete job postings')
                        <form method="POST" action="{{ route('admin.job-postings.destroy', $jobPosting) }}"
                              onsubmit="return confirm('{{ __('Sind Sie sicher, dass Sie diese Stellenausschreibung löschen möchten?') }}')">
                            @csrf
                            @method('DELETE')
                            <x-button type="danger" class="w-full justify-center">
                                <x-fas-trash class="w-4 h-4 mr-2" />
                                {{ __('Löschen') }}
                            </x-button>
                        </form>
                    @endcan
                </div>
            </div>

            <!-- Details Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Details') }}</h3>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('Einrichtung') }}</dt>
                        <dd class="text-gray-900 dark:text-gray-100 font-medium">
                            <a href="{{ route('admin.facilities.show', $jobPosting->facility) }}" class="text-blue-600 hover:underline">
                                {{ $jobPosting->facility->name }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('Organisation') }}</dt>
                        <dd class="text-gray-900 dark:text-gray-100 font-medium">
                            <a href="{{ route('admin.organizations.show', $jobPosting->facility->organization) }}" class="text-blue-600 hover:underline">
                                {{ $jobPosting->facility->organization->name }}
                            </a>
                        </dd>
                    </div>
                    @if($jobPosting->published_at)
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Veröffentlicht am') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100 font-medium">{{ $jobPosting->published_at->format('d.m.Y H:i') }}</dd>
                        </div>
                    @endif
                    @if($jobPosting->expires_at)
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Läuft ab am') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100 font-medium">{{ $jobPosting->expires_at->format('d.m.Y H:i') }}</dd>
                        </div>
                    @endif
                    @if($jobPosting->creator)
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Erstellt von') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100 font-medium">
                                <a href="{{ route('admin.users.show', $jobPosting->creator) }}" class="text-blue-600 hover:underline">
                                    {{ $jobPosting->creator->name }}
                                </a>
                            </dd>
                        </div>
                    @endif
                    @if($jobPosting->contact_email)
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Kontakt E-Mail') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100 font-medium break-all">{{ $jobPosting->contact_email }}</dd>
                        </div>
                    @endif
                    @if($jobPosting->contact_phone)
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Kontakt Telefon') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100 font-medium">{{ $jobPosting->contact_phone }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <!-- Audit Log -->
            @if($jobPosting->audits->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
                        <x-fas-history class="w-5 h-5 inline-block mr-2" />
                        {{ __('Änderungshistorie') }}
                    </h3>
                    <div class="space-y-2">
                        @foreach($jobPosting->audits->take(5) as $audit)
                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                <span class="font-medium">{{ $audit->created_at->format('d.m.Y H:i') }}</span>
                                <span class="text-gray-500">{{ $audit->event }}</span>
                                @if($audit->user)
                                    <span>von {{ $audit->user->name }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>

