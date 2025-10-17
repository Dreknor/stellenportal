<x-layouts.app>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Dashboard') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Willkommen in Ihrem Jobportal-Dashboard') }}</p>
    </div>


    {{-- Benutzerspezifische Statistiken --}}
    @if(!empty($userStats))
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Meine Statistiken') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @isset($userStats['my_facilities'])
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-lg p-6 border border-blue-200 dark:border-blue-700">
                        <p class="text-sm font-medium text-blue-700 dark:text-blue-300">{{ __('Meine Einrichtungen') }}</p>
                        <p class="text-3xl font-bold text-blue-900 dark:text-blue-100 mt-2">{{ $userStats['my_facilities'] }}</p>
                    </div>
                @endisset

                @isset($userStats['my_job_postings'])
                    <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 rounded-lg p-6 border border-green-200 dark:border-green-700">
                        <p class="text-sm font-medium text-green-700 dark:text-green-300">{{ __('Gesamte Stellenanzeigen') }}</p>
                        <p class="text-3xl font-bold text-green-900 dark:text-green-100 mt-2">{{ $userStats['my_job_postings'] }}</p>
                    </div>
                @endisset

                @isset($userStats['my_active_postings'])
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900 dark:to-purple-800 rounded-lg p-6 border border-purple-200 dark:border-purple-700">
                        <p class="text-sm font-medium text-purple-700 dark:text-purple-300">{{ __('Aktive Anzeigen') }}</p>
                        <p class="text-3xl font-bold text-purple-900 dark:text-purple-100 mt-2">{{ $userStats['my_active_postings'] }}</p>
                    </div>
                @endisset

                @isset($userStats['my_draft_postings'])
                    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900 dark:to-yellow-800 rounded-lg p-6 border border-yellow-200 dark:border-yellow-700">
                        <p class="text-sm font-medium text-yellow-700 dark:text-yellow-300">{{ __('Entwürfe') }}</p>
                        <p class="text-3xl font-bold text-yellow-900 dark:text-yellow-100 mt-2">{{ $userStats['my_draft_postings'] }}</p>
                    </div>
                @endisset
            </div>
        </div>
    @endif

    {{-- Credit-Saldo --}}
    @if($creditBalance)
        <div class="mb-6">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-90">{{ __('Guthaben') }}</p>
                        <p class="text-4xl font-bold mt-2">{{ number_format($creditBalance->balance, 0, ',', '.') }}</p>
                        <p class="text-sm opacity-75 mt-1">{{ __('Verfügbare Credits') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Meine aktuellen Stellenanzeigen --}}
        @if($recentJobPostings->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('Meine aktuellen Stellenanzeigen') }}</h2>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($recentJobPostings as $jobPosting)
                        <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <a href="{{ route('job-postings.show', $jobPosting) }}" class="text-sm font-medium text-gray-900 dark:text-gray-100 hover:text-blue-600">
                                        {{ $jobPosting->title }}
                                    </a>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $jobPosting->facility->name }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-2">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                    @if($jobPosting->status === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @elseif($jobPosting->status === 'draft') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                    @elseif($jobPosting->status === 'expired') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                    @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                    @endif">
                                    {{ __(ucfirst($jobPosting->status)) }}
                                </span>
                                        @if($jobPosting->published_at)
                                            <span class="text-xs text-gray-500">{{ $jobPosting->published_at->diffForHumans() }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-900">
                    <a href="{{ route('job-postings.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        {{ __('Alle meine Stellenanzeigen anzeigen') }} →
                    </a>
                </div>
            </div>
        @endif

        {{-- Neueste aktive Stellenanzeigen --}}
        @if($latestJobPostings->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('Neueste aktive Stellenanzeigen') }}</h2>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-96 overflow-y-auto">
                    @foreach($latestJobPostings->take(5) as $jobPosting)
                        <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <a href="{{ route('public.jobs.show', $jobPosting) }}" class="block">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 hover:text-blue-600">
                                    {{ $jobPosting->title }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $jobPosting->facility->name }}
                                    @if($jobPosting->facility->organization)
                                        • {{ $jobPosting->facility->organization->name }}
                                    @endif
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                    {{ $jobPosting->published_at?->format('d.m.Y') }}
                                </p>
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-900">
                    <a href="{{ route('public.jobs.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        {{ __('Alle aktiven Jobs anzeigen') }} →
                    </a>
                </div>
            </div>
        @endif
    </div>

    {{-- Letzte Credit-Transaktionen --}}
    @if(isset($recentTransactions) && $recentTransactions->isNotEmpty())
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('Letzte Credit-Transaktionen') }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Datum') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Typ') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Beschreibung') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Betrag') }}</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($recentTransactions as $transaction)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $transaction->created_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($transaction->type === 'purchase') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                @elseif($transaction->type === 'usage') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                @elseif($transaction->type === 'transfer_in') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                @else bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300
                                @endif">
                                {{ __(ucfirst(str_replace('_', ' ', $transaction->type))) }}
                            </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $transaction->description ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium
                            @if(in_array($transaction->type, ['purchase', 'transfer_in', 'grant'])) text-green-600 dark:text-green-400
                            @else text-red-600 dark:text-red-400
                            @endif">
                                {{ in_array($transaction->type, ['purchase', 'transfer_in', 'grant']) ? '+' : '-' }}{{ abs($transaction->amount) }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</x-layouts.app>
