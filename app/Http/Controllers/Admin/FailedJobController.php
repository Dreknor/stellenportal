<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class FailedJobController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 25;
        $page = max(1, (int) $request->get('page', 1));

        $q = $request->get('q');
        $queue = $request->get('queue');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $base = DB::table('failed_jobs');

        // Build filtered query
        $query = clone $base;
        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('exception', 'like', "%{$q}%")
                    ->orWhere('payload', 'like', "%{$q}%");
            });
        }

        if ($queue) {
            $query->where('queue', $queue);
        }

        if ($dateFrom) {
            $query->where('failed_at', '>=', $dateFrom.' 00:00:00');
        }

        if ($dateTo) {
            $query->where('failed_at', '<=', $dateTo.' 23:59:59');
        }

        $query = $query->orderBy('failed_at', 'desc');

        $total = $query->count();
        $jobs = $query->forPage($page, $perPage)->get();

        // Distinct queues for filter select
        $queues = $base->select('queue')->distinct()->orderBy('queue')->pluck('queue')->filter()->values();

        return view('admin.failed_jobs.index', [
            'jobs' => $jobs,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'queues' => $queues,
            'filters' => [
                'q' => $q,
                'queue' => $queue,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);
    }

    public function show($id)
    {
        $job = DB::table('failed_jobs')->where('id', $id)->first();
        if (!$job) abort(404);

        // Decode payload if possible
        $payload = $job->payload;
        $decoded = null;
        try {
            $decoded = json_decode($payload, true);
        } catch (\Throwable $e) {
            $decoded = null;
        }

        return view('admin.failed_jobs.show', ['job' => $job, 'decoded' => $decoded]);
    }

    public function retry($id)
    {
        $job = DB::table('failed_jobs')->where('id', $id)->first();
        if (!$job) abort(404);

        try {
            // Use Laravel's own retry (handles encrypted jobs, no manual unserialize)
            Artisan::call('queue:retry', ['id' => [$job->uuid]]);

            return redirect()->back()->with('success', 'Job erneut in die Queue gestellt');
        } catch (\Throwable $e) {
            Log::error('Fehler beim erneuten Starten eines fehlgeschlagenen Jobs', ['id' => $id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Fehler beim erneuten Starten des Jobs. Details stehen im Log.');
        }
    }

    public function destroy($id)
    {
        DB::table('failed_jobs')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Fehlgeschlagener Job gelöscht');
    }
}
