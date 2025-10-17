<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use App\Services\JobPostingService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PublicJobPostingController extends Controller
{
    public function __construct(
        protected JobPostingService $jobPostingService
    ) {}

    /**
     * Display public job postings
     */
    public function index(Request $request)
    {
        $query = JobPosting::active()
            ->with(['facility.address', 'facility.organization']);

        // Search by keyword
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('job_category', 'like', "%{$search}%");
            });
        }

        // Filter by employment type
        if ($request->has('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        // Filter by job category
        if ($request->has('job_category')) {
            $query->where('job_category', 'like', "%{$request->job_category}%");
        }

        // Location-based search
        if ($request->has('latitude') && $request->has('longitude')) {
            $radius = $request->get('radius', 50); // Default 50km

            $jobPostings = $this->jobPostingService->searchByRadius(
                $request->latitude,
                $request->longitude,
                $radius
            );
        } else {
            $jobPostings = $query->orderBy('published_at', 'desc')->paginate(20);
        }

        return view('public.job-postings.index', compact('jobPostings'));
    }

    /**
     * Display a single public job posting
     */
    public function show(JobPosting $jobPosting)
    {
        if (!$jobPosting->isActive()) {
            abort(404);
        }

        $jobPosting->load(['facility.address', 'facility.organization']);

        return view('public.job-postings.show', compact('jobPosting'));
    }

    /**
     * Export job posting as PDF
     */
    public function exportPdf(JobPosting $jobPosting)
    {
        if (!$jobPosting->isActive()) {
            abort(404);
        }

        $jobPosting->load(['facility.address', 'facility.organization']);

        // Get header image from facility
        $headerImage = $jobPosting->facility->getFirstMediaUrl('header_image') ?:
                      $jobPosting->facility->getFirstMediaUrl('header') ?:
                      $jobPosting->facility->getFirstMediaUrl('cover') ?:
                      $jobPosting->facility->getFirstMediaUrl('logo');

        $pdf = Pdf::loadView('public.job-postings.pdf', compact('jobPosting', 'headerImage'));

        $filename = 'stellenangebot-' . $jobPosting->slug . '.pdf';

        return $pdf->download($filename);
    }
}
