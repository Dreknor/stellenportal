<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SearchQuery;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchAnalyticsController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display search analytics dashboard
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', \App\Models\User::class); // Nur für Admins

        $period = $request->get('period', 7); // Standard: letzte 7 Tage

        // Beliebte Suchbegriffe
        $popularSearches = SearchQuery::recent($period)
            ->whereNotNull('query')
            ->where('query', '!=', '')
            ->select('query', DB::raw('COUNT(*) as count'), DB::raw('AVG(results_count) as avg_results'))
            ->groupBy('query')
            ->orderByDesc('count')
            ->limit(20)
            ->get();

        // Beliebte Standorte
        $popularLocations = SearchQuery::recent($period)
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->select('location', DB::raw('COUNT(*) as count'))
            ->groupBy('location')
            ->orderByDesc('count')
            ->limit(15)
            ->get();

        // Suchen ohne Ergebnisse (wichtig für Content-Strategie!)
        $noResultSearches = SearchQuery::recent($period)
            ->withoutResults()
            ->whereNotNull('query')
            ->select('query', DB::raw('COUNT(*) as count'))
            ->groupBy('query')
            ->orderByDesc('count')
            ->limit(15)
            ->get();

        // Statistiken
        $stats = [
            'total_searches' => SearchQuery::recent($period)->count(),
            'unique_queries' => SearchQuery::recent($period)->distinct('query')->count('query'),
            'searches_with_results' => SearchQuery::recent($period)->where('results_count', '>', 0)->count(),
            'searches_without_results' => SearchQuery::recent($period)->where('results_count', 0)->count(),
            'avg_results_per_search' => round(SearchQuery::recent($period)->avg('results_count'), 2),
        ];

        // Beschäftigungsarten-Filter
        $employmentTypeFilters = SearchQuery::recent($period)
            ->whereNotNull('employment_type')
            ->select('employment_type', DB::raw('COUNT(*) as count'))
            ->groupBy('employment_type')
            ->orderByDesc('count')
            ->get();

        // Suchtrends (täglich)
        $dailySearches = SearchQuery::where('created_at', '>=', now()->subDays($period))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Aktuelle Suchen
        $recentSearches = SearchQuery::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        return view('admin.search-analytics.index', compact(
            'popularSearches',
            'popularLocations',
            'noResultSearches',
            'stats',
            'employmentTypeFilters',
            'dailySearches',
            'recentSearches',
            'period'
        ));
    }
}

