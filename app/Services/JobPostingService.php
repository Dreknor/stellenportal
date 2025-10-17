<?php

namespace App\Services;

use App\Models\JobPosting;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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
     * Search job postings within radius
     */
    public function searchByRadius(float $latitude, float $longitude, float $radiusKm = 50)
    {
        // Using Haversine formula
        $earthRadiusKm = 6371;

        return JobPosting::active()
            ->with(['facility.address'])
            ->whereHas('facility.address', function ($query) use ($latitude, $longitude, $radiusKm, $earthRadiusKm) {
                $query->selectRaw(
                    "*, (
                        {$earthRadiusKm} * acos(
                            cos(radians(?))
                            * cos(radians(latitude))
                            * cos(radians(longitude) - radians(?))
                            + sin(radians(?))
                            * sin(radians(latitude))
                        )
                    ) AS distance",
                    [$latitude, $longitude, $latitude]
                )->having('distance', '<=', $radiusKm);
            })
            ->get();
    }
}

