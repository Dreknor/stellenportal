<x-layouts.app>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Such-Analyse</h1>
                        <p class="mt-2 text-gray-600">Einblicke in das Suchverhalten der Nutzer</p>
                    </div>
                    <div>
                        <form method="GET" action="{{ route('admin.search-analytics.index') }}" class="flex items-center gap-2">
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

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Gesamte Suchen</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_searches'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Unique Begriffe</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['unique_queries'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Mit Ergebnissen</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['searches_with_results'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-red-100 rounded-lg">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Ohne Ergebnisse</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['searches_without_results'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Ø Ergebnisse</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['avg_results_per_search'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Popular Searches -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">Beliebte Suchbegriffe</h2>
                        <p class="mt-1 text-sm text-gray-600">Top 20 am häufigsten gesuchte Begriffe</p>
                    </div>
                    <div class="p-6">
                        @if($popularSearches->isEmpty())
                            <p class="text-gray-500 text-center py-8">Keine Suchanfragen im ausgewählten Zeitraum</p>
                        @else
                            <div class="space-y-4">
                                @foreach($popularSearches as $index => $search)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center flex-1">
                                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span class="text-sm font-bold text-blue-600">{{ $index + 1 }}</span>
                                            </div>
                                            <div class="ml-4 flex-1">
                                                <p class="text-sm font-medium text-gray-900">{{ $search->query }}</p>
                                                <p class="text-xs text-gray-500">Ø {{ number_format($search->avg_results, 1) }} Ergebnisse</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-bold text-gray-900">{{ number_format($search->count) }}×</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Popular Locations -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">Beliebte Standorte</h2>
                        <p class="mt-1 text-sm text-gray-600">Top 15 gesuchte Orte</p>
                    </div>
                    <div class="p-6">
                        @if($popularLocations->isEmpty())
                            <p class="text-gray-500 text-center py-8">Keine Standort-Suchen im ausgewählten Zeitraum</p>
                        @else
                            <div class="space-y-4">
                                @foreach($popularLocations as $index => $location)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center flex-1">
                                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                                <span class="text-sm font-bold text-green-600">{{ $index + 1 }}</span>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-medium text-gray-900">{{ $location->location }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-bold text-gray-900">{{ number_format($location->count) }}×</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Searches Without Results (Important!) -->
                <div class="bg-white rounded-lg shadow border-2 border-red-200">
                    <div class="p-6 border-b border-red-200 bg-red-50">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div>
                                <h2 class="text-xl font-bold text-red-900">Suchen ohne Ergebnisse</h2>
                                <p class="mt-1 text-sm text-red-700">Diese Begriffe führten zu keinen Ergebnissen - Content-Chance!</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($noResultSearches->isEmpty())
                            <p class="text-gray-500 text-center py-8">Alle Suchen hatten Ergebnisse! 🎉</p>
                        @else
                            <div class="space-y-3">
                                @foreach($noResultSearches as $search)
                                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                        <p class="text-sm font-medium text-gray-900">{{ $search->query }}</p>
                                        <span class="text-sm text-red-600 font-semibold">{{ number_format($search->count) }}× gesucht</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Employment Type Filters -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">Beschäftigungsart-Filter</h2>
                        <p class="mt-1 text-sm text-gray-600">Verwendung der Filter</p>
                    </div>
                    <div class="p-6">
                        @if($employmentTypeFilters->isEmpty())
                            <p class="text-gray-500 text-center py-8">Keine Filter-Verwendung</p>
                        @else
                            <div class="space-y-4">
                                @foreach($employmentTypeFilters as $filter)
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-gray-900">{{ ucfirst($filter->employment_type) }}</p>
                                        <div class="flex items-center">
                                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                                <div class="bg-purple-600 h-2 rounded-full" style="width: {{ min(100, ($filter->count / $stats['total_searches']) * 100) }}%"></div>
                                            </div>
                                            <span class="text-sm font-bold text-gray-900">{{ number_format($filter->count) }}×</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Daily Trend Chart -->
            @if($dailySearches->isNotEmpty())
                <div class="bg-white rounded-lg shadow mt-8">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">Such-Trend</h2>
                        <p class="mt-1 text-sm text-gray-600">Anzahl der Suchen pro Tag</p>
                    </div>
                    <div class="p-6">
                        <div class="h-64 flex items-end justify-between gap-2">
                            @php
                                $maxCount = $dailySearches->max('count');
                            @endphp
                            @foreach($dailySearches as $day)
                                <div class="flex-1 flex flex-col items-center">
                                    <div class="w-full bg-blue-500 hover:bg-blue-600 transition rounded-t relative group"
                                         style="height: {{ $maxCount > 0 ? ($day->count / $maxCount * 100) : 0 }}%">
                                        <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
                                            {{ number_format($day->count) }} Suchen
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-2 transform -rotate-45 origin-top-left">
                                        {{ \Carbon\Carbon::parse($day->date)->format('d.m') }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Recent Searches -->
            <div class="bg-white rounded-lg shadow mt-8">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Aktuelle Suchen</h2>
                    <p class="mt-1 text-sm text-gray-600">Die letzten 30 Suchanfragen</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zeitpunkt</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Suchbegriff</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Standort</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Beschäftigungsart</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ergebnisse</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Benutzer</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentSearches as $search)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $search->created_at->format('d.m.Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-gray-900">{{ $search->query ?: '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $search->location ?: '-' }}
                                        @if($search->radius)
                                            <span class="text-xs text-gray-400">({{ $search->radius }}km)</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $search->employment_type ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($search->results_count > 0)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ $search->results_count }}
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                0
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($search->user)
                                            {{ $search->user->name }}
                                        @else
                                            <span class="text-gray-400 italic">Gast</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

