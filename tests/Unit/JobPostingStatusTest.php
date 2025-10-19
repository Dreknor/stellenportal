<?php

namespace Tests\Unit;

use App\Models\JobPosting;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobPostingStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_posting_is_active_when_published_and_not_expired(): void
    {
        $jobPosting = JobPosting::factory()->create([
            'status' => JobPosting::STATUS_ACTIVE,
            'published_at' => Carbon::now()->subDay(),
            'expires_at' => Carbon::now()->addMonth(),
        ]);

        $this->assertTrue($jobPosting->isActive());
        $this->assertFalse($jobPosting->isExpired());
        $this->assertFalse($jobPosting->isDraft());
    }

    public function test_job_posting_is_not_active_when_not_yet_published(): void
    {
        $jobPosting = JobPosting::factory()->create([
            'status' => JobPosting::STATUS_ACTIVE,
            'published_at' => Carbon::now()->addDay(),
            'expires_at' => Carbon::now()->addMonth(),
        ]);

        $this->assertFalse($jobPosting->isActive());
    }

    public function test_job_posting_is_expired_when_past_expiry_date(): void
    {
        $jobPosting = JobPosting::factory()->create([
            'status' => JobPosting::STATUS_ACTIVE,
            'published_at' => Carbon::now()->subMonths(4),
            'expires_at' => Carbon::now()->subDay(),
        ]);

        $this->assertFalse($jobPosting->isActive());
        $this->assertTrue($jobPosting->isExpired());
    }

    public function test_draft_job_posting_is_not_active(): void
    {
        $jobPosting = JobPosting::factory()->create([
            'status' => JobPosting::STATUS_DRAFT,
        ]);

        $this->assertTrue($jobPosting->isDraft());
        $this->assertFalse($jobPosting->isActive());
    }

    public function test_paused_job_posting_is_not_active(): void
    {
        $jobPosting = JobPosting::factory()->create([
            'status' => JobPosting::STATUS_PAUSED,
            'published_at' => Carbon::now()->subDay(),
            'expires_at' => Carbon::now()->addMonth(),
        ]);

        $this->assertFalse($jobPosting->isActive());
        $this->assertFalse($jobPosting->isDraft());
    }
}

