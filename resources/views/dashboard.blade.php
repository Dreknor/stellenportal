<x-layouts.app>
    @php
        $cardClasses = 'bg-white dark:bg-gray-800 rounded-2xl border border-sand-200 dark:border-gray-700';
        $statTiles = [
            'my_facilities' => ['label' => __('Meine Einrichtungen'), 'hint' => null],
            'my_job_postings' => ['label' => __('Stellenanzeigen gesamt'), 'hint' => null],
            'my_active_postings' => ['label' => __('Aktive Anzeigen'), 'hint' => __('aktuell öffentlich sichtbar')],
            'my_draft_postings' => ['label' => __('Entwürfe'), 'hint' => __('noch nicht veröffentlicht')],
        ];
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-gray-100">{{ __('Guten Tag, :name', ['name' => Auth::user()->first_name ?? Auth::user()->name]) }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Hier sehen Sie, was in Ihren Einrichtungen gerade ansteht.') }}</p>
        </div>
        @can('create', \App\Models\JobPosting::class)
            <x-button tag="a" :href="route('job-postings.create')" class="self-start sm:self-auto h-11">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                {{ __('Neue Stellenanzeige') }}
            </x-button>
        @endcan
    </div>

    {{-- Guthaben + Statistiken --}}
    @if($creditBalance || !empty($userStats))
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
            @if($creditBalance)
                <section class="sm:col-span-2 bg-primary-800 text-white rounded-2xl p-6 flex flex-wrap items-center justify-between gap-4" aria-label="{{ __('Guthaben') }}">
                    <div>
                        <p class="text-sm text-primary-200">{{ __('Verfügbare Credits') }}</p>
                        <p class="text-4xl sm:text-5xl font-extrabold leading-none mt-1">{{ number_format($creditBalance->balance, 0, ',', '.') }}</p>
                        <p class="text-sm text-primary-100 mt-2">{{ __('1 Credit = 1 Stellenanzeige für 3 Monate') }}</p>
                    </div>
                    <svg class="w-14 h-14 text-primary-300/60 hidden sm:block" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 7v10M9.5 9.5h4a1.5 1.5 0 010 3h-3a1.5 1.5 0 000 3h4"/>
                    </svg>
                </section>
            @endif

            @foreach($statTiles as $key => $tile)
                @isset($userStats[$key])
                    <section class="{{ $cardClasses }} p-5">
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $tile['label'] }}</p>
                        <p class="text-3xl font-extrabold text-gray-900 dark:text-gray-100 mt-2">{{ $userStats[$key] }}</p>
                        @if($tile['hint'])
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $tile['hint'] }}</p>
                        @endif
                    </section>
                @endisset
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        {{-- Meine aktuellen Stellenanzeigen --}}
        @if($recentJobPostings->isNotEmpty())
            <section class="{{ $cardClasses }} lg:col-span-3 overflow-hidden" aria-labelledby="my-postings-heading">
                <div class="flex items-center justify-between gap-4 px-5 sm:px-6 py-4 border-b border-sand-100 dark:border-gray-700">
                    <h2 id="my-postings-heading" class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ __('Meine aktuellen Stellenanzeigen') }}</h2>
                    <a href="{{ route('job-postings.index') }}" class="text-sm font-semibold text-primary-700 dark:text-primary-300 hover:underline whitespace-nowrap">
                        {{ __('Alle anzeigen') }}
                    </a>
                </div>
                <ul class="divide-y divide-sand-100 dark:divide-gray-700">
                    @foreach($recentJobPostings as $jobPosting)
                        <li class="relative px-5 sm:px-6 py-4 hover:bg-sand-50 dark:hover:bg-gray-700/50 transition flex flex-wrap items-center gap-x-4 gap-y-2">
                            <div class="flex-1 min-w-[12rem]">
                                <a href="{{ route('job-postings.show', $jobPosting) }}" class="font-semibold text-gray-900 dark:text-gray-100 hover:text-primary-700 after:absolute after:inset-0">
                                    {{ $jobPosting->title }}
                                </a>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">{{ $jobPosting->facility->name }}</p>
                            </div>
                            <x-job-posting.status-badge :job-posting="$jobPosting" />
                            @if($jobPosting->expires_at && $jobPosting->status === \App\Models\JobPosting::STATUS_ACTIVE)
                                <span class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ __('bis') }} {{ $jobPosting->expires_at->format('d.m.Y') }}</span>
                            @elseif($jobPosting->published_at)
                                <span class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $jobPosting->published_at->diffForHumans() }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        {{-- Neueste aktive Stellenanzeigen --}}
        @if($latestJobPostings->isNotEmpty())
            <section class="{{ $cardClasses }} overflow-hidden {{ $recentJobPostings->isNotEmpty() ? 'lg:col-span-2' : 'lg:col-span-5' }}" aria-labelledby="latest-postings-heading">
                <div class="flex items-center justify-between gap-4 px-5 sm:px-6 py-4 border-b border-sand-100 dark:border-gray-700">
                    <h2 id="latest-postings-heading" class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ __('Neu im Portal') }}</h2>
                    <a href="{{ route('public.jobs.index') }}" class="text-sm font-semibold text-primary-700 dark:text-primary-300 hover:underline whitespace-nowrap">
                        {{ __('Alle Jobs') }}
                    </a>
                </div>
                <ul class="divide-y divide-sand-100 dark:divide-gray-700">
                    @foreach($latestJobPostings->take(5) as $jobPosting)
                        <li class="relative px-5 sm:px-6 py-3.5 hover:bg-sand-50 dark:hover:bg-gray-700/50 transition">
                            <a href="{{ route('public.jobs.show', $jobPosting) }}" class="font-medium text-gray-900 dark:text-gray-100 hover:text-primary-700 after:absolute after:inset-0">
                                {{ $jobPosting->title }}
                            </a>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">
                                {{ $jobPosting->facility->name }}
                                @if($jobPosting->published_at)
                                    · {{ $jobPosting->published_at->format('d.m.Y') }}
                                @endif
                            </p>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    </div>

    {{-- Letzte Credit-Transaktionen --}}
    @if(isset($recentTransactions) && $recentTransactions->isNotEmpty())
        <section class="mt-6 {{ $cardClasses }} overflow-hidden" aria-labelledby="transactions-heading">
            <div class="px-5 sm:px-6 py-4 border-b border-sand-100 dark:border-gray-700">
                <h2 id="transactions-heading" class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ __('Letzte Credit-Transaktionen') }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                    <tr class="text-left text-gray-600 dark:text-gray-400">
                        <th scope="col" class="px-5 sm:px-6 py-3 font-semibold">{{ __('Datum') }}</th>
                        <th scope="col" class="px-4 py-3 font-semibold">{{ __('Typ') }}</th>
                        <th scope="col" class="px-4 py-3 font-semibold">{{ __('Beschreibung') }}</th>
                        <th scope="col" class="px-5 sm:px-6 py-3 font-semibold text-right">{{ __('Betrag') }}</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-sand-100 dark:divide-gray-700">
                    @foreach($recentTransactions as $transaction)
                        @php $isCredit = in_array($transaction->type, ['purchase', 'transfer_in', 'grant']); @endphp
                        <tr>
                            <td class="px-5 sm:px-6 py-3.5 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                {{ $transaction->created_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span @class([
                                    'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold',
                                    'bg-primary-100 text-primary-800 dark:bg-primary-900/50 dark:text-primary-200' => $isCredit,
                                    'bg-sand-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200' => !$isCredit,
                                ])>
                                    {{ __(ucfirst(str_replace('_', ' ', $transaction->type))) }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-gray-600 dark:text-gray-400">
                                {{ $transaction->description ?? '-' }}
                            </td>
                            <td @class([
                                'px-5 sm:px-6 py-3.5 whitespace-nowrap text-right font-semibold',
                                'text-primary-700 dark:text-primary-300' => $isCredit,
                                'text-red-700 dark:text-red-300' => !$isCredit,
                            ])>
                                {{ $isCredit ? '+' : '-' }}{{ abs($transaction->amount) }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif

</x-layouts.app>
