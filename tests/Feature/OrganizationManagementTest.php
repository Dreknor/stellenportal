<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class OrganizationManagementTest extends TestCase
{
    use RefreshDatabase;
    protected Role $adminRole;
    protected Permission $permission;

    protected function setUp(): void
    {
        parent::setUp();

        // Create role and permission for all tests
        $this->adminRole = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        // The admin routes expect the permission named 'admin edit organizations'
        $this->permission = Permission::create(['name' => 'admin edit organizations', 'guard_name' => 'web']);
        $this->adminRole->givePermissionTo($this->permission);

        // Ensure Spatie permission cache is cleared so middleware sees the newly created permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_admin_can_approve_organization(): void
    {
        // Create an admin user, mark as email-verified and ensure password expiry check won't redirect
        $admin = User::factory()->create([
            'email_verified_at' => now(),
            'change_password' => false,
        ]);
        $admin->assignRole('Super Admin');
        // Also explicitly give the permission to the user to avoid edge-case caching issues
        $admin->givePermissionTo('admin edit organizations');

        $this->assertTrue($admin->can('admin edit organizations'), 'Admin user cannot ->can("admin edit organizations")');
        $this->assertTrue($admin->hasPermissionTo('admin edit organizations'), 'Admin user does not have hasPermissionTo("admin edit organizations")');

        $organization = Organization::factory()->create(['is_approved' => false]);

        $response = $this->actingAs($admin)->post(
            route('admin.organizations.approve', $organization)
        );

        $organization->refresh();

        $response->assertRedirect();
        $this->assertTrue($organization->is_approved);
        $this->assertEquals($admin->id, $organization->approved_by);
    }

    public function test_organization_can_have_multiple_facilities(): void
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $user->organizations()->attach($organization);

        $facility1 = Facility::factory()->create(['organization_id' => $organization->id]);
        $facility2 = Facility::factory()->create(['organization_id' => $organization->id]);

        $this->assertCount(2, $organization->facilities);
    }

    public function test_organization_users_can_access_facilities(): void
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $facility = Facility::factory()->create(['organization_id' => $organization->id]);

        $user->organizations()->attach($organization);

        $this->assertTrue($organization->facilities->contains($facility));
    }
}
