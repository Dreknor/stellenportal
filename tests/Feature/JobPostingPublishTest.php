<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobPostingPublishTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_publish_job_posting_with_sufficient_credits(): void
    {
        $user = User::factory()->create();
        $facility = Facility::factory()->create();
        $facility->creditBalance()->create(['balance' => 10]);

        $user->facilities()->attach($facility);

        $jobPosting = JobPosting::factory()->create([
            'facility_id' => $facility->id,
            'status' => JobPosting::STATUS_DRAFT,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(
            route('job-postings.publish', $jobPosting)
        );

        $jobPosting->refresh();

        $response->assertRedirect();
        $this->assertEquals(JobPosting::STATUS_ACTIVE, $jobPosting->status);
        $this->assertNotNull($jobPosting->published_at);
        $this->assertEquals(9, $facility->getCurrentCreditBalance());
    }

    public function test_cannot_publish_job_posting_without_credits(): void
    {
        $user = User::factory()->create();
        $facility = Facility::factory()->create();
        $facility->creditBalance()->create(['balance' => 0]);

        $user->facilities()->attach($facility);

        $jobPosting = JobPosting::factory()->create([
            'facility_id' => $facility->id,
            'status' => JobPosting::STATUS_DRAFT,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(
            route('job-postings.publish', $jobPosting)
        );

        $jobPosting->refresh();

        $response->assertSessionHas('error');
        $this->assertEquals(JobPosting::STATUS_DRAFT, $jobPosting->status);
    }

    public function test_can_extend_active_job_posting(): void
    {
        $user = User::factory()->create();
        $facility = Facility::factory()->create();
        $facility->creditBalance()->create(['balance' => 10]);

        $user->facilities()->attach($facility);

        $jobPosting = JobPosting::factory()->create([
            'facility_id' => $facility->id,
            'status' => JobPosting::STATUS_ACTIVE,
            'expires_at' => now()->addMonth(),
            'created_by' => $user->id,
        ]);

        $originalExpiry = $jobPosting->expires_at;

        $response = $this->actingAs($user)->post(
            route('job-postings.extend', $jobPosting)
        );

        $jobPosting->refresh();

        $response->assertRedirect();
        $this->assertTrue($jobPosting->expires_at->isAfter($originalExpiry));
        $this->assertEquals(9, $facility->getCurrentCreditBalance());
    }

    public function test_can_pause_and_resume_job_posting(): void
    {
        $user = User::factory()->create();
        $facility = Facility::factory()->create();
        $user->facilities()->attach($facility);

        $jobPosting = JobPosting::factory()->create([
            'facility_id' => $facility->id,
            'status' => JobPosting::STATUS_ACTIVE,
            'expires_at' => now()->addMonth(),
            'created_by' => $user->id,
        ]);

        // Pause
        $response = $this->actingAs($user)->post(
            route('job-postings.pause', $jobPosting)
        );

        $jobPosting->refresh();
        $this->assertEquals(JobPosting::STATUS_PAUSED, $jobPosting->status);

        // Resume
        $response = $this->actingAs($user)->post(
            route('job-postings.resume', $jobPosting)
        );

        $jobPosting->refresh();
        $this->assertEquals(JobPosting::STATUS_ACTIVE, $jobPosting->status);
    }
}

