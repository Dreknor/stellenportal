<?php

namespace Tests\Feature;

use App\Models\CreditPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CreditPackageManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Role $adminRole;
    protected Permission $permission;

    protected function setUp(): void
    {
        parent::setUp();

        // Create role and permission for all tests
        $this->adminRole = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $this->permission = Permission::create(['name' => 'manage credit packages', 'guard_name' => 'web']);
        $this->adminRole->givePermissionTo($this->permission);
    }

    public function test_admin_can_view_credit_packages(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');

        CreditPackage::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('credits.packages.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_credit_package(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');

        $response = $this->actingAs($admin)->post(route('credits.packages.store'), [
            'name' => 'Test Package',
            'description' => 'Test Description',
            'credits' => 10,
            'price' => 99.99,
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('credit_packages', [
            'name' => 'Test Package',
            'credits' => 10,
        ]);
    }

    public function test_admin_can_update_credit_package(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');

        $package = CreditPackage::factory()->create();

        $response = $this->actingAs($admin)->patch(route('credits.packages.update', $package), [
            'name' => 'Updated Package',
            'description' => $package->description,
            'credits' => $package->credits,
            'price' => $package->price,
            'is_active' => $package->is_active,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('credit_packages', [
            'id' => $package->id,
            'name' => 'Updated Package',
        ]);
    }

    public function test_admin_can_delete_credit_package(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');

        $package = CreditPackage::factory()->create();

        $response = $this->actingAs($admin)->delete(route('credits.packages.destroy', $package));

        $response->assertRedirect();
        $this->assertDatabaseMissing('credit_packages', [
            'id' => $package->id,
        ]);
    }

    public function test_regular_user_cannot_manage_credit_packages(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('credits.packages.index'));

        $response->assertStatus(403);
    }
}
