<?php

namespace App\Http\Controllers\Admin;

use App\Models\LogEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;

class LogController extends Controller
{
    /**
     * Display a listing of log entries from database.
     */
    public function index(Request $request)
    {
        $query = LogEntry::query()->orderBy('created_at', 'desc');

        // Filter by level
        if ($request->filled('level') && $request->level !== 'all') {
            $query->level($request->level);
        }

        // Filter by channel
        if ($request->filled('channel') && $request->channel !== 'all') {
            $query->channel($request->channel);
        }

        // Search in messages
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Get available levels and channels for filters
        $levels = LogEntry::query()
            ->select('level_name')
            ->distinct()
            ->pluck('level_name')
            ->sort()
            ->values();

        $channels = LogEntry::query()
            ->select('channel')
            ->distinct()
            ->whereNotNull('channel')
            ->pluck('channel')
            ->sort()
            ->values();

        // Statistics
        $stats = [
            'total' => LogEntry::count(),
            'error' => LogEntry::whereIn('level_name', ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'])->count(),
            'warning' => LogEntry::level('WARNING')->count(),
            'info' => LogEntry::level('INFO')->count(),
        ];

        $logs = $query->paginate(50)->withQueryString();

        return view('admin.logs.index', compact('logs', 'levels', 'channels', 'stats'));
    }

    /**
     * Display the specified log entry.
     */
    public function show(Request $request, $id)
    {
        $log = LogEntry::findOrFail($id);

        return view('admin.logs.show', compact('log'));
    }

    /**
     * Delete old log entries.
     */
    public function clear(Request $request)
    {
        $request->validate([
            'days' => 'nullable|integer|min:1|max:365',
        ]);

        $days = $request->input('days', 30);
        $date = now()->subDays($days);

        $deleted = LogEntry::where('created_at', '<', $date)->delete();

        return redirect()->route('admin.logs.index')
            ->with('success', __('Es wurden :count Log-Einträge gelöscht, die älter als :days Tage waren.', [
                'count' => $deleted,
                'days' => $days
            ]));
    }

    /**
     * Export logs as JSON.
     */
    public function export(Request $request)
    {
        $query = LogEntry::query()->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('level') && $request->level !== 'all') {
            $query->level($request->level);
        }

        if ($request->filled('channel') && $request->channel !== 'all') {
            $query->channel($request->channel);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $logs = $query->limit(1000)->get();

        $filename = 'logs-export-' . now()->format('Y-m-d-H-i-s') . '.json';

        return Response::json($logs, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}

