<?php

namespace Tests\Feature;

use App\Models\CreditPackage;
use App\Models\Facility;
use App\Models\JobPosting;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CompleteJobPostingWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_workflow_from_registration_to_published_job(): void
    {
        Mail::fake();

        // 1. Create organization and user
        $user = User::factory()->create();
        $organization = Organization::factory()->create(['is_approved' => true]);
        $user->organizations()->attach($organization);

        // 2. Create facility
        $facility = Facility::factory()->create(['organization_id' => $organization->id]);
        $user->facilities()->attach($facility);

        // 3. Purchase credits for organization
        $package = CreditPackage::factory()->create([
            'credits' => 10,
            'price' => 100,
            'is_active' => true,
        ]);

        $this->actingAs($user)->post(
            route('credits.organization.purchase.store', $organization),
            ['credit_package_id' => $package->id]
        );

        $this->assertEquals(10, $organization->getCurrentCreditBalance());

        // 4. Transfer credits to facility
        $this->actingAs($user)->post(
            route('credits.organization.transfer', $organization),
            [
                'facility_id' => $facility->id,
                'amount' => 5,
                'note' => 'Initial facility credits',
            ]
        );

        $this->assertEquals(5, $organization->getCurrentCreditBalance());
        $this->assertEquals(5, $facility->getCurrentCreditBalance());

        // 5. Create job posting
        $response = $this->actingAs($user)->post(route('job-postings.store'), [
            'facility_id' => $facility->id,
            'title' => 'Software Developer',
            'description' => 'We are looking for a talented developer',
            'employment_type' => JobPosting::EMPLOYMENT_TYPE_FULL_TIME,
            'job_category' => 'IT',
            'contact_email' => 'jobs@example.com',
        ]);

        $jobPosting = JobPosting::where('title', 'Software Developer')->first();
        $this->assertNotNull($jobPosting);
        $this->assertEquals(JobPosting::STATUS_DRAFT, $jobPosting->status);

        // 6. Publish job posting
        $this->actingAs($user)->post(
            route('job-postings.publish', $jobPosting)
        );

        $jobPosting->refresh();
        $this->assertEquals(JobPosting::STATUS_ACTIVE, $jobPosting->status);
        $this->assertEquals(4, $facility->getCurrentCreditBalance());

        // 7. Verify it's visible to public
        $response = $this->get(route('public.jobs.show', $jobPosting));
        $response->assertStatus(200);
        $response->assertSee('Software Developer');

        // 8. Pause job posting
        $this->actingAs($user)->post(
            route('job-postings.pause', $jobPosting)
        );

        $jobPosting->refresh();
        $this->assertEquals(JobPosting::STATUS_PAUSED, $jobPosting->status);

        // 9. Resume job posting
        $this->actingAs($user)->post(
            route('job-postings.resume', $jobPosting)
        );

        $jobPosting->refresh();
        $this->assertEquals(JobPosting::STATUS_ACTIVE, $jobPosting->status);

        // 10. Extend job posting
        $originalExpiry = $jobPosting->expires_at;

        $this->actingAs($user)->post(
            route('job-postings.extend', $jobPosting)
        );

        $jobPosting->refresh();
        $this->assertTrue($jobPosting->expires_at->isAfter($originalExpiry));
        $this->assertEquals(3, $facility->getCurrentCreditBalance());
    }
}
