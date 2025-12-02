<?php

namespace Tests\Unit;

use App\Models\CreditPackage;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CooperativeMembershipUnitTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function organization_is_cooperative_member_attribute_is_boolean(): void
    {
        $organization = Organization::factory()->create([
            'is_cooperative_member' => true,
        ]);

        $this->assertIsBool($organization->is_cooperative_member);
        $this->assertTrue($organization->is_cooperative_member);
    }

    #[Test]
    public function organization_is_cooperative_member_defaults_to_false(): void
    {
        $organization = Organization::factory()->create();

        $this->assertFalse($organization->is_cooperative_member);
    }

    #[Test]
    public function organization_can_be_set_as_cooperative_member(): void
    {
        $organization = Organization::factory()->create([
            'is_cooperative_member' => false,
        ]);

        $organization->update(['is_cooperative_member' => true]);

        $this->assertTrue($organization->fresh()->is_cooperative_member);
    }

    #[Test]
    public function credit_package_for_cooperative_members_attribute_is_boolean(): void
    {
        $package = CreditPackage::factory()->create([
            'for_cooperative_members' => true,
        ]);

        $this->assertIsBool($package->for_cooperative_members);
        $this->assertTrue($package->for_cooperative_members);
    }

    #[Test]
    public function credit_package_for_cooperative_members_defaults_to_false(): void
    {
        $package = CreditPackage::factory()->create();

        $this->assertFalse($package->for_cooperative_members);
    }

    #[Test]
    public function credit_package_can_be_set_for_cooperative_members(): void
    {
        $package = CreditPackage::factory()->create([
            'for_cooperative_members' => false,
        ]);

        $package->update(['for_cooperative_members' => true]);

        $this->assertTrue($package->fresh()->for_cooperative_members);
    }

    #[Test]
    public function credit_package_scope_for_cooperative_members_only_returns_cooperative_packages(): void
    {
        CreditPackage::factory()->count(3)->create(['for_cooperative_members' => true]);
        CreditPackage::factory()->count(2)->create(['for_cooperative_members' => false]);

        $cooperativePackages = CreditPackage::forCooperativeMembers()->get();

        $this->assertCount(3, $cooperativePackages);
        $cooperativePackages->each(function ($package) {
            $this->assertTrue($package->for_cooperative_members);
        });
    }

    #[Test]
    public function credit_package_scope_for_non_cooperative_members_only_returns_standard_packages(): void
    {
        CreditPackage::factory()->count(2)->create(['for_cooperative_members' => true]);
        CreditPackage::factory()->count(4)->create(['for_cooperative_members' => false]);

        $standardPackages = CreditPackage::forNonCooperativeMembers()->get();

        $this->assertCount(4, $standardPackages);
        $standardPackages->each(function ($package) {
            $this->assertFalse($package->for_cooperative_members);
        });
    }

    #[Test]
    public function credit_package_scope_available_for_filters_by_organization_cooperative_status(): void
    {
        $cooperativeOrg = Organization::factory()->create(['is_cooperative_member' => true]);
        $standardOrg = Organization::factory()->create(['is_cooperative_member' => false]);

        $cooperativePackage = CreditPackage::factory()->create(['for_cooperative_members' => true]);
        $standardPackage = CreditPackage::factory()->create(['for_cooperative_members' => false]);

        // Test for cooperative organization
        $packagesForCoopOrg = CreditPackage::availableFor($cooperativeOrg)->get();
        $this->assertCount(1, $packagesForCoopOrg);
        $this->assertEquals($cooperativePackage->id, $packagesForCoopOrg->first()->id);

        // Test for standard organization
        $packagesForStandardOrg = CreditPackage::availableFor($standardOrg)->get();
        $this->assertCount(1, $packagesForStandardOrg);
        $this->assertEquals($standardPackage->id, $packagesForStandardOrg->first()->id);
    }

    #[Test]
    public function credit_package_scope_available_for_returns_empty_when_no_matching_packages(): void
    {
        $cooperativeOrg = Organization::factory()->create(['is_cooperative_member' => true]);

        // Create only standard packages
        CreditPackage::factory()->count(3)->create(['for_cooperative_members' => false]);

        $availablePackages = CreditPackage::availableFor($cooperativeOrg)->get();

        $this->assertCount(0, $availablePackages);
    }

    #[Test]
    public function organization_is_cooperative_member_attribute_is_mass_assignable(): void
    {
        $organization = Organization::factory()->make();

        $this->assertContains('is_cooperative_member', $organization->getFillable());
    }

    #[Test]
    public function credit_package_for_cooperative_members_attribute_is_mass_assignable(): void
    {
        $package = CreditPackage::factory()->make();

        $this->assertContains('for_cooperative_members', $package->getFillable());
    }

    #[Test]
    public function multiple_organizations_can_have_different_cooperative_statuses(): void
    {
        $cooperativeOrg1 = Organization::factory()->create(['is_cooperative_member' => true]);
        $cooperativeOrg2 = Organization::factory()->create(['is_cooperative_member' => true]);
        $standardOrg1 = Organization::factory()->create(['is_cooperative_member' => false]);
        $standardOrg2 = Organization::factory()->create(['is_cooperative_member' => false]);

        $this->assertTrue($cooperativeOrg1->is_cooperative_member);
        $this->assertTrue($cooperativeOrg2->is_cooperative_member);
        $this->assertFalse($standardOrg1->is_cooperative_member);
        $this->assertFalse($standardOrg2->is_cooperative_member);
    }

    #[Test]
    public function multiple_packages_can_have_different_cooperative_targeting(): void
    {
        $cooperativePkg1 = CreditPackage::factory()->create(['for_cooperative_members' => true]);
        $cooperativePkg2 = CreditPackage::factory()->create(['for_cooperative_members' => true]);
        $standardPkg1 = CreditPackage::factory()->create(['for_cooperative_members' => false]);
        $standardPkg2 = CreditPackage::factory()->create(['for_cooperative_members' => false]);

        $this->assertTrue($cooperativePkg1->for_cooperative_members);
        $this->assertTrue($cooperativePkg2->for_cooperative_members);
        $this->assertFalse($standardPkg1->for_cooperative_members);
        $this->assertFalse($standardPkg2->for_cooperative_members);
    }

    #[Test]
    public function changing_organization_cooperative_status_persists_to_database(): void
    {
        $organization = Organization::factory()->create(['is_cooperative_member' => false]);

        $organization->is_cooperative_member = true;
        $organization->save();

        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'is_cooperative_member' => true,
        ]);
    }

    #[Test]
    public function changing_package_cooperative_targeting_persists_to_database(): void
    {
        $package = CreditPackage::factory()->create(['for_cooperative_members' => false]);

        $package->for_cooperative_members = true;
        $package->save();

        $this->assertDatabaseHas('credit_packages', [
            'id' => $package->id,
            'for_cooperative_members' => true,
        ]);
    }
}

