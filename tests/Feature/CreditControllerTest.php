<?php

namespace Tests\Feature;

use App\Models\CreditPackage;
use App\Models\Facility;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CreditControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_facility_credit_purchase_page(): void
    {
        $user = User::factory()->create();
        $facility = Facility::factory()->create();
        $user->facilities()->attach($facility);

        CreditPackage::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)->get(route('credits.facility.purchase', $facility));

        $response->assertStatus(200);
        $response->assertViewHas(['facility', 'packages', 'balance']);
    }

    public function test_user_can_purchase_credits_for_facility(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $facility = Facility::factory()->create();
        $user->facilities()->attach($facility);

        $package = CreditPackage::factory()->create([
            'credits' => 10,
            'price' => 100,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(
            route('credits.facility.purchase.store', $facility),
            ['credit_package_id' => $package->id]
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(10, $facility->getCurrentCreditBalance());
    }

    public function test_user_can_view_organization_credit_purchase_page(): void
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $user->organizations()->attach($organization);

        CreditPackage::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)->get(route('credits.organization.purchase', $organization));

        $response->assertStatus(200);
        $response->assertViewHas(['organization', 'packages', 'balance']);
    }

    public function test_user_cannot_purchase_credits_for_unauthorized_facility(): void
    {
        $user = User::factory()->create();
        $facility = Facility::factory()->create(); // Not attached

        $package = CreditPackage::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)->post(
            route('credits.facility.purchase.store', $facility),
            ['credit_package_id' => $package->id]
        );

        $response->assertStatus(403);
    }

    public function test_cannot_purchase_inactive_credit_package(): void
    {
        $user = User::factory()->create();
        $facility = Facility::factory()->create();
        $user->facilities()->attach($facility);

        $package = CreditPackage::factory()->create(['is_active' => false]);

        $response = $this->actingAs($user)->post(
            route('credits.facility.purchase.store', $facility),
            ['credit_package_id' => $package->id]
        );

        $response->assertSessionHas('error');
    }
}
