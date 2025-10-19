<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\JobPosting;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobPostingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_create_job_posting_and_is_redirected_to_login(): void
    {
        $response = $this->post(route('job-postings.store'), []);

        $response->assertRedirect(route('login'));
    }

    public function test_validation_errors_when_required_fields_missing(): void
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create(['is_approved' => true]);
        $facility = Facility::factory()->create(['organization_id' => $organization->id]);

        $user->organizations()->attach($organization);
        $user->facilities()->attach($facility);

        $response = $this->actingAs($user)->post(route('job-postings.store'), [
            // Intentionally empty to trigger validation
        ]);

        // facility_id is required by the controller validation; job_category is nullable
        $response->assertSessionHasErrors(['facility_id', 'title', 'description', 'employment_type']);
    }

    public function test_user_can_create_job_posting(): void
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create(['is_approved' => true]);
        $facility = Facility::factory()->create(['organization_id' => $organization->id]);

        $user->organizations()->attach($organization);
        $user->facilities()->attach($facility);

        $postData = [
            'facility_id' => $facility->id,
            'title' => 'Test Job Controller Create',
            'description' => 'Controller test description',
            'employment_type' => JobPosting::EMPLOYMENT_TYPE_FULL_TIME,
            'job_category' => 'IT',
            'contact_email' => 'contact@example.com',
        ];

        $response = $this->actingAs($user)->post(route('job-postings.store'), $postData);

        $response->assertStatus(302);
        $this->assertDatabaseHas('job_postings', [
            'title' => 'Test Job Controller Create',
            'facility_id' => $facility->id,
        ]);

        $job = JobPosting::where('title', 'Test Job Controller Create')->first();
        $this->assertNotNull($job);
        $this->assertEquals(JobPosting::STATUS_DRAFT, $job->status);
    }

    public function test_user_can_update_job_posting(): void
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create(['is_approved' => true]);
        $facility = Facility::factory()->create(['organization_id' => $organization->id]);

        $user->organizations()->attach($organization);
        $user->facilities()->attach($facility);

        $job = JobPosting::factory()->create([
            'facility_id' => $facility->id,
            'created_by' => $user->id,
            'title' => 'Original Title',
            'status' => JobPosting::STATUS_DRAFT,
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'description' => $job->description ?? 'Updated description',
            'employment_type' => $job->employment_type ?? JobPosting::EMPLOYMENT_TYPE_FULL_TIME,
            'job_category' => $job->job_category ?? 'IT',
            'contact_email' => $job->contact_email ?? 'contact@example.com',
        ];

        $response = $this->actingAs($user)->put(route('job-postings.update', $job), $updateData);

        $response->assertStatus(302);

        $job->refresh();
        $this->assertEquals('Updated Title', $job->title);
    }

    public function test_user_can_delete_job_posting(): void
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create(['is_approved' => true]);
        $facility = Facility::factory()->create(['organization_id' => $organization->id]);

        $user->organizations()->attach($organization);
        $user->facilities()->attach($facility);

        $job = JobPosting::factory()->create([
            'facility_id' => $facility->id,
            'created_by' => $user->id,
            'title' => 'To Be Deleted',
        ]);

        $response = $this->actingAs($user)->delete(route('job-postings.destroy', $job));

        $response->assertStatus(302);
        // JobPosting uses SoftDeletes; ensure it was soft-deleted
        $this->assertSoftDeleted('job_postings', ['id' => $job->id]);
    }
}

