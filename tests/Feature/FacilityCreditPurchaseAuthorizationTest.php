<?php

namespace Tests\Feature;

use App\Models\CreditPackage;
use App\Models\Facility;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FacilityCreditPurchaseAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_assigned_to_organization_can_purchase_credits_for_facility(): void
    {
        $organization = Organization::factory()->create([
            'is_approved' => true,
        ]);

        $facility = Facility::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // User ist der Organisation zugeordnet, aber NICHT der Facility
        $user->organizations()->attach($organization);

        CreditPackage::factory()->create([
            'is_active' => true,
            'for_cooperative_members' => false,
        ]);

        $response = $this->actingAs($user)
            ->get(route('credits.facility.purchase', $facility));

        $response->assertStatus(200);
    }

    #[Test]
    public function user_assigned_to_facility_can_purchase_credits_for_facility(): void
    {
        $organization = Organization::factory()->create([
            'is_approved' => true,
        ]);

        $facility = Facility::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // User ist nur der Facility zugeordnet
        $user->facilities()->attach($facility);

        CreditPackage::factory()->create([
            'is_active' => true,
            'for_cooperative_members' => false,
        ]);

        $response = $this->actingAs($user)
            ->get(route('credits.facility.purchase', $facility));

        $response->assertStatus(200);
    }

    #[Test]
    public function user_not_assigned_cannot_purchase_credits_for_facility(): void
    {
        $organization = Organization::factory()->create([
            'is_approved' => true,
        ]);

        $facility = Facility::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // User ist weder der Organisation noch der Facility zugeordnet

        $response = $this->actingAs($user)
            ->get(route('credits.facility.purchase', $facility));

        $response->assertStatus(403);
    }

    #[Test]
    public function user_cannot_purchase_credits_if_organization_not_approved(): void
    {
        $organization = Organization::factory()->create([
            'is_approved' => false, // Nicht genehmigt
        ]);

        $facility = Facility::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $user->organizations()->attach($organization);

        $response = $this->actingAs($user)
            ->get(route('credits.facility.purchase', $facility));

        $response->assertStatus(302); // Redirect
        $response->assertSessionHas('error');
    }

    #[Test]
    public function user_assigned_to_organization_can_view_facility_transactions(): void
    {
        $organization = Organization::factory()->create([
            'is_approved' => true,
        ]);

        $facility = Facility::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // User ist der Organisation zugeordnet, aber NICHT der Facility
        $user->organizations()->attach($organization);

        $response = $this->actingAs($user)
            ->get(route('credits.facility.transactions', $facility));

        $response->assertStatus(200);
    }
}

