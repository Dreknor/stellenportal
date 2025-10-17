<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Stellenausschreibungen'), 'url' => route('job-postings.index')],
            ['label' => $jobPosting->title],
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
            <!-- Actions Card -->
            @if($jobPosting->isDraft())
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Aktionen') }}</h3>

                    <div class="space-y-3">
                        @can('publish', $jobPosting)
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-4">
                                <p class="text-sm text-gray-700 dark:text-gray-300 mb-3">
                                    {{ __('Veröffentlichen Sie diese Stellenausschreibung für 3 Monate. Kosten: 1 Guthaben') }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                                    {{ __('Aktuelles Guthaben') }}: <strong>{{ $jobPosting->facility->getCurrentCreditBalance() }}</strong>
                                </p>
                                <form method="POST" action="{{ route('job-postings.publish', $jobPosting) }}">
                                    @csrf
                                    <x-button type="success" class="w-full justify-center">
                                        <x-fas-check class="w-4 h-4 mr-2" />
                                        {{ __('Jetzt veröffentlichen') }}
                                    </x-button>
                                </form>
                            </div>
                        @else
                            <x-alerts.alert type="warning">
                                {{ __('Nicht genügend Guthaben zum Veröffentlichen.') }}
                            </x-alerts.alert>
                        @endcan
                    </div>
                </div>
            @endif

            @if($jobPosting->isActive())
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Aktionen') }}</h3>

                    <div class="space-y-3">
                        @can('extend', $jobPosting)
                            <form method="POST" action="{{ route('job-postings.extend', $jobPosting) }}">
                                @csrf
                                <x-button type="primary" class="w-full justify-center">
                                    <x-fas-clock class="w-4 h-4 mr-2" />
                                    {{ __('Um 3 Monate verlängern') }}
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
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Aktionen') }}</h3>

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
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Aktionen') }}</h3>

                    @can('extend', $jobPosting)
                        <form method="POST" action="{{ route('job-postings.extend', $jobPosting) }}">
                            @csrf
                            <x-button type="primary" class="w-full justify-center">
                                <x-fas-redo class="w-4 h-4 mr-2" />
                                {{ __('Reaktivieren (3 Monate)') }}
                            </x-button>
                        </form>
                    @endcan
                </div>
            @endif

            <!-- Facility Info -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Einrichtung') }}</h3>

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
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Kontakt') }}</h3>

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
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Informationen') }}</h3>

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
                        <span class="text-gray-800 dark:text-gray-200">{{ $jobPosting->credits_used }}</span>
                    </div>
                </div>
            </div>

            <!-- Delete -->
            @can('delete', $jobPosting)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-red-200 dark:border-red-700 p-6">
                    <h3 class="text-lg font-semibold text-red-600 dark:text-red-400 mb-4">{{ __('Gefahrenzone') }}</h3>

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
