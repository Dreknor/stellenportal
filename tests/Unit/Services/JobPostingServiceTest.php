<?php

namespace Tests\Unit\Services;

use App\Models\Facility;
use App\Models\JobPosting;
use App\Models\User;
use App\Services\JobPostingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobPostingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected JobPostingService $jobPostingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->jobPostingService = app(JobPostingService::class);
    }

    public function test_publish_job_posting_deducts_credits(): void
    {
        $facility = Facility::factory()->create();
        $facility->creditBalance()->create(['balance' => 10]);

        $user = User::factory()->create();
        $jobPosting = JobPosting::factory()->create([
            'facility_id' => $facility->id,
            'status' => JobPosting::STATUS_DRAFT,
        ]);

        $result = $this->jobPostingService->publishJobPosting($jobPosting, $user);

        $this->assertEquals(JobPosting::STATUS_ACTIVE, $result->status);
        $this->assertNotNull($result->published_at);
        $this->assertNotNull($result->expires_at);
        $this->assertEquals(9, $facility->getCurrentCreditBalance());
    }

    public function test_publish_job_posting_fails_when_insufficient_credits(): void
    {
        $facility = Facility::factory()->create();
        $facility->creditBalance()->create(['balance' => 0]);

        $user = User::factory()->create();
        $jobPosting = JobPosting::factory()->create([
            'facility_id' => $facility->id,
            'status' => JobPosting::STATUS_DRAFT,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Nicht genügend Guthaben vorhanden');

        $this->jobPostingService->publishJobPosting($jobPosting, $user);
    }

    public function test_publish_job_posting_fails_when_not_draft(): void
    {
        $facility = Facility::factory()->create();
        $facility->creditBalance()->create(['balance' => 10]);

        $user = User::factory()->create();
        $jobPosting = JobPosting::factory()->create([
            'facility_id' => $facility->id,
            'status' => JobPosting::STATUS_ACTIVE,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Nur Entwürfe können veröffentlicht werden');

        $this->jobPostingService->publishJobPosting($jobPosting, $user);
    }

    public function test_extend_job_posting(): void
    {
        $facility = Facility::factory()->create();
        $facility->creditBalance()->create(['balance' => 10]);

        $user = User::factory()->create();
        $jobPosting = JobPosting::factory()->create([
            'facility_id' => $facility->id,
            'status' => JobPosting::STATUS_ACTIVE,
            'expires_at' => now()->addMonth(),
        ]);

        $result = $this->jobPostingService->extendJobPosting($jobPosting, $user);

        $this->assertEquals(JobPosting::STATUS_ACTIVE, $result->status);
        $this->assertTrue($result->expires_at->isFuture());
        $this->assertEquals(9, $facility->getCurrentCreditBalance());
    }

    public function test_pause_job_posting(): void
    {
        $jobPosting = JobPosting::factory()->create([
            'status' => JobPosting::STATUS_ACTIVE,
        ]);

        $result = $this->jobPostingService->pauseJobPosting($jobPosting);

        $this->assertEquals(JobPosting::STATUS_PAUSED, $result->status);
    }

    public function test_pause_job_posting_fails_when_not_active(): void
    {
        $jobPosting = JobPosting::factory()->create([
            'status' => JobPosting::STATUS_DRAFT,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Nur aktive Stellenausschreibungen können pausiert werden');

        $this->jobPostingService->pauseJobPosting($jobPosting);
    }

    public function test_resume_job_posting(): void
    {
        $jobPosting = JobPosting::factory()->create([
            'status' => JobPosting::STATUS_PAUSED,
            'expires_at' => now()->addMonth(),
        ]);

        $result = $this->jobPostingService->resumeJobPosting($jobPosting);

        $this->assertEquals(JobPosting::STATUS_ACTIVE, $result->status);
    }

    public function test_resume_job_posting_fails_when_expired(): void
    {
        $jobPosting = JobPosting::factory()->create([
            'status' => JobPosting::STATUS_PAUSED,
            'expires_at' => now()->subDay(),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Die Stellenausschreibung ist abgelaufen');

        $this->jobPostingService->resumeJobPosting($jobPosting);
    }

    public function test_mark_expired_postings(): void
    {
        JobPosting::factory()->create([
            'status' => JobPosting::STATUS_ACTIVE,
            'expires_at' => now()->subDay(),
        ]);

        JobPosting::factory()->create([
            'status' => JobPosting::STATUS_ACTIVE,
            'expires_at' => now()->addMonth(),
        ]);

        $count = $this->jobPostingService->markExpiredPostings();

        $this->assertEquals(1, $count);
        $this->assertEquals(1, JobPosting::where('status', JobPosting::STATUS_EXPIRED)->count());
    }
}

