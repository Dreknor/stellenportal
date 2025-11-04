<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Stellenausschreibungen'), 'url' => route('job-postings.index')],
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

            @can('update', $jobPosting)
                <div class="ml-4">
                    <x-button tag="a" :href="route('job-postings.edit', $jobPosting)" type="secondary">
                        <x-fas-edit class="w-4 h-4 mr-2" />
                        {{ __('Bearbeiten') }}
                    </x-button>
                </div>
            @endcan
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

    <!-- Statistics Section -->
    @can('view job posting statistics')
        @if(in_array($jobPosting->status, ['active', 'expired', 'paused']))
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
                    <x-fas-chart-bar class="w-5 h-5 mr-2" />
                    {{ __('Statistiken') }}
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                <!-- Views -->
                <div class="text-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <div class="flex justify-center mb-2">
                        <div class="bg-blue-100 dark:bg-blue-900/40 rounded-full p-2">
                            <x-fas-eye class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($stats['views']) }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ __('Aufrufe') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-0.5">
                        {{ number_format($uniqueVisitors) }} {{ __('eindeutig') }}
                    </p>
                </div>

                <!-- Apply Clicks -->
                <div class="text-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <div class="flex justify-center mb-2">
                        <div class="bg-green-100 dark:bg-green-900/40 rounded-full p-2">
                            <x-fas-paper-plane class="w-5 h-5 text-green-600 dark:text-green-400" />
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($stats['apply_clicks']) }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ __('Bewerbungen') }}</p>
                    @if($stats['views'] > 0)
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-0.5">
                            {{ number_format(($stats['apply_clicks'] / $stats['views']) * 100, 1) }}% {{ __('Rate') }}
                        </p>
                    @endif
                </div>

                <!-- Email Reveals -->
                <div class="text-center p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                    <div class="flex justify-center mb-2">
                        <div class="bg-purple-100 dark:bg-purple-900/40 rounded-full p-2">
                            <x-fas-envelope class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($stats['email_reveals']) }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ __('E-Mail') }}</p>
                </div>

                <!-- Phone Reveals -->
                <div class="text-center p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                    <div class="flex justify-center mb-2">
                        <div class="bg-yellow-100 dark:bg-yellow-900/40 rounded-full p-2">
                            <x-fas-phone class="w-5 h-5 text-yellow-600 dark:text-yellow-400" />
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($stats['phone_reveals']) }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ __('Telefon') }}</p>
                </div>

                <!-- Downloads -->
                <div class="text-center p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                    <div class="flex justify-center mb-2">
                        <div class="bg-red-100 dark:bg-red-900/40 rounded-full p-2">
                            <x-fas-download class="w-5 h-5 text-red-600 dark:text-red-400" />
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($stats['downloads']) }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ __('PDF-Downloads') }}</p>
                </div>

                <!-- Total Interactions -->
                <div class="text-center p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg">
                    <div class="flex justify-center mb-2">
                        <div class="bg-indigo-100 dark:bg-indigo-900/40 rounded-full p-2">
                            <x-fas-mouse-pointer class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($stats['total_interactions']) }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ __('Interaktionen') }}</p>
                </div>
            </div>
        </div>
        @endif
    @endcan

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Description -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-3">{{ __('Stellenbeschreibung') }}</h2>
                <div class="prose dark:prose-invert max-w-none">
                    {!! nl2br(e($jobPosting->description)) !!}
                </div>
            </div>

            @if($jobPosting->requirements)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-3">{{ __('Anforderungen') }}</h2>
                    <div class="prose dark:prose-invert max-w-none">
                        {!! nl2br(e($jobPosting->requirements)) !!}
                    </div>
                </div>
            @endif

            @if($jobPosting->benefits)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-3">{{ __('Wir bieten') }}</h2>
                    <div class="prose dark:prose-invert max-w-none">
                        {!! nl2br(e($jobPosting->benefits)) !!}
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-4">
            <!-- Actions Card -->
            @if($jobPosting->isDraft())
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-3">{{ __('Aktionen') }}</h3>

                    @can('publish', $jobPosting)
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3">
                            @if($jobPosting->isExemptFromCredits())
                                <div class="flex items-start mb-2">
                                    <x-fas-info-circle class="w-4 h-4 text-green-600 dark:text-green-400 mr-2 mt-0.5 flex-shrink-0"/>
                                    <p class="text-xs text-gray-700 dark:text-gray-300">
                                        {{ __('Für diese Beschäftigungsart (') }}{{ $jobPosting->getEmploymentTypeLabel() }}{{ __(') werden keine Guthaben benötigt.') }}
                                    </p>
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-300 mb-3">
                                    {{ __('Veröffentlichen Sie diese Stellenausschreibung kostenlos für 3 Monate.') }}
                                </p>
                            @else
                                <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Veröffentlichen Sie diese Stellenausschreibung für 3 Monate.') }}
                                </p>
                                <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400 mb-3 bg-white/50 dark:bg-gray-800/50 rounded px-2 py-1.5">
                                    <span>{{ __('Kosten') }}:</span>
                                    <span class="font-semibold">1 Guthaben</span>
                                </div>
                                <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400 mb-3 bg-white/50 dark:bg-gray-800/50 rounded px-2 py-1.5">
                                    <span>{{ __('Verfügbar') }}:</span>
                                    <span class="font-semibold">{{ $jobPosting->facility->getCurrentCreditBalance() }} Guthaben</span>
                                </div>
                            @endif
                            <form method="POST" action="{{ route('job-postings.publish', $jobPosting) }}">
                                @csrf
                                <x-button type="success" class="w-full justify-center">
                                    <x-fas-check class="w-4 h-4 mr-2" />
                                    {{ __('Jetzt veröffentlichen') }}
                                </x-button>
                            </form>
                        </div>
                    @else
                        <x-alerts.alert type="warning" class="text-sm">
                            {{ __('Nicht genügend Guthaben zum Veröffentlichen.') }}
                        </x-alerts.alert>
                    @endcan
                </div>
            @endif

            @if($jobPosting->status === 'active')
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-3">{{ __('Aktionen') }}</h3>

                    @if($jobPosting->expires_at && $jobPosting->expires_at->isPast())
                        <x-alerts.alert type="warning" class="text-sm mb-3">
                            <div class="flex items-start">
                                <x-fas-exclamation-triangle class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" />
                                <div>
                                    <p class="font-semibold">{{ __('Abgelaufen') }}</p>
                                    <p class="text-xs mt-1">{{ __('Diese Stellenausschreibung ist am :date abgelaufen. Der Status wird automatisch aktualisiert.', ['date' => $jobPosting->expires_at->format('d.m.Y')]) }}</p>
                                </div>
                            </div>
                        </x-alerts.alert>
                    @endif

                    <div class="space-y-2">
                        @can('extend', $jobPosting)
                            @if($jobPosting->isExemptFromCredits())
                                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-2 mb-2">
                                    <p class="text-xs text-green-700 dark:text-green-300 flex items-center">
                                        <x-fas-info-circle class="w-3.5 h-3.5 inline mr-1.5 flex-shrink-0"/>
                                        {{ __('Verlängerung ist kostenlos für diese Beschäftigungsart.') }}
                                    </p>
                                </div>
                            @endif
                            <form method="POST" action="{{ route('job-postings.extend', $jobPosting) }}">
                                @csrf
                                <x-button type="primary" class="w-full justify-center">
                                    <x-fas-clock class="w-4 h-4 mr-2" />
                                    {{ __('Um 3 Monate verlängern') }}
                                    @unless($jobPosting->isExemptFromCredits())
                                        (1 Guthaben)
                                    @endunless
                                </x-button>
                            </form>
                        @endcan

                        @can('pause', $jobPosting)
                            <form method="POST" action="{{ route('job-postings.pause', $jobPosting) }}">
                                @csrf
                                <x-button type="warning" class="w-full justify-center">
                                    <x-fas-pause class="w-4 h-4 mr-2" />
                                    {{ __('Pausieren') }}
                                </x-button>
                            </form>
                        @endcan
                    </div>
                </div>
            @endif

            @if($jobPosting->status === 'paused')
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-3">{{ __('Aktionen') }}</h3>

                    @can('resume', $jobPosting)
                        <form method="POST" action="{{ route('job-postings.resume', $jobPosting) }}">
                            @csrf
                            <x-button type="success" class="w-full justify-center">
                                <x-fas-play class="w-4 h-4 mr-2" />
                                {{ __('Fortsetzen') }}
                            </x-button>
                        </form>
                    @endcan
                </div>
            @endif

            @if($jobPosting->status === 'expired')
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-orange-200 dark:border-orange-700 p-5">
                    <div class="flex items-center mb-4">
                        <x-fas-exclamation-triangle class="w-5 h-5 text-orange-600 dark:text-orange-400 mr-2" />
                        <h3 class="text-base font-semibold text-orange-900 dark:text-orange-200">{{ __('Stellenausschreibung abgelaufen') }}</h3>
                    </div>

                    @can('extend', $jobPosting)
                        @php
                            $creditsRequired = \App\Models\JobPosting::CREDITS_PER_POSTING;
                            $currentBalance = $jobPosting->facility->getCurrentCreditBalance();
                            $isExempt = $jobPosting->isExemptFromCredits();
                        @endphp

                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-3">
                            <div class="flex items-start">
                                <x-fas-info-circle class="w-4 h-4 text-blue-600 dark:text-blue-400 mr-2 mt-0.5 flex-shrink-0" />
                                <div>
                                    <p class="text-sm text-blue-900 dark:text-blue-200 font-medium mb-2">
                                        {{ __('Reaktivieren Sie diese Stellenausschreibung') }}
                                    </p>
                                    <p class="text-xs text-blue-800 dark:text-blue-300 mb-3">
                                        {{ __('Sie können diese Stellenausschreibung um :months weitere Monate aktiv machen.', ['months' => \App\Models\JobPosting::POSTING_DURATION_MONTHS]) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if($isExempt)
                            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3 mb-3">
                                <p class="text-xs text-green-700 dark:text-green-300 flex items-center">
                                    <x-fas-check-circle class="w-3.5 h-3.5 inline mr-1.5 flex-shrink-0"/>
                                    {{ __('Reaktivierung ist kostenlos für diese Beschäftigungsart.') }}
                                </p>
                            </div>
                        @else
                            <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-3 mb-3 space-y-2">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Kosten') }}:</span>
                                    <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $creditsRequired }} {{ __('Guthaben') }}</span>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Aktuelles Guthaben') }}:</span>
                                    <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $currentBalance }} {{ __('Guthaben') }}</span>
                                </div>
                            </div>
                        @endif

                        @if(!$isExempt && $currentBalance < $creditsRequired)
                            <x-alerts.alert type="warning" class="text-xs mb-3">
                                {{ __('Nicht genügend Guthaben zum Reaktivieren.') }}
                            </x-alerts.alert>

                            <div class="space-y-2">
                                <x-button type="primary" class="w-full justify-center" disabled aria-disabled="true">
                                    <x-fas-redo class="w-4 h-4 mr-2" />
                                    {{ __('Reaktivieren (3 Monate)') }}
                                </x-button>
                                <x-button tag="a" :href="route('credits.facility.purchase', $jobPosting->facility)" type="secondary" class="w-full justify-center">
                                    <x-fas-plus class="w-4 h-4 mr-2" />
                                    {{ __('Guthaben aufladen') }}
                                </x-button>
                            </div>
                        @else
                            <form method="POST" action="{{ route('job-postings.extend', $jobPosting) }}">
                                @csrf
                                <x-button type="success" class="w-full justify-center">
                                    <x-fas-redo class="w-4 h-4 mr-2" />
                                    {{ __('Jetzt reaktivieren') }}
                                </x-button>
                            </form>
                        @endif
                    @else
                        <x-alerts.alert type="info" class="text-sm">
                            {{ __('Sie haben keine Berechtigung, diese Stellenausschreibung zu reaktivieren.') }}
                        </x-alerts.alert>
                    @endcan
                </div>
            @endif

            <!-- Facility Info -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-3">{{ __('Einrichtung') }}</h3>

                <div class="space-y-3">
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $jobPosting->facility->name }}</p>
                        @if($jobPosting->facility->address)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ $jobPosting->facility->address->street }}<br>
                                {{ $jobPosting->facility->address->postal_code }} {{ $jobPosting->facility->address->city }}
                            </p>

                            @if($jobPosting->facility->address->latitude && $jobPosting->facility->address->longitude)
                                <!-- Map -->
                                <div class="mt-4">
                                    <div id="facilityMap" class="h-48 rounded-lg border border-gray-200 dark:border-gray-600"></div>
                                </div>
                            @endif
                        @endif
                    </div>

                    @if($jobPosting->job_category)
                        <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Kategorie') }}</p>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $jobPosting->job_category }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Contact Info -->
            @if($jobPosting->contact_person || $jobPosting->contact_email || $jobPosting->contact_phone)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-3">{{ __('Kontakt') }}</h3>

                    <div class="space-y-3">
                        @if($jobPosting->contact_person)
                            <div class="flex items-start">
                                <x-fas-user class="w-4 h-4 text-gray-400 mr-2 mt-1" />
                                <div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $jobPosting->contact_person }}</p>
                                </div>
                            </div>
                        @endif

                        @if($jobPosting->contact_email)
                            <div class="flex items-start">
                                <x-fas-envelope class="w-4 h-4 text-gray-400 mr-2 mt-1" />
                                <div>
                                    <a href="mailto:{{ $jobPosting->contact_email }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ $jobPosting->contact_email }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if($jobPosting->contact_phone)
                            <div class="flex items-start">
                                <x-fas-phone class="w-4 h-4 text-gray-400 mr-2 mt-1" />
                                <div>
                                    <a href="tel:{{ $jobPosting->contact_phone }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ $jobPosting->contact_phone }}
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Meta Info -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-3">{{ __('Informationen') }}</h3>

                <div class="space-y-2 text-sm">
                    @if($jobPosting->published_at)
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('Veröffentlicht') }}</span>
                            <span class="text-gray-800 dark:text-gray-200">{{ $jobPosting->published_at->format('d.m.Y') }}</span>
                        </div>
                    @endif

                    @if($jobPosting->expires_at)
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('Läuft ab') }}</span>
                            <span class="text-gray-800 dark:text-gray-200">{{ $jobPosting->expires_at->format('d.m.Y') }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('Erstellt von') }}</span>
                        <span class="text-gray-800 dark:text-gray-200">{{ $jobPosting->creator->name }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('Guthabenverbrauch') }}</span>
                        <span class="text-gray-800 dark:text-gray-200">
                            {{ $jobPosting->credits_used }}
                            @if($jobPosting->isExemptFromCredits())
                                <span class="ml-2 text-xs px-2 py-1 bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 rounded-full">
                                    {{ __('Ausnahme') }}
                                </span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            {{-- Share Buttons (nur sichtbar wenn veröffentlicht) --}}
            @if($jobPosting->published_at)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-3">{{ __('Stellenausschreibung teilen') }}</h3>

                    <div class="flex flex-wrap gap-3">
                        @php
                            $shareUrl = route('public.jobs.show', $jobPosting);
                            $shareText = $jobPosting->title . ' - ' . $jobPosting->facility->name;
                        @endphp

                        <button type="button" class="inline-flex items-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-sm" data-share="twitter" aria-label="Teilen auf Twitter">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M19.633 7.997c.013.176.013.353.013.53 0 5.393-4.103 11.61-11.61 11.61-2.307 0-4.453-.676-6.253-1.847.329.038.657.051.998.051 1.91 0 3.668-.651 5.073-1.747-1.785-.038-3.294-1.21-3.816-2.829.25.038.501.064.767.064.373 0 .746-.05 1.094-.143-1.869-.374-3.272-2.029-3.272-4.014v-.051c.55.307 1.181.494 1.86.517-1.104-.736-1.828-1.989-1.828-3.409 0-.747.201-1.445.552-2.048 2.031 2.493 5.073 4.138 8.494 4.309-.064-.302-.101-.615-.101-.936 0-2.268 1.853-4.121 4.121-4.121 1.186 0 2.258.5 3.011 1.302.936-.176 1.8-.526 2.586-.998-.307.96-.96 1.766-1.816 2.277.829-.089 1.62-.32 2.356-.649-.55.821-1.248 1.542-2.049 2.115z"/></svg>
                            {{ __('Twitter') }}
                        </button>

                        <button type="button" class="inline-flex items-center px-3 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-md text-sm" data-share="facebook" aria-label="Teilen auf Facebook">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M22 12.07C22 6.48 17.52 2 11.93 2 6.48 2 2 6.48 2 12.07c0 4.99 3.66 9.13 8.44 9.94v-7.03H8.08v-2.9h2.36V9.41c0-2.34 1.39-3.62 3.52-3.62.99 0 2.03.18 2.03.18v2.23h-1.14c-1.12 0-1.47.7-1.47 1.41v1.7h2.5l-.4 2.9h-2.1v7.03C18.34 21.2 22 17.06 22 12.07z"/></svg>
                            {{ __('Facebook') }}
                        </button>

                        <button type="button" class="inline-flex items-center px-3 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-md text-sm" data-share="linkedin" aria-label="Teilen auf LinkedIn">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M4.98 3.5C4.98 4.88 3.88 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1 4.98 2.12 4.98 3.5zM.5 8.5h4V24h-4zM8.5 8.5h3.84v2.08h.05c.54-1.02 1.86-2.08 3.83-2.08 4.1 0 4.86 2.7 4.86 6.21V24h-4v-7.44c0-1.78-.03-4.08-2.49-4.08-2.49 0-2.87 1.95-2.87 3.96V24h-4z"/></svg>
                            {{ __('LinkedIn') }}
                        </button>

                        <button type="button" class="inline-flex items-center px-3 py-2 bg-green-500 hover:bg-green-600 text-white rounded-md text-sm" data-share="whatsapp" aria-label="Teilen auf WhatsApp">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.52 3.48A11.9 11.9 0 0012.02 0C5.6 0 .6 5 0 11.38 0 13.1.43 14.74 1.24 16.2L0 24l7.94-1.18c1.44.79 3.07 1.2 4.76 1.2 6.42 0 11.42-5 12.02-11.38.1-.6.16-1.2.16-1.78 0-1.02-.08-2.02-.56-2.94zM12.02 20.06c-1.34 0-2.65-.36-3.78-1.04l-.27-.16-4.7.7.9-4.57-.18-.28A7.66 7.66 0 014.12 6.98c0-4.08 3.32-7.4 7.4-7.4 1.98 0 3.84.77 5.24 2.16A7.33 7.33 0 0120.44 12c0 4.07-3.32 7.4-8.42 8.06z"/></svg>
                            {{ __('WhatsApp') }}
                        </button>

                        <button type="button" id="copyLinkBtn" class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-md text-sm" aria-label="Link kopieren">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16h8M8 12h8M8 8h8M4 6h.01M4 10h.01M4 14h.01M4 18h.01"/></svg>
                            <span id="copyLinkText">{{ __('Link kopieren') }}</span>
                        </button>
                    </div>
                </div>

                @push('scripts')
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const shareUrl = @json($shareUrl ?? route('public.jobs.show', $jobPosting));
                            const shareText = @json($shareText ?? ($jobPosting->title . ' - ' . $jobPosting->facility->name));

                            function openPopup(url) {
                                const width = 800;
                                const height = 600;
                                const left = (screen.width / 2) - (width / 2);
                                const top = (screen.height / 2) - (height / 2);
                                window.open(url, 'shareWindow', `toolbar=0,status=0,width=${width},height=${height},top=${top},left=${left}`);
                            }

                            document.querySelectorAll('[data-share]').forEach(btn => {
                                btn.addEventListener('click', function () {
                                    const provider = this.getAttribute('data-share');
                                    let url = '';

                                    switch (provider) {
                                        case 'twitter':
                                            url = `https://twitter.com/intent/tweet?text=${encodeURIComponent(shareText)}&url=${encodeURIComponent(shareUrl)}`;
                                            break;
                                        case 'facebook':
                                            url = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`;
                                            break;
                                        case 'linkedin':
                                            url = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(shareUrl)}`;
                                            break;
                                        case 'whatsapp':
                                            url = `https://api.whatsapp.com/send?text=${encodeURIComponent(shareText + ' ' + shareUrl)}`;
                                            break;
                                    }

                                    if (url) {
                                        openPopup(url);
                                    }
                                });
                            });

                            // Copy link
                            const copyBtn = document.getElementById('copyLinkBtn');
                            if (copyBtn) {
                                copyBtn.addEventListener('click', async function () {
                                    try {
                                        await navigator.clipboard.writeText(shareUrl);
                                        const textEl = document.getElementById('copyLinkText');
                                        const original = textEl.innerText;
                                        textEl.innerText = '{{ __('Kopiert!') }}';
                                        setTimeout(() => textEl.innerText = original, 2000);

                                    } catch (e) {
                                        // Fallback: select and prompt
                                        const tempInput = document.createElement('input');
                                        tempInput.value = shareUrl;
                                        document.body.appendChild(tempInput);
                                        tempInput.select();
                                        try { document.execCommand('copy'); }
                                        catch (err) { /* ignore */ }
                                        document.body.removeChild(tempInput);
                                        const textEl = document.getElementById('copyLinkText');
                                        const original = textEl.innerText;
                                        textEl.innerText = '{{ __('Kopiert!') }}';
                                        setTimeout(() => textEl.innerText = original, 2000);
                                    }
                                });
                            }
                        });
                    </script>
                @endpush
            @endif

            <!-- Delete -->
            @can('delete', $jobPosting)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-red-200 dark:border-red-700 p-5">
                    <h3 class="text-base font-semibold text-red-600 dark:text-red-400 mb-3">{{ __('Gefahrenzone') }}</h3>

                    <form method="POST" action="{{ route('job-postings.destroy', $jobPosting) }}" onsubmit="return confirm('{{ __('Wirklich löschen?') }}');">
                        @csrf
                        @method('DELETE')
                        <x-button type="danger" class="w-full justify-center">
                            <x-fas-trash class="w-4 h-4 mr-2" />
                            {{ __('Löschen') }}
                        </x-button>
                    </form>
                </div>
            @endcan
        </div>
    </div>

    @if($jobPosting->facility->address && $jobPosting->facility->address->latitude && $jobPosting->facility->address->longitude)
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const map = L.map('facilityMap').setView([{{ $jobPosting->facility->address->latitude }}, {{ $jobPosting->facility->address->longitude }}], 15);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    L.marker([{{ $jobPosting->facility->address->latitude }}, {{ $jobPosting->facility->address->longitude }}]).addTo(map);
                });
            </script>
        @endpush
    @endif
</x-layouts.app>
