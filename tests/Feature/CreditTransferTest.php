<?php

namespace Tests\Feature;

use App\Models\CreditPackage;
use App\Models\Facility;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreditTransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_organization_can_transfer_credits_to_facility(): void
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $facility = Facility::factory()->create(['organization_id' => $organization->id]);

        $user->organizations()->attach($organization);
        $organization->creditBalance()->create(['balance' => 50]);

        $response = $this->actingAs($user)->post(
            route('credits.organization.transfer', $organization),
            [
                'facility_id' => $facility->id,
                'amount' => 10,
                'note' => 'Initial credits',
            ]
        );

        $response->assertRedirect();

        $this->assertEquals(40, $organization->getCurrentCreditBalance());
        $this->assertEquals(10, $facility->getCurrentCreditBalance());
    }

    public function test_cannot_transfer_more_credits_than_available(): void
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $facility = Facility::factory()->create(['organization_id' => $organization->id]);

        $user->organizations()->attach($organization);
        $organization->creditBalance()->create(['balance' => 5]);

        $response = $this->actingAs($user)->post(
            route('credits.organization.transfer', $organization),
            [
                'facility_id' => $facility->id,
                'amount' => 10,
            ]
        );

        $response->assertSessionHas('error');
        $this->assertEquals(5, $organization->getCurrentCreditBalance());
        $this->assertEquals(0, $facility->getCurrentCreditBalance());
    }

    public function test_cannot_transfer_credits_to_unrelated_facility(): void
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $otherFacility = Facility::factory()->create(); // Different organization

        $user->organizations()->attach($organization);
        $organization->creditBalance()->create(['balance' => 50]);

        $response = $this->actingAs($user)->post(
            route('credits.organization.transfer', $organization),
            [
                'facility_id' => $otherFacility->id,
                'amount' => 10,
            ]
        );

        $response->assertSessionHas('error');
    }
}

