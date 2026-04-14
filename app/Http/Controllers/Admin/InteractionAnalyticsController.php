<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobPostingInteraction;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InteractionAnalyticsController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display interaction analytics dashboard
     */
    public function index(Request $request)
    {
        $period = (int) $request->get('period', 30);

        $since = now()->subDays($period);

        // Aggregate totals per interaction type
        $totals = JobPostingInteraction::where('created_at', '>=', $since)
            ->selectRaw('interaction_type, COUNT(*) as count')
            ->groupBy('interaction_type')
            ->pluck('count', 'interaction_type')
            ->toArray();

        $stats = [
            'views' => $totals[JobPostingInteraction::TYPE_VIEW] ?? 0,
            'apply_clicks' => $totals[JobPostingInteraction::TYPE_APPLY_CLICK] ?? 0,
            'email_reveals' => $totals[JobPostingInteraction::TYPE_EMAIL_REVEAL] ?? 0,
            'phone_reveals' => $totals[JobPostingInteraction::TYPE_PHONE_REVEAL] ?? 0,
            'downloads' => $totals[JobPostingInteraction::TYPE_DOWNLOAD] ?? 0,
            'total_interactions' => array_sum($totals),
        ];

        // Unique visitors (distinct IPs with view interactions)
        $uniqueVisitors = JobPostingInteraction::where('created_at', '>=', $since)
            ->where('interaction_type', JobPostingInteraction::TYPE_VIEW)
            ->distinct('ip_address')
            ->count('ip_address');

        // Conversion rate
        $conversionRate = $stats['views'] > 0
            ? round(($stats['apply_clicks'] / $stats['views']) * 100, 1)
            : 0;

        // Daily trend
        $dailyInteractions = JobPostingInteraction::where('created_at', '>=', $since)
            ->selectRaw('DATE(created_at) as date, interaction_type, COUNT(*) as count')
            ->groupBy('date', 'interaction_type')
            ->orderBy('date')
            ->get();

        // Pivot daily data: group by date, then by type
        $dailyTrend = $dailyInteractions->groupBy('date')->map(function ($items) {
            $byType = $items->pluck('count', 'interaction_type')->toArray();
            return [
                'views' => $byType[JobPostingInteraction::TYPE_VIEW] ?? 0,
                'apply_clicks' => $byType[JobPostingInteraction::TYPE_APPLY_CLICK] ?? 0,
                'downloads' => $byType[JobPostingInteraction::TYPE_DOWNLOAD] ?? 0,
                'total' => $items->sum('count'),
            ];
        });

        // Top job postings by total interactions
        $topJobPostings = JobPostingInteraction::where('job_posting_interactions.created_at', '>=', $since)
            ->join('job_postings', 'job_postings.id', '=', 'job_posting_interactions.job_posting_id')
            ->join('facilities', 'facilities.id', '=', 'job_postings.facility_id')
            ->select(
                'job_postings.id',
                'job_postings.title',
                'job_postings.slug',
                'job_postings.status',
                'facilities.name as facility_name',
                DB::raw("SUM(CASE WHEN interaction_type = '" . JobPostingInteraction::TYPE_VIEW . "' THEN 1 ELSE 0 END) as views_count"),
                DB::raw("SUM(CASE WHEN interaction_type = '" . JobPostingInteraction::TYPE_APPLY_CLICK . "' THEN 1 ELSE 0 END) as apply_clicks_count"),
                DB::raw("SUM(CASE WHEN interaction_type = '" . JobPostingInteraction::TYPE_EMAIL_REVEAL . "' THEN 1 ELSE 0 END) as email_reveals_count"),
                DB::raw("SUM(CASE WHEN interaction_type = '" . JobPostingInteraction::TYPE_PHONE_REVEAL . "' THEN 1 ELSE 0 END) as phone_reveals_count"),
                DB::raw("SUM(CASE WHEN interaction_type = '" . JobPostingInteraction::TYPE_DOWNLOAD . "' THEN 1 ELSE 0 END) as downloads_count"),
                DB::raw('COUNT(*) as total_interactions'),
            )
            ->groupBy('job_postings.id', 'job_postings.title', 'job_postings.slug', 'job_postings.status', 'facilities.name')
            ->orderByDesc('total_interactions')
            ->limit(20)
            ->get();

        // Interactions by hour of day (for heatmap-style display)
        $driver = DB::getDriverName();
        $hourExpression = $driver === 'sqlite'
            ? "CAST(strftime('%H', created_at) AS INTEGER)"
            : 'HOUR(created_at)';

        $hourlyDistribution = JobPostingInteraction::where('created_at', '>=', $since)
            ->where('interaction_type', JobPostingInteraction::TYPE_VIEW)
            ->selectRaw("$hourExpression as hour, COUNT(*) as count")
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        // Recent notable interactions (last 30 apply clicks / downloads)
        $recentConversions = JobPostingInteraction::with('jobPosting')
            ->whereIn('interaction_type', [
                JobPostingInteraction::TYPE_APPLY_CLICK,
                JobPostingInteraction::TYPE_DOWNLOAD,
            ])
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        return view('admin.interaction-analytics.index', compact(
            'stats',
            'uniqueVisitors',
            'conversionRate',
            'dailyTrend',
            'topJobPostings',
            'hourlyDistribution',
            'recentConversions',
            'period',
        ));
    }
}


