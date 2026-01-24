<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use App\Models\SearchQuery;
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

        $searchPerformed = false;
        $searchData = [
            'query' => null,
            'location' => null,
            'radius' => null,
            'employment_type' => null,
            'results_count' => 0,
        ];

        // Full-text search across all relevant fields
        if ($request->filled('search')) {
            $search = $request->search;

            // If search term is the Google placeholder, show all active jobs
            if ($search !== '{search_term_string}') {
                $searchPerformed = true;
                $searchData['query'] = $search;

                $query->where(function ($q) use ($search) {
                    $q->where('job_postings.title', 'like', "%{$search}%")
                        ->orWhere('job_postings.description', 'like', "%{$search}%")
                        ->orWhere('job_postings.job_category', 'like', "%{$search}%")
                        ->orWhere('job_postings.requirements', 'like', "%{$search}%")
                        ->orWhere('job_postings.benefits', 'like', "%{$search}%")
                        ->orWhereHas('facility', function ($fq) use ($search) {
                            $fq->where('name', 'like', "%{$search}%");
                        });
                });
            }
        }

        // Filter by employment type
        if ($request->filled('employment_type')) {
            $searchPerformed = true;
            $searchData['employment_type'] = $request->employment_type;
            $query->where('job_postings.employment_type', $request->employment_type);
        }

        // Location-based search with radius
        if ($request->filled('location')) {
            $location = $request->location;
            $radius = $request->get('radius', 50); // Default 50km
            $searchPerformed = true;
            $searchData['location'] = $location;
            $searchData['radius'] = $radius;

            // Geocode the location
            $coordinates = $this->jobPostingService->geocodeLocation($location);

            if ($coordinates) {
                $jobPostings = $this->jobPostingService->searchByRadius(
                    $query,
                    $coordinates['latitude'],
                    $coordinates['longitude'],
                    $radius
                );

                // Track search query
                $searchData['results_count'] = $jobPostings->total();
                $this->trackSearchQuery($request, $searchData);

                return view('public.job-postings.index', [
                    'jobPostings' => $jobPostings,
                    'searchLocation' => $location,
                    'searchCoordinates' => $coordinates,
                ]);
            }
        }

        $jobPostings = $query->orderBy('published_at', 'desc')->paginate(20);

        // Track search query if any search was performed
        if ($searchPerformed) {
            $searchData['results_count'] = $jobPostings->total();
            $this->trackSearchQuery($request, $searchData);
        }

        return view('public.job-postings.index', compact('jobPostings'));
    }

    /**
     * Track search query for analytics
     */
    protected function trackSearchQuery(Request $request, array $searchData)
    {
        try {
            SearchQuery::create([
                'query' => $searchData['query'],
                'location' => $searchData['location'],
                'radius' => $searchData['radius'],
                'employment_type' => $searchData['employment_type'],
                'results_count' => $searchData['results_count'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_id' => auth()->id(),
            ]);
        } catch (\Exception $e) {
            // Stilles Logging - Search Tracking sollte die Suche nicht blockieren
            logger()->error('Failed to track search query', [
                'error' => $e->getMessage(),
                'search_data' => $searchData,
            ]);
        }
    }

    /**
     * Display a single public job posting
     */
    public function show(JobPosting $jobPosting)
    {
        $jobPosting->load(['facility.address', 'facility.organization']);

        // Check if job posting is expired or inactive
        $isExpired = !$jobPosting->isActive();

        return view('public.job-postings.show', compact('jobPosting', 'isExpired'));
    }

    /**
     * Export job posting as PDF
     */
    public function exportPdf(JobPosting $jobPosting)
    {
        $jobPosting->load(['facility.address', 'facility.organization']);

        // Check if job posting is expired or inactive
        $isExpired = !$jobPosting->isActive();

        // Get header image from facility
        $headerImage = $jobPosting->facility->getFirstMediaUrl('header_image') ?:
                      $jobPosting->facility->getFirstMediaUrl('header') ?:
                      $jobPosting->facility->getFirstMediaUrl('cover') ?:
                      $jobPosting->facility->getFirstMediaUrl('logo');

        $pdf = Pdf::loadView('public.job-postings.pdf', compact('jobPosting', 'headerImage', 'isExpired'));

        $filename = 'stellenangebot-' . $jobPosting->slug . '.pdf';

        return $pdf->download($filename);
    }
}
