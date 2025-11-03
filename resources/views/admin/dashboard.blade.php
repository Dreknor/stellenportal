<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Dashboard')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Admin Dashboard') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Übersicht über das gesamte System') }}</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Users Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Benutzer') }}</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $stats['users_total'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ $stats['users_verified'] }} {{ __('verifiziert') }}
                    </p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full">
                    <x-fas-users class="w-6 h-6 text-blue-600 dark:text-blue-300"/>
                </div>
            </div>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                {{ __('Alle anzeigen') }} →
            </a>
        </div>

        <!-- Organizations Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Organisationen') }}</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $stats['organizations_total'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ __('Träger') }}
                    </p>
                </div>
                <div class="bg-green-100 dark:bg-green-900 p-3 rounded-full">
                    <x-fas-building class="w-6 h-6 text-green-600 dark:text-green-300"/>
                </div>
            </div>
            <a href="{{ route('admin.organizations.index') }}" class="text-sm text-green-600 dark:text-green-400 hover:underline">
                {{ __('Alle anzeigen') }} →
            </a>
        </div>

        <!-- Facilities Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Einrichtungen') }}</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $stats['facilities_total'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ __('Aktive Einrichtungen') }}
                    </p>
                </div>
                <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-full">
                    <x-fas-hospital class="w-6 h-6 text-purple-600 dark:text-purple-300"/>
                </div>
            </div>
            <a href="{{ route('admin.facilities.index') }}" class="text-sm text-purple-600 dark:text-purple-400 hover:underline">
                {{ __('Alle anzeigen') }} →
            </a>
        </div>

        <!-- Job Postings Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Stellenausschreibungen') }}</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $stats['job_postings_active'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ $stats['job_postings_draft'] }} {{ __('Entwürfe') }}, {{ $stats['job_postings_expired'] }} {{ __('abgelaufen') }}
                    </p>
                </div>
                <div class="bg-orange-100 dark:bg-orange-900 p-3 rounded-full">
                    <x-fas-briefcase class="w-6 h-6 text-orange-600 dark:text-orange-300"/>
                </div>
            </div>
            <a href="{{ route('admin.job-postings.index') }}" class="text-sm text-orange-600 dark:text-orange-400 hover:underline">
                {{ __('Alle anzeigen') }} →
            </a>
        </div>
    </div>

    <!-- Credits Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('Guthaben-Übersicht') }}</h2>
                <a href="{{ route('admin.credits.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                    {{ __('Details') }} →
                </a>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['credits_total'] }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('Gesamt Credits') }}</p>
                </div>
                <div class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                    <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ abs($stats['credits_used_today']) }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('Heute verbraucht') }}</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Schnellaktionen') }}</h2>
            <div class="grid grid-cols-2 gap-3">
                @can('admin create users')
                    <x-button type="primary" size="sm" tag="a" :href="route('admin.users.create')" class="w-full justify-center">
                        <x-fas-user-plus class="w-3 mr-2"/>
                        {{ __('Benutzer erstellen') }}
                    </x-button>
                @endcan
                @can('admin grant credits')
                    <x-button type="success" size="sm" tag="a" :href="route('admin.credits.grant')" class="w-full justify-center">
                        <x-fas-coins class="w-3 mr-2"/>
                        {{ __('Credits gewähren') }}
                    </x-button>
                @endcan
                @can('admin edit organizations')
                    <x-button type="secondary" size="sm" tag="a" :href="route('admin.footer-settings.index')" class="w-full justify-center">
                        <x-fas-cog class="w-3 mr-2"/>
                        {{ __('Footer-Einstellungen') }}
                    </x-button>
                @endcan
                @can('admin view logs')
                    <x-button type="secondary" size="sm" tag="a" :href="route('admin.audits.index')" class="w-full justify-center">
                        <x-fas-clipboard-list class="w-3 mr-2"/>
                        {{ __('Audit-Logs') }}
                    </x-button>
                @endcan
                @can('admin view logs')
                    <x-button type="secondary" size="sm" tag="a" :href="route('admin.logs.index')" class="w-full justify-center">
                        <x-fas-file-alt class="w-3 mr-2"/>
                        {{ __('Logs') }}
                    </x-button>
                @endcan
                @can('admin view logs')
                    <x-button type="danger" size="sm" tag="a" :href="route('admin.failed-jobs.index')" class="w-full justify-center">
                        <x-fas-bug class="w-3 mr-2"/>
                        {{ __('Fehlgeschlagene Jobs') }}
                    </x-button>
                @endcan
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Users -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('Neueste Benutzer') }}</h2>
                <a href="{{ route('admin.users.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                    {{ __('Alle') }} →
                </a>
            </div>
            <div class="space-y-3">
                @forelse($recentUsers as $user)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->first_name }} {{ $user->last_name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                        <x-button type="secondary" size="sm" tag="a" :href="route('admin.users.show', $user)">
                            <x-fas-eye class="w-3"/>
                        </x-button>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">{{ __('Keine Benutzer gefunden') }}</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Job Postings -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('Neueste Stellenausschreibungen') }}</h2>
                <a href="{{ route('admin.job-postings.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                    {{ __('Alle') }} →
                </a>
            </div>
            <div class="space-y-3">
                @forelse($recentJobPostings as $jobPosting)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $jobPosting->title }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $jobPosting->facility->name }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($jobPosting->status === 'active')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                    {{ __('Aktiv') }}
                                </span>
                            @elseif($jobPosting->status === 'draft')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300">
                                    {{ __('Entwurf') }}
                                </span>
                            @endif
                            <x-button type="secondary" size="sm" tag="a" :href="route('admin.job-postings.show', $jobPosting)">
                                <x-fas-eye class="w-3"/>
                            </x-button>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">{{ __('Keine Stellenausschreibungen gefunden') }}</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mt-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('Letzte Credit-Transaktionen') }}</h2>
            <a href="{{ route('admin.credits.transactions') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                {{ __('Alle anzeigen') }} →
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Datum') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Typ') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Entität') }}</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Betrag') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($recentTransactions->take(10) as $transaction)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-2 text-xs text-gray-900 dark:text-gray-100">
                                {{ $transaction->created_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="px-4 py-2 text-xs">
                                @if($transaction->type === 'purchase')
                                    <span class="text-blue-600 dark:text-blue-400">{{ __('Kauf') }}</span>
                                @elseif($transaction->type === 'grant')
                                    <span class="text-green-600 dark:text-green-400">{{ __('Gewährt') }}</span>
                                @elseif($transaction->type === 'transfer')
                                    <span class="text-purple-600 dark:text-purple-400">{{ __('Transfer') }}</span>
                                @else
                                    <span class="text-red-600 dark:text-red-400">{{ __('Abzug') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-xs text-gray-900 dark:text-gray-100">
                                {{ $transaction->transactionable->name ?? '-' }}
                            </td>
                            <td class="px-4 py-2 text-xs text-right font-bold {{ $transaction->amount >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $transaction->amount >= 0 ? '+' : '' }}{{ $transaction->amount }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                {{ __('Keine Transaktionen gefunden') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
