<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = Audit::with(['user']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('auditable_type', 'like', "%{$search}%")
                    ->orWhere('event', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        // Filter by event type
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // Filter by auditable type
        if ($request->filled('auditable_type')) {
            $query->where('auditable_type', 'like', "%{$request->auditable_type}%");
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $audits = $query->latest()->paginate(50)->withQueryString();

        // Get unique event types and auditable types for filters
        $eventTypes = Audit::distinct()->pluck('event');
        $auditableTypes = Audit::distinct()
            ->pluck('auditable_type')
            ->map(function ($type) {
                return class_basename($type);
            })
            ->unique();

        return view('admin.audits.index', compact('audits', 'eventTypes', 'auditableTypes'));
    }

    public function show(Audit $audit)
    {
        $audit->load(['user']);

        return view('admin.audits.show', compact('audit'));
    }

    public function export(Request $request)
    {
        $query = Audit::with(['user']);

        // Apply same filters as index
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }
        if ($request->filled('auditable_type')) {
            $query->where('auditable_type', 'like', "%{$request->auditable_type}%");
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $audits = $query->latest()->get();

        $csv = "ID,Event,Model,User,IP Address,User Agent,Created At\n";

        foreach ($audits as $audit) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s\n",
                $audit->id,
                $audit->event,
                class_basename($audit->auditable_type),
                $audit->user ? $audit->user->name : 'System',
                $audit->ip_address,
                $audit->user_agent,
                $audit->created_at
            );
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="audit-logs-' . now()->format('Y-m-d') . '.csv"');
    }
}
