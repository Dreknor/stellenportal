<?php

namespace Tests\Feature;

use App\Models\CreditPackage;
use App\Models\Facility;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CooperativeMembershipTest extends TestCase
{
    use RefreshDatabase;

    protected Role $adminRole;
    protected Permission $adminEditOrganizationsPermission;
    protected User $admin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin role and permissions
        $this->adminRole = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $this->adminEditOrganizationsPermission = Permission::create([
            'name' => 'admin edit organizations',
            'guard_name' => 'web'
        ]);
        $this->adminRole->givePermissionTo($this->adminEditOrganizationsPermission);

        // Clear permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Create admin user
        $this->admin = User::factory()->create([
            'email_verified_at' => now(),
            'change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        // Create regular user
        $this->regularUser = User::factory()->create([
            'email_verified_at' => now(),
            'change_password' => false,
        ]);
    }

    /** @test */
    public function organization_has_is_cooperative_member_attribute_set_to_false_by_default(): void
    {
        $organization = Organization::factory()->create();

        $this->assertFalse($organization->is_cooperative_member);
        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'is_cooperative_member' => false,
        ]);
    }

    /** @test */
    public function admin_can_set_organization_as_cooperative_member(): void
    {
        $organization = Organization::factory()->create([
            'is_cooperative_member' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.organizations.update', $organization), [
                'name' => $organization->name,
                'email' => $organization->email,
                'phone' => $organization->phone,
                'website' => $organization->website,
                'description' => $organization->description,
                'is_cooperative_member' => true,
            ]);

        $response->assertRedirect(route('admin.organizations.show', $organization));
        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'is_cooperative_member' => true,
        ]);

        $organization->refresh();
        $this->assertTrue($organization->is_cooperative_member);
    }

    /** @test */
    public function admin_can_remove_cooperative_membership_from_organization(): void
    {
        $organization = Organization::factory()->create([
            'is_cooperative_member' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.organizations.update', $organization), [
                'name' => $organization->name,
                'email' => $organization->email,
                'phone' => $organization->phone,
                'website' => $organization->website,
                'description' => $organization->description,
                'is_cooperative_member' => false,
            ]);

        $response->assertRedirect(route('admin.organizations.show', $organization));
        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'is_cooperative_member' => false,
        ]);

        $organization->refresh();
        $this->assertFalse($organization->is_cooperative_member);
    }

    /** @test */
    public function credit_package_has_for_cooperative_members_attribute_set_to_false_by_default(): void
    {
        $package = CreditPackage::factory()->create();

        $this->assertFalse($package->for_cooperative_members);
        $this->assertDatabaseHas('credit_packages', [
            'id' => $package->id,
            'for_cooperative_members' => false,
        ]);
    }

    /** @test */
    public function admin_can_create_package_for_cooperative_members(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('credits.packages.store'), [
                'name' => 'Cooperative Special Package',
                'description' => 'Special package for cooperative members',
                'credits' => 100,
                'price' => 79.99,
                'is_active' => true,
                'for_cooperative_members' => true,
            ]);

        $response->assertRedirect(route('credits.packages.index'));
        $this->assertDatabaseHas('credit_packages', [
            'name' => 'Cooperative Special Package',
            'for_cooperative_members' => true,
        ]);
    }

    /** @test */
    public function admin_can_create_package_for_non_cooperative_members(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('credits.packages.store'), [
                'name' => 'Standard Package',
                'description' => 'Standard package',
                'credits' => 100,
                'price' => 99.99,
                'is_active' => true,
                'for_cooperative_members' => false,
            ]);

        $response->assertRedirect(route('credits.packages.index'));
        $this->assertDatabaseHas('credit_packages', [
            'name' => 'Standard Package',
            'for_cooperative_members' => false,
        ]);
    }

    /** @test */
    public function cooperative_member_organization_only_sees_cooperative_packages(): void
    {
        $organization = Organization::factory()->create([
            'is_cooperative_member' => true,
            'is_approved' => true,
        ]);
        $this->regularUser->organizations()->attach($organization);

        // Create packages
        $cooperativePackage = CreditPackage::factory()->create([
            'name' => 'Cooperative Package',
            'for_cooperative_members' => true,
            'is_active' => true,
        ]);
        $standardPackage = CreditPackage::factory()->create([
            'name' => 'Standard Package',
            'for_cooperative_members' => false,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->regularUser)
            ->get(route('credits.organization.purchase', $organization));

        $response->assertStatus(200);
        $response->assertSee($cooperativePackage->name);
        $response->assertDontSee($standardPackage->name);
    }

    /** @test */
    public function non_cooperative_member_organization_only_sees_standard_packages(): void
    {
        $organization = Organization::factory()->create([
            'is_cooperative_member' => false,
            'is_approved' => true,
        ]);
        $this->regularUser->organizations()->attach($organization);

        // Create packages
        $cooperativePackage = CreditPackage::factory()->create([
            'name' => 'Cooperative Package',
            'for_cooperative_members' => true,
            'is_active' => true,
        ]);
        $standardPackage = CreditPackage::factory()->create([
            'name' => 'Standard Package',
            'for_cooperative_members' => false,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->regularUser)
            ->get(route('credits.organization.purchase', $organization));

        $response->assertStatus(200);
        $response->assertDontSee($cooperativePackage->name);
        $response->assertSee($standardPackage->name);
    }

    /** @test */
    public function facility_inherits_cooperative_status_from_organization(): void
    {
        $cooperativeOrg = Organization::factory()->create([
            'is_cooperative_member' => true,
            'is_approved' => true,
        ]);
        $facility = Facility::factory()->create([
            'organization_id' => $cooperativeOrg->id,
        ]);
        $this->regularUser->facilities()->attach($facility);

        // Create packages
        $cooperativePackage = CreditPackage::factory()->create([
            'name' => 'Cooperative Package',
            'for_cooperative_members' => true,
            'is_active' => true,
        ]);
        $standardPackage = CreditPackage::factory()->create([
            'name' => 'Standard Package',
            'for_cooperative_members' => false,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->regularUser)
            ->get(route('credits.facility.purchase', $facility));

        $response->assertStatus(200);
        $response->assertSee($cooperativePackage->name);
        $response->assertDontSee($standardPackage->name);
    }

    /** @test */
    public function scope_for_cooperative_members_filters_correctly(): void
    {
        CreditPackage::factory()->create(['for_cooperative_members' => true]);
        CreditPackage::factory()->create(['for_cooperative_members' => true]);
        CreditPackage::factory()->create(['for_cooperative_members' => false]);

        $cooperativePackages = CreditPackage::forCooperativeMembers()->get();

        $this->assertCount(2, $cooperativePackages);
        $this->assertTrue($cooperativePackages->every(fn($p) => $p->for_cooperative_members === true));
    }

    /** @test */
    public function scope_for_non_cooperative_members_filters_correctly(): void
    {
        CreditPackage::factory()->create(['for_cooperative_members' => true]);
        CreditPackage::factory()->create(['for_cooperative_members' => false]);
        CreditPackage::factory()->create(['for_cooperative_members' => false]);

        $standardPackages = CreditPackage::forNonCooperativeMembers()->get();

        $this->assertCount(2, $standardPackages);
        $this->assertTrue($standardPackages->every(fn($p) => $p->for_cooperative_members === false));
    }

    /** @test */
    public function scope_available_for_returns_cooperative_packages_for_cooperative_org(): void
    {
        $cooperativeOrg = Organization::factory()->create(['is_cooperative_member' => true]);

        $cooperativePackage = CreditPackage::factory()->create([
            'for_cooperative_members' => true,
            'is_active' => true,
        ]);
        $standardPackage = CreditPackage::factory()->create([
            'for_cooperative_members' => false,
            'is_active' => true,
        ]);

        $availablePackages = CreditPackage::active()->availableFor($cooperativeOrg)->get();

        $this->assertCount(1, $availablePackages);
        $this->assertEquals($cooperativePackage->id, $availablePackages->first()->id);
    }

    /** @test */
    public function scope_available_for_returns_standard_packages_for_non_cooperative_org(): void
    {
        $standardOrg = Organization::factory()->create(['is_cooperative_member' => false]);

        $cooperativePackage = CreditPackage::factory()->create([
            'for_cooperative_members' => true,
            'is_active' => true,
        ]);
        $standardPackage = CreditPackage::factory()->create([
            'for_cooperative_members' => false,
            'is_active' => true,
        ]);

        $availablePackages = CreditPackage::active()->availableFor($standardOrg)->get();

        $this->assertCount(1, $availablePackages);
        $this->assertEquals($standardPackage->id, $availablePackages->first()->id);
    }

    /** @test */
    public function admin_organization_show_displays_cooperative_membership_status(): void
    {
        $cooperativeOrg = Organization::factory()->create([
            'is_cooperative_member' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.organizations.show', $cooperativeOrg));

        $response->assertStatus(200);
        $response->assertSee('Genossenschaftsmitglied');
    }

    /** @test */
    public function admin_organization_show_displays_non_member_status(): void
    {
        $standardOrg = Organization::factory()->create([
            'is_cooperative_member' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.organizations.show', $standardOrg));

        $response->assertStatus(200);
        $response->assertSee('Kein Mitglied');
    }

    /** @test */
    public function admin_can_see_cooperative_member_checkbox_in_edit_form(): void
    {
        $organization = Organization::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.organizations.edit', $organization));

        $response->assertStatus(200);
        $response->assertSee('Genossenschaftsmitglied');
        $response->assertSee('is_cooperative_member');
    }

    /** @test */
    public function credit_package_index_shows_cooperative_badge_for_cooperative_packages(): void
    {
        $cooperativePackage = CreditPackage::factory()->create([
            'name' => 'Cooperative Package',
            'for_cooperative_members' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('credits.packages.index'));

        $response->assertStatus(200);
        $response->assertSee('Genossenschaft');
    }

    /** @test */
    public function inactive_cooperative_packages_are_not_shown_to_cooperative_members(): void
    {
        $organization = Organization::factory()->create([
            'is_cooperative_member' => true,
            'is_approved' => true,
        ]);
        $this->regularUser->organizations()->attach($organization);

        $activePackage = CreditPackage::factory()->create([
            'name' => 'Active Cooperative Package',
            'for_cooperative_members' => true,
            'is_active' => true,
        ]);
        $inactivePackage = CreditPackage::factory()->create([
            'name' => 'Inactive Cooperative Package',
            'for_cooperative_members' => true,
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->regularUser)
            ->get(route('credits.organization.purchase', $organization));

        $response->assertStatus(200);
        $response->assertSee($activePackage->name);
        $response->assertDontSee($inactivePackage->name);
    }

    /** @test */
    public function organization_can_be_created_without_cooperative_membership(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->post(route('organizations.store'), [
                'name' => 'Test Organization',
                'email' => 'test@example.com',
                'phone' => '123456789',
                'website' => 'https://example.com',
                'description' => 'Test description',
                'street' => 'Test Street',
                'number' => '1',
                'city' => 'Test City',
                'zip_code' => '12345',
            ]);

        $response->assertRedirect(route('organizations.index'));

        $this->assertDatabaseHas('organizations', [
            'name' => 'Test Organization',
            'is_cooperative_member' => false,
        ]);
    }
}

