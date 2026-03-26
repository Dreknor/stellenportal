<x-layouts.app>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Interaktions-Statistiken</h1>
                        <p class="mt-2 text-gray-600">Zentrale Auswertung aller Klicks, Aufrufe und Downloads</p>
                    </div>
                    <div>
                        <form method="GET" action="{{ route('admin.interaction-analytics.index') }}" class="flex items-center gap-2">
                            <label for="period" class="text-sm font-medium text-gray-700">Zeitraum:</label>
                            <select name="period" id="period" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="7" {{ $period == 7 ? 'selected' : '' }}>Letzte 7 Tage</option>
                                <option value="30" {{ $period == 30 ? 'selected' : '' }}>Letzte 30 Tage</option>
                                <option value="90" {{ $period == 90 ? 'selected' : '' }}>Letzte 90 Tage</option>
                                <option value="365" {{ $period == 365 ? 'selected' : '' }}>Letztes Jahr</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
                <!-- Views -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Aufrufe</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['views'], 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-3">
                            <x-fas-eye class="w-6 h-6 text-blue-600" />
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        {{ number_format($uniqueVisitors, 0, ',', '.') }} eindeutige Besucher
                    </p>
                </div>

                <!-- Apply Clicks -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Bewerbungsklicks</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['apply_clicks'], 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3">
                            <x-fas-paper-plane class="w-6 h-6 text-green-600" />
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        {{ $conversionRate }}% Conversion-Rate
                    </p>
                </div>

                <!-- Email Reveals -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">E-Mail angezeigt</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['email_reveals'], 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-purple-100 rounded-full p-3">
                            <x-fas-envelope class="w-6 h-6 text-purple-600" />
                        </div>
                    </div>
                </div>

                <!-- Phone Reveals -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Telefon angezeigt</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['phone_reveals'], 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-yellow-100 rounded-full p-3">
                            <x-fas-phone class="w-6 h-6 text-yellow-600" />
                        </div>
                    </div>
                </div>

                <!-- Downloads -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">PDF-Downloads</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['downloads'], 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-red-100 rounded-full p-3">
                            <x-fas-download class="w-6 h-6 text-red-600" />
                        </div>
                    </div>
                </div>

                <!-- Total Interactions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Gesamt</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_interactions'], 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-indigo-100 rounded-full p-3">
                            <x-fas-mouse-pointer class="w-6 h-6 text-indigo-600" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Trend Chart -->
            @if($dailyTrend->isNotEmpty())
                <div class="bg-white rounded-lg shadow mb-8">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">Täglicher Verlauf</h2>
                        <p class="mt-1 text-sm text-gray-600">Interaktionen pro Tag</p>
                        <div class="flex items-center gap-4 mt-3 text-xs">
                            <span class="flex items-center gap-1"><span class="inline-block w-3 h-3 rounded bg-blue-500"></span> Aufrufe</span>
                            <span class="flex items-center gap-1"><span class="inline-block w-3 h-3 rounded bg-green-500"></span> Bewerbungen</span>
                            <span class="flex items-center gap-1"><span class="inline-block w-3 h-3 rounded bg-red-500"></span> Downloads</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="h-64 flex items-end justify-between gap-1">
                            @php
                                $maxTotal = $dailyTrend->max('total') ?: 1;
                            @endphp
                            @foreach($dailyTrend as $date => $day)
                                <div class="flex-1 flex flex-col items-center group relative">
                                    {{-- Stacked bar --}}
                                    <div class="w-full flex flex-col-reverse" style="height: {{ ($day['total'] / $maxTotal) * 100 }}%">
                                        @if($day['views'] > 0)
                                            <div class="w-full bg-blue-500 hover:bg-blue-600 transition"
                                                 style="height: {{ ($day['views'] / $day['total']) * 100 }}%"></div>
                                        @endif
                                        @if($day['apply_clicks'] > 0)
                                            <div class="w-full bg-green-500 hover:bg-green-600 transition"
                                                 style="height: {{ ($day['apply_clicks'] / $day['total']) * 100 }}%"></div>
                                        @endif
                                        @if($day['downloads'] > 0)
                                            <div class="w-full bg-red-500 hover:bg-red-600 transition"
                                                 style="height: {{ ($day['downloads'] / $day['total']) * 100 }}%"></div>
                                        @endif
                                    </div>
                                    {{-- Tooltip --}}
                                    <div class="absolute -top-16 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs rounded py-2 px-3 opacity-0 group-hover:opacity-100 transition whitespace-nowrap z-10 pointer-events-none">
                                        <strong>{{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}</strong><br>
                                        {{ $day['views'] }} Aufrufe · {{ $day['apply_clicks'] }} Bewerbungen · {{ $day['downloads'] }} Downloads
                                    </div>
                                    @if($dailyTrend->count() <= 31)
                                        <p class="text-xs text-gray-600 mt-2 transform -rotate-45 origin-top-left">
                                            {{ \Carbon\Carbon::parse($date)->format('d.m') }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Top Job Postings Table -->
            <div class="bg-white rounded-lg shadow mb-8">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Top Stellenausschreibungen</h2>
                    <p class="mt-1 text-sm text-gray-600">Die 20 meistgenutzten Stellenausschreibungen nach Interaktionen</p>
                </div>
                <div class="overflow-x-auto">
                    @if($topJobPostings->isEmpty())
                        <p class="text-gray-500 text-center py-8">Keine Interaktionen im ausgewählten Zeitraum</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stellenausschreibung</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Einrichtung</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <x-fas-eye class="w-3.5 h-3.5 inline-block" /> Aufrufe
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <x-fas-paper-plane class="w-3.5 h-3.5 inline-block" /> Bewerbungen
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <x-fas-envelope class="w-3.5 h-3.5 inline-block" /> E-Mail
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <x-fas-phone class="w-3.5 h-3.5 inline-block" /> Telefon
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <x-fas-download class="w-3.5 h-3.5 inline-block" /> Downloads
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Conversion</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($topJobPostings as $index => $posting)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4">
                                            <a href="{{ route('admin.job-postings.show', $posting->slug) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline">
                                                {{ \Illuminate\Support\Str::limit($posting->title, 50) }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ \Illuminate\Support\Str::limit($posting->facility_name, 30) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @switch($posting->status)
                                                @case('active')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktiv</span>
                                                    @break
                                                @case('draft')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Entwurf</span>
                                                    @break
                                                @case('expired')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Abgelaufen</span>
                                                    @break
                                                @case('paused')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pausiert</span>
                                                    @break
                                                @default
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $posting->status }}</span>
                                            @endswitch
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900">{{ number_format($posting->views_count, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-green-700">{{ number_format($posting->apply_clicks_count, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-700">{{ number_format($posting->email_reveals_count, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-700">{{ number_format($posting->phone_reveals_count, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-700">{{ number_format($posting->downloads_count, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                            @if($posting->views_count > 0)
                                                <span class="font-semibold {{ ($posting->apply_clicks_count / $posting->views_count) * 100 >= 5 ? 'text-green-700' : 'text-gray-700' }}">
                                                    {{ number_format(($posting->apply_clicks_count / $posting->views_count) * 100, 1) }}%
                                                </span>
                                            @else
                                                <span class="text-gray-400">–</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Hourly Distribution -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">Aufrufe nach Tageszeit</h2>
                        <p class="mt-1 text-sm text-gray-600">Verteilung der Seitenaufrufe über den Tag</p>
                    </div>
                    <div class="p-6">
                        @php
                            $maxHourly = count($hourlyDistribution) > 0 ? max($hourlyDistribution) : 1;
                        @endphp
                        <div class="h-48 flex items-end justify-between gap-0.5">
                            @for($h = 0; $h < 24; $h++)
                                @php $count = $hourlyDistribution[$h] ?? 0; @endphp
                                <div class="flex-1 flex flex-col items-center group relative">
                                    <div class="w-full bg-indigo-400 hover:bg-indigo-500 transition rounded-t"
                                         style="height: {{ $maxHourly > 0 ? ($count / $maxHourly * 100) : 0 }}%">
                                    </div>
                                    <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition whitespace-nowrap z-10 pointer-events-none">
                                        {{ $count }} Aufrufe um {{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00
                                    </div>
                                    @if($h % 3 === 0)
                                        <p class="text-xs text-gray-600 mt-1">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}</p>
                                    @endif
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <!-- Recent Conversions -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">Letzte Bewerbungsklicks & Downloads</h2>
                        <p class="mt-1 text-sm text-gray-600">Die aktuellsten Conversion-Ereignisse</p>
                    </div>
                    <div class="p-6">
                        @if($recentConversions->isEmpty())
                            <p class="text-gray-500 text-center py-8">Keine Conversions im Zeitraum</p>
                        @else
                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                @foreach($recentConversions as $interaction)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center gap-3">
                                            @if($interaction->interaction_type === 'apply_click')
                                                <div class="bg-green-100 rounded-full p-2">
                                                    <x-fas-paper-plane class="w-4 h-4 text-green-600" />
                                                </div>
                                            @else
                                                <div class="bg-red-100 rounded-full p-2">
                                                    <x-fas-download class="w-4 h-4 text-red-600" />
                                                </div>
                                            @endif
                                            <div>
                                                @if($interaction->jobPosting)
                                                    <a href="{{ route('admin.job-postings.show', $interaction->jobPosting->slug) }}" class="text-sm font-medium text-blue-600 hover:underline">
                                                        {{ \Illuminate\Support\Str::limit($interaction->jobPosting->title, 40) }}
                                                    </a>
                                                @else
                                                    <span class="text-sm text-gray-400 italic">Gelöscht</span>
                                                @endif
                                                <p class="text-xs text-gray-500">
                                                    {{ $interaction->interaction_type === 'apply_click' ? 'Bewerbungsklick' : 'PDF-Download' }}
                                                </p>
                                            </div>
                                        </div>
                                        <span class="text-xs text-gray-500 whitespace-nowrap">
                                            {{ $interaction->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

