<?php

namespace Tests\Unit\Models;

use App\Models\Facility;
use App\Models\Organization;
use App\Models\User;
use App\Models\JobPosting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_facility_has_slug(): void
    {
        $facility = Facility::factory()->create([
            'name' => 'Test Facility',
        ]);

        $this->assertNotNull($facility->slug);
        $this->assertEquals('test-facility', $facility->slug);
    }

    public function test_facility_belongs_to_organization(): void
    {
        $organization = Organization::factory()->create();
        $facility = Facility::factory()->create(['organization_id' => $organization->id]);

        $this->assertTrue($facility->organization->is($organization));
    }

    public function test_facility_can_have_users(): void
    {
        $facility = Facility::factory()->create();
        $user = User::factory()->create();

        $facility->users()->attach($user);

        $this->assertTrue($facility->users->contains($user));
    }

    public function test_facility_can_have_job_postings(): void
    {
        $facility = Facility::factory()->create();
        $jobPosting = JobPosting::factory()->create(['facility_id' => $facility->id]);

        $this->assertTrue($facility->jobPostings->contains($jobPosting));
    }

    public function test_facility_can_have_credit_balance(): void
    {
        $facility = Facility::factory()->create();

        $balance = $facility->creditBalance()->create(['balance' => 50]);

        $this->assertEquals(50, $facility->getCurrentCreditBalance());
    }

    public function test_facility_route_key_is_slug(): void
    {
        $facility = Facility::factory()->create();

        $this->assertEquals('slug', $facility->getRouteKeyName());
    }
}

