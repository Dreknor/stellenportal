<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchQuery extends Model
{
    protected $fillable = [
        'query',
        'location',
        'radius',
        'employment_type',
        'results_count',
        'ip_address',
        'user_agent',
        'user_id',
    ];

    protected $casts = [
        'radius' => 'integer',
        'results_count' => 'integer',
    ];

    /**
     * Get the user who performed the search
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get popular search queries
     */
    public function scopePopular($query, $limit = 10)
    {
        return $query->whereNotNull('query')
            ->selectRaw('query, COUNT(*) as search_count')
            ->groupBy('query')
            ->orderByDesc('search_count')
            ->limit($limit);
    }

    /**
     * Scope to get recent searches
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope to get searches with no results
     */
    public function scopeWithoutResults($query)
    {
        return $query->where('results_count', 0);
    }

    /**
     * Get popular locations
     */
    public function scopePopularLocations($query, $limit = 10)
    {
        return $query->whereNotNull('location')
            ->selectRaw('location, COUNT(*) as search_count')
            ->groupBy('location')
            ->orderByDesc('search_count')
            ->limit($limit);
    }
}
