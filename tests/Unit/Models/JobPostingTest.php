<?php

namespace Tests\Unit\Models;

use App\Models\JobPosting;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobPostingTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_posting_has_slug(): void
    {
        $jobPosting = JobPosting::factory()->create([
            'title' => 'Software Developer',
        ]);

        $this->assertNotNull($jobPosting->slug);
        $this->assertStringContainsString('software-developer', $jobPosting->slug);
    }

    public function test_job_posting_belongs_to_facility(): void
    {
        $facility = Facility::factory()->create();
        $jobPosting = JobPosting::factory()->create(['facility_id' => $facility->id]);

        $this->assertTrue($jobPosting->facility->is($facility));
    }

    public function test_job_posting_has_creator(): void
    {
        $user = User::factory()->create();
        $jobPosting = JobPosting::factory()->create(['created_by' => $user->id]);

        $this->assertTrue($jobPosting->creator->is($user));
    }

    public function test_job_posting_is_active_when_published_and_not_expired(): void
    {
        $jobPosting = JobPosting::factory()->create([
            'status' => JobPosting::STATUS_ACTIVE,
            'published_at' => now()->subDay(),
            'expires_at' => now()->addMonth(),
        ]);

        $this->assertTrue($jobPosting->isActive());
    }

    public function test_job_posting_is_not_active_when_expired(): void
    {
        $jobPosting = JobPosting::factory()->create([
            'status' => JobPosting::STATUS_ACTIVE,
            'published_at' => now()->subMonth(),
            'expires_at' => now()->subDay(),
        ]);

        $this->assertFalse($jobPosting->isActive());
    }

    public function test_job_posting_is_expired(): void
    {
        $jobPosting = JobPosting::factory()->create([
            'expires_at' => now()->subDay(),
        ]);

        $this->assertTrue($jobPosting->isExpired());
    }

    public function test_job_posting_is_draft(): void
    {
        $jobPosting = JobPosting::factory()->create([
            'status' => JobPosting::STATUS_DRAFT,
        ]);

        $this->assertTrue($jobPosting->isDraft());
    }

    public function test_job_posting_route_key_is_slug(): void
    {
        $jobPosting = JobPosting::factory()->create();

        $this->assertEquals('slug', $jobPosting->getRouteKeyName());
    }
}

