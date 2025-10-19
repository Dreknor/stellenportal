<?php

namespace Tests\Unit\Models;

use App\Models\Organization;
use App\Models\Facility;
use App\Models\User;
use App\Models\CreditBalance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_organization_has_slug(): void
    {
        $organization = Organization::factory()->create([
            'name' => 'Test Organization',
        ]);

        $this->assertNotNull($organization->slug);
        $this->assertEquals('test-organization', $organization->slug);
    }

    public function test_organization_can_have_facilities(): void
    {
        $organization = Organization::factory()->create();
        $facility = Facility::factory()->create(['organization_id' => $organization->id]);

        $this->assertTrue($organization->facilities->contains($facility));
    }

    public function test_organization_can_have_users(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create();

        $organization->users()->attach($user);

        $this->assertTrue($organization->users->contains($user));
    }

    public function test_organization_can_have_credit_balance(): void
    {
        $organization = Organization::factory()->create();

        $balance = $organization->creditBalance()->create(['balance' => 100]);

        $this->assertEquals(100, $organization->getCurrentCreditBalance());
    }

    public function test_organization_creates_credit_balance_if_not_exists(): void
    {
        $organization = Organization::factory()->create();

        $this->assertEquals(0, $organization->getCurrentCreditBalance());
        $this->assertDatabaseHas('credit_balances', [
            'creditable_id' => $organization->id,
            'creditable_type' => Organization::class,
            'balance' => 0,
        ]);
    }

    public function test_organization_approval_status(): void
    {
        $organization = Organization::factory()->create(['is_approved' => false]);

        $this->assertFalse($organization->is_approved);

        $user = User::factory()->create();
        $organization->update([
            'is_approved' => true,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        $this->assertTrue($organization->is_approved);
        $this->assertEquals($user->id, $organization->approved_by);
    }
}

