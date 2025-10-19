<?php

namespace Tests\Unit\Policies;

use App\Models\JobPosting;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobPostingPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_job_posting_from_their_facility(): void
    {
        $user = User::factory()->create();
        $facility = Facility::factory()->create();
        $user->facilities()->attach($facility);

        $jobPosting = JobPosting::factory()->create(['facility_id' => $facility->id]);

        $this->assertTrue($user->can('view', $jobPosting));
    }

    public function test_user_cannot_view_job_posting_from_other_facility(): void
    {
        $user = User::factory()->create();
        $otherFacility = Facility::factory()->create();

        $jobPosting = JobPosting::factory()->create(['facility_id' => $otherFacility->id]);

        $this->assertFalse($user->can('view', $jobPosting));
    }

    public function test_user_can_update_their_job_posting(): void
    {
        $user = User::factory()->create();
        $facility = Facility::factory()->create();
        $user->facilities()->attach($facility);

        $jobPosting = JobPosting::factory()->create([
            'facility_id' => $facility->id,
            'created_by' => $user->id,
        ]);

        $this->assertTrue($user->can('update', $jobPosting));
    }

    public function test_user_can_delete_their_job_posting(): void
    {
        $user = User::factory()->create();
        $facility = Facility::factory()->create();
        $user->facilities()->attach($facility);

        $jobPosting = JobPosting::factory()->create([
            'facility_id' => $facility->id,
            'created_by' => $user->id,
        ]);

        $this->assertTrue($user->can('delete', $jobPosting));
    }
}

