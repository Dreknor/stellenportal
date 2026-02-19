<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use App\Models\Facility;
use App\Services\JobPostingService;
use Illuminate\Http\Request;

class JobPostingController extends Controller
{
    public function __construct(
        protected JobPostingService $jobPostingService
    ) {
    }

    public function index(Request $request)
    {
        $query = JobPosting::with(['facility.organization', 'facility.address']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;

            // Split search string into individual terms
            $searchTerms = array_filter(array_map('trim', explode(' ', $search)));

            // Each search term must match at least one field (AND logic between terms)
            foreach ($searchTerms as $term) {
                $query->where(function ($q) use ($term) {
                    $q->where('title', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%")
                        ->orWhere('employment_type', 'like', "%{$term}%")
                        ->orWhereHas('facility', function ($fq) use ($term) {
                            $fq->where('name', 'like', "%{$term}%");
                        });
                });
            }
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by facility
        if ($request->filled('facility')) {
            $query->where('facility_id', $request->facility);
        }

        $jobPostings = $query->latest('published_at')->paginate(20)->withQueryString();
        $facilities = Facility::with('organization')->get();

        return view('admin.job-postings.index', compact('jobPostings', 'facilities'));
    }

    public function show(JobPosting $jobPosting)
    {
        $jobPosting->load(['facility.organization', 'facility.address', 'audits']);

        // Get interaction statistics (always available for admins)
        $stats = $jobPosting->getInteractionStats();
        $uniqueVisitors = $jobPosting->getUniqueVisitorsCount();

        return view('admin.job-postings.show', compact('jobPosting', 'stats', 'uniqueVisitors'));
    }

    public function edit(JobPosting $jobPosting)
    {
        $facilities = Facility::with('organization')->get();
        return view('admin.job-postings.edit', compact('jobPosting', 'facilities'));
    }

    public function update(Request $request, JobPosting $jobPosting)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'requirements' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'employment_type' => ['required', 'string'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0'],
            'contact_email' => ['nullable', 'email'],
            'contact_phone' => ['nullable', 'string'],
        ]);

        $jobPosting->update($validated);

        return redirect()->route('admin.job-postings.show', $jobPosting)
            ->with('success', 'Stellenausschreibung erfolgreich aktualisiert.');
    }

    public function destroy(JobPosting $jobPosting)
    {
        $jobPosting->delete();

        return redirect()->route('admin.job-postings.index')
            ->with('success', 'Stellenausschreibung erfolgreich gelöscht.');
    }

    public function publish(JobPosting $jobPosting)
    {
        try {
            $this->jobPostingService->publishJobPosting($jobPosting, auth()->user());
            return back()->with('success', 'Stellenausschreibung erfolgreich veröffentlicht.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function pause(JobPosting $jobPosting)
    {
        try {
            $this->jobPostingService->pauseJobPosting($jobPosting);
            return back()->with('success', 'Stellenausschreibung erfolgreich pausiert.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function resume(JobPosting $jobPosting)
    {
        try {
            $this->jobPostingService->resumeJobPosting($jobPosting);
            return back()->with('success', 'Stellenausschreibung erfolgreich fortgesetzt.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function extend(Request $request, JobPosting $jobPosting)
    {
        try {
            $this->jobPostingService->extendJobPosting($jobPosting, auth()->user(), 3);
            return back()->with('success', 'Stellenausschreibung erfolgreich verlängert.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
