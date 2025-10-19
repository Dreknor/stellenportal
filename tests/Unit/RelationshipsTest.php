<?php

namespace Tests\Unit;

use App\Models\CreditPackage;
use App\Models\Facility;
use App\Models\JobPosting;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_organization_has_many_facilities(): void
    {
        $organization = Organization::factory()->create();
        $facilities = Facility::factory()->count(3)->create(['organization_id' => $organization->id]);

        $this->assertCount(3, $organization->facilities);
    }

    public function test_facility_has_many_job_postings(): void
    {
        $facility = Facility::factory()->create();
        $jobPostings = JobPosting::factory()->count(5)->create(['facility_id' => $facility->id]);

        $this->assertCount(5, $facility->jobPostings);
    }

    public function test_user_belongs_to_many_organizations(): void
    {
        $user = User::factory()->create();
        $organizations = Organization::factory()->count(2)->create();

        $user->organizations()->attach($organizations);

        $this->assertCount(2, $user->organizations);
    }

    public function test_user_belongs_to_many_facilities(): void
    {
        $user = User::factory()->create();
        $facilities = Facility::factory()->count(3)->create();

        $user->facilities()->attach($facilities);

        $this->assertCount(3, $user->facilities);
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

    public function test_credit_package_has_many_transactions(): void
    {
        $package = CreditPackage::factory()->create();

        // This test verifies the relationship exists
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            $package->transactions()
        );
    }
}

