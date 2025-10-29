<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use App\Models\Facility;
use App\Services\JobPostingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class JobPostingController extends Controller
{
    public function __construct(
        protected JobPostingService $jobPostingService
    ) {}

    /**
     * Display a listing of job postings
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Get facilities the user has access to
        $facilityIds = $user->facilities()->pluck('facilities.id');

        // Get facilities from organizations
        $orgFacilityIds = $user->organizations()
            ->with('facilities')
            ->get()
            ->pluck('facilities')
            ->flatten()
            ->pluck('id');

        $allFacilityIds = $facilityIds->merge($orgFacilityIds)->unique();

        $query = JobPosting::whereIn('facility_id', $allFacilityIds)
            ->with(['facility', 'creator']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by facility
        if ($request->has('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }

        $jobPostings = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('job-postings.index', compact('jobPostings'));
    }

    /**
     * Show the form for creating a new job posting
     */
    public function create(Request $request)
    {
        // Check if user has at least one approved organization
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $hasApprovedOrg = $user->organizations()->where('is_approved', true)->exists();
        $hasApprovedFacility = $user->facilities()
            ->whereHas('organization', function ($query) {
                $query->where('is_approved', true);
            })
            ->exists();

        if (!$hasApprovedOrg && !$hasApprovedFacility) {
            return redirect()->route('organizations.index')
                ->with('error', 'Sie müssen einer genehmigten Organisation zugeordnet sein, um Stellenausschreibungen zu erstellen.');
        }

        Gate::authorize('create', JobPosting::class);

        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Get facilities the user can post for
        $facilities = $user->facilities;

        // Add facilities from organizations
        $orgFacilities = $user->organizations()
            ->with('facilities')
            ->get()
            ->pluck('facilities')
            ->flatten();

        $facilities = $facilities->merge($orgFacilities)->unique('id');

        $preselectedFacilityId = $request->get('facility_id');

        return view('job-postings.create', compact('facilities', 'preselectedFacilityId'));
    }

    /**
     * Store a newly created job posting
     */
    public function store(Request $request)
    {
        Gate::authorize('create', JobPosting::class);

        $validated = $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'employment_type' => 'required|in:full_time,part_time,mini_job,internship,apprenticeship',
            'job_category' => 'nullable|string|max:255',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
        ]);

        // Check if user has access to this facility
        $facility = Facility::findOrFail($validated['facility_id']);

        // Check if organization is approved
        if (!$facility->organization->canUseFeatures()) {
            return redirect()->route('organizations.show', $facility->organization)
                ->with('error', 'Die Organisation dieser Einrichtung muss erst vom Administrator genehmigt werden, bevor Sie Stellenausschreibungen erstellen können.');
        }

        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (!$user->facilities()->where('facilities.id', $facility->id)->exists() &&
            !$user->organizations()->where('organizations.id', $facility->organization_id)->exists()) {
            abort(403, 'Sie haben keine Berechtigung für diese Einrichtung.');
        }

        $jobPosting = JobPosting::create([
            ...$validated,
            'created_by' => $user->id,
            'status' => JobPosting::STATUS_DRAFT,
        ]);

        return redirect()->route('job-postings.show', $jobPosting)
            ->with('success', 'Stellenausschreibung wurde als Entwurf erstellt.');
    }

    /**
     * Display the specified job posting
     */
    public function show(JobPosting $jobPosting)
    {
        Gate::authorize('view', $jobPosting);

        $jobPosting->load(['facility.address', 'creator']);

        // Get interaction statistics only if user has permission
        $stats = null;
        $uniqueVisitors = null;

        if (auth()->user()->can('view job posting statistics')) {
            $stats = $jobPosting->getInteractionStats();
            $uniqueVisitors = $jobPosting->getUniqueVisitorsCount();
        }

        return view('job-postings.show', compact('jobPosting', 'stats', 'uniqueVisitors'));
    }

    /**
     * Show the form for editing the job posting
     */
    public function edit(JobPosting $jobPosting)
    {
        if (!$jobPosting->facility->organization->canUseFeatures()) {
            return redirect()->route('organizations.show', $jobPosting->facility->organization)
                ->with('error', 'Die Organisation dieser Stellenausschreibung muss erst vom Administrator genehmigt werden.');
        }

        Gate::authorize('update', $jobPosting);

        return view('job-postings.edit', compact('jobPosting'));
    }

    /**
     * Update the specified job posting
     */
    public function update(Request $request, JobPosting $jobPosting)
    {
        if (!$jobPosting->facility->organization->canUseFeatures()) {
            return redirect()->route('organizations.show', $jobPosting->facility->organization)
                ->with('error', 'Die Organisation dieser Stellenausschreibung muss erst vom Administrator genehmigt werden.');
        }

        Gate::authorize('update', $jobPosting);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'employment_type' => 'required|in:full_time,part_time,mini_job,internship,apprenticeship',
            'job_category' => 'nullable|string|max:255',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
        ]);

        $jobPosting->update($validated);

        return redirect()->route('job-postings.show', $jobPosting)
            ->with('success', 'Stellenausschreibung wurde aktualisiert.');
    }

    /**
     * Remove the specified job posting
     */
    public function destroy(JobPosting $jobPosting)
    {
        if (!$jobPosting->facility->organization->canUseFeatures()) {
            return redirect()->route('organizations.show', $jobPosting->facility->organization)
                ->with('error', 'Die Organisation dieser Stellenausschreibung muss erst vom Administrator genehmigt werden.');
        }

        Gate::authorize('delete', $jobPosting);

        $jobPosting->delete();

        return redirect()->route('job-postings.index')
            ->with('success', 'Stellenausschreibung wurde gelöscht.');
    }

    /**
     * Publish a job posting
     */
    public function publish(JobPosting $jobPosting)
    {
        if (!$jobPosting->facility->organization->canUseFeatures()) {
            return redirect()->route('organizations.show', $jobPosting->facility->organization)
                ->with('error', 'Die Organisation dieser Stellenausschreibung muss erst vom Administrator genehmigt werden, bevor Sie veröffentlichen können.');
        }

        Gate::authorize('publish', $jobPosting);

        try {
            /** @var \App\Models\User $user */
            $user = auth()->user();
            $this->jobPostingService->publishJobPosting($jobPosting, $user);

            return redirect()->route('job-postings.show', $jobPosting)
                ->with('success', 'Stellenausschreibung wurde veröffentlicht und ist jetzt für 3 Monate aktiv.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Extend a job posting
     */
    public function extend(JobPosting $jobPosting)
    {
        if (!$jobPosting->facility->organization->canUseFeatures()) {
            return redirect()->route('organizations.show', $jobPosting->facility->organization)
                ->with('error', 'Die Organisation dieser Stellenausschreibung muss erst vom Administrator genehmigt werden.');
        }

        Gate::authorize('extend', $jobPosting);

        try {
            /** @var \App\Models\User $user */
            $user = auth()->user();
            $this->jobPostingService->extendJobPosting($jobPosting, $user);

            return redirect()->route('job-postings.show', $jobPosting)
                ->with('success', 'Stellenausschreibung wurde um 3 Monate verlängert.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Pause a job posting
     */
    public function pause(JobPosting $jobPosting)
    {
        if (!$jobPosting->facility->organization->canUseFeatures()) {
            return redirect()->route('organizations.show', $jobPosting->facility->organization)
                ->with('error', 'Die Organisation dieser Stellenausschreibung muss erst vom Administrator genehmigt werden.');
        }

        Gate::authorize('pause', $jobPosting);

        try {
            $this->jobPostingService->pauseJobPosting($jobPosting);

            return redirect()->route('job-postings.show', $jobPosting)
                ->with('success', 'Stellenausschreibung wurde pausiert.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Resume a job posting
     */
    public function resume(JobPosting $jobPosting)
    {
        if (!$jobPosting->facility->organization->canUseFeatures()) {
            return redirect()->route('organizations.show', $jobPosting->facility->organization)
                ->with('error', 'Die Organisation dieser Stellenausschreibung muss erst vom Administrator genehmigt werden.');
        }

        Gate::authorize('resume', $jobPosting);

        try {
            $this->jobPostingService->resumeJobPosting($jobPosting);

            return redirect()->route('job-postings.show', $jobPosting)
                ->with('success', 'Stellenausschreibung wurde fortgesetzt.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
