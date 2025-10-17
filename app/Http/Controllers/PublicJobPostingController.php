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

        // Full-text search across all relevant fields
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('job_category', 'like', "%{$search}%")
                    ->orWhere('requirements', 'like', "%{$search}%")
                    ->orWhere('benefits', 'like', "%{$search}%")
                    ->orWhereHas('facility', function ($fq) use ($search) {
                        $fq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by employment type
        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        // Location-based search with radius
        if ($request->filled('location')) {
            $location = $request->location;
            $radius = $request->get('radius', 50); // Default 50km

            // Geocode the location
            $coordinates = $this->jobPostingService->geocodeLocation($location);

            if ($coordinates) {
                $jobPostings = $this->jobPostingService->searchByRadius(
                    $query,
                    $coordinates['latitude'],
                    $coordinates['longitude'],
                    $radius
                );

                return view('public.job-postings.index', [
                    'jobPostings' => $jobPostings,
                    'searchLocation' => $location,
                    'searchCoordinates' => $coordinates,
                ]);
            }
        }

        $jobPostings = $query->orderBy('published_at', 'desc')->paginate(20);

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
