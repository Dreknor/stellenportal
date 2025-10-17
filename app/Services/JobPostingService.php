<?php

namespace App\Services;

use App\Models\JobPosting;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class JobPostingService
{
    public function __construct(
        protected CreditService $creditService
    ) {}

    /**
     * Publish a job posting by deducting credits
     */
    public function publishJobPosting(JobPosting $jobPosting, User $user): JobPosting
    {
        if ($jobPosting->status !== JobPosting::STATUS_DRAFT) {
            throw new \Exception('Nur Entwürfe können veröffentlicht werden.');
        }

        $facility = $jobPosting->facility;
        $creditsRequired = JobPosting::CREDITS_PER_POSTING;

        if ($facility->getCurrentCreditBalance() < $creditsRequired) {
            throw new \Exception('Nicht genügend Guthaben vorhanden.');
        }

        return DB::transaction(function () use ($jobPosting, $facility, $user, $creditsRequired) {
            // Deduct credits
            $this->creditService->useCredits(
                $facility,
                $creditsRequired,
                $user,
                "Stellenausschreibung veröffentlicht: {$jobPosting->title}"
            );

            // Update job posting
            $jobPosting->status = JobPosting::STATUS_ACTIVE;
            $jobPosting->published_at = now();
            $jobPosting->expires_at = now()->addMonths(JobPosting::POSTING_DURATION_MONTHS);
            $jobPosting->credits_used += $creditsRequired;
            $jobPosting->save();

            return $jobPosting;
        });
    }

    /**
     * Extend a job posting by deducting additional credits
     */
    public function extendJobPosting(JobPosting $jobPosting, User $user, int $months = 3): JobPosting
    {
        if (!in_array($jobPosting->status, [JobPosting::STATUS_ACTIVE, JobPosting::STATUS_EXPIRED])) {
            throw new \Exception('Nur aktive oder abgelaufene Stellenausschreibungen können verlängert werden.');
        }

        $facility = $jobPosting->facility;
        $creditsRequired = JobPosting::CREDITS_PER_POSTING;

        if ($facility->getCurrentCreditBalance() < $creditsRequired) {
            throw new \Exception('Nicht genügend Guthaben vorhanden.');
        }

        return DB::transaction(function () use ($jobPosting, $facility, $user, $creditsRequired, $months) {
            // Deduct credits
            $this->creditService->useCredits(
                $facility,
                $creditsRequired,
                $user,
                "Stellenausschreibung verlängert: {$jobPosting->title}"
            );

            // Extend expiration date
            $newExpiresAt = $jobPosting->expires_at && $jobPosting->expires_at->isFuture()
                ? $jobPosting->expires_at->addMonths($months)
                : now()->addMonths($months);

            $jobPosting->status = JobPosting::STATUS_ACTIVE;
            $jobPosting->expires_at = $newExpiresAt;
            $jobPosting->credits_used += $creditsRequired;
            $jobPosting->save();

            return $jobPosting;
        });
    }

    /**
     * Pause a job posting
     */
    public function pauseJobPosting(JobPosting $jobPosting): JobPosting
    {
        if ($jobPosting->status !== JobPosting::STATUS_ACTIVE) {
            throw new \Exception('Nur aktive Stellenausschreibungen können pausiert werden.');
        }

        $jobPosting->status = JobPosting::STATUS_PAUSED;
        $jobPosting->save();

        return $jobPosting;
    }

    /**
     * Resume a paused job posting
     */
    public function resumeJobPosting(JobPosting $jobPosting): JobPosting
    {
        if ($jobPosting->status !== JobPosting::STATUS_PAUSED) {
            throw new \Exception('Nur pausierte Stellenausschreibungen können fortgesetzt werden.');
        }

        // Check if still valid
        if ($jobPosting->expires_at && $jobPosting->expires_at->isPast()) {
            throw new \Exception('Die Stellenausschreibung ist abgelaufen und muss verlängert werden.');
        }

        $jobPosting->status = JobPosting::STATUS_ACTIVE;
        $jobPosting->save();

        return $jobPosting;
    }

    /**
     * Mark expired job postings
     */
    public function markExpiredPostings(): int
    {
        return JobPosting::where('status', JobPosting::STATUS_ACTIVE)
            ->where('expires_at', '<=', now())
            ->update(['status' => JobPosting::STATUS_EXPIRED]);
    }

    /**
     * Geocode a location string to latitude and longitude
     */
    public function geocodeLocation(string $location): ?array
    {
        try {
            $url = config('geocode.geocode_url');
            $key = config('geocode.geocode_key');

            if (!$url || !$key) {
                return null;
            }

            $client = new \GuzzleHttp\Client();
            $requestUrl = $url . urlencode($location) . '&api_key=' . $key;

            $response = $client->get($requestUrl);
            $data = json_decode($response->getBody(), true);

            if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                return [
                    'latitude' => (float) $data[0]['lat'],
                    'longitude' => (float) $data[0]['lon'],
                    'display_name' => $data[0]['display_name'] ?? $location,
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Geocoding failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Search job postings within radius
     */
    public function searchByRadius($query, float $latitude, float $longitude, float $radiusKm = 50)
    {
        // Using Haversine formula
        $earthRadiusKm = 6371;

        // Build the query with JOIN to properly calculate distance
        $jobPostings = $query
            ->join('facilities', 'job_postings.facility_id', '=', 'facilities.id')
            ->join('addresses', function ($join) {
                $join->on('facilities.id', '=', 'addresses.addressable_id')
                    ->where('addresses.addressable_type', '=', 'App\Models\Facility');
            })
            ->whereNotNull('addresses.latitude')
            ->whereNotNull('addresses.longitude')
            ->selectRaw(
                "job_postings.*,
                ( {$earthRadiusKm} * acos(
                    cos(radians(?))
                    * cos(radians(addresses.latitude))
                    * cos(radians(addresses.longitude) - radians(?))
                    + sin(radians(?))
                    * sin(radians(addresses.latitude))
                ) ) AS distance",
                [$latitude, $longitude, $latitude]
            )
            ->having('distance', '<=', $radiusKm)
            ->with(['facility.address', 'facility.organization'])
            ->orderBy('distance', 'asc')
            ->orderBy('published_at', 'desc')
            ->paginate(20)
            ->appends(request()->query());

        return $jobPostings;
    }
}

