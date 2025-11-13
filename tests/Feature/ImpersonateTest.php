<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Reset cached permissions
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    // Create permissions
    Permission::create(['name' => 'admin impersonate users', 'guard_name' => 'web']);

    // Create roles
    $this->adminRole = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
    $this->adminRole->givePermissionTo('admin impersonate users');

    $this->userRole = Role::create(['name' => 'User', 'guard_name' => 'web']);

    // Create users
    $this->admin = User::factory()->create([
        'email' => 'admin@example.com',
    ]);
    $this->admin->assignRole($this->adminRole);

    $this->regularUser = User::factory()->create([
        'email' => 'user@example.com',
    ]);
    $this->regularUser->assignRole($this->userRole);

    $this->anotherUser = User::factory()->create([
        'email' => 'another@example.com',
    ]);
    $this->anotherUser->assignRole($this->userRole);
});

test('admin can impersonate a regular user', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('admin.users.impersonate', $this->regularUser));

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('success');
    expect(session('impersonate_original_user'))->toBe($this->admin->id);
});

test('admin cannot impersonate another admin', function () {
    $anotherAdmin = User::factory()->create();
    $anotherAdmin->assignRole($this->adminRole);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.users.impersonate', $anotherAdmin));

    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('admin cannot impersonate themselves', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('admin.users.impersonate', $this->admin));

    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('regular user cannot impersonate anyone', function () {
    $response = $this->actingAs($this->regularUser)
        ->post(route('admin.users.impersonate', $this->anotherUser));

    $response->assertForbidden();
});

test('admin can stop impersonating', function () {
    // Start impersonating
    session(['impersonate_original_user' => $this->admin->id]);
    $this->actingAs($this->regularUser);

    $response = $this->post(route('admin.impersonate.stop'));

    $response->assertRedirect(route('admin.dashboard'));
    $response->assertSessionHas('success');
    expect(session()->has('impersonate_original_user'))->toBeFalse();
});

test('user can be impersonated returns correct boolean', function () {
    expect($this->regularUser->canBeImpersonated())->toBeTrue();
    expect($this->admin->canBeImpersonated())->toBeFalse();
});

test('impersonate banner is shown when impersonating', function () {
    session(['impersonate_original_user' => $this->admin->id]);

    $response = $this->actingAs($this->regularUser)
        ->get(route('dashboard'));

    $response->assertSee('Impersonierung aktiv');
    $response->assertSee('Impersonierung beenden');
});

test('impersonate button is shown in admin users index', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.users.index'));

    $response->assertStatus(200);
    // The impersonate button should be present for regular users
    $response->assertSee(route('admin.users.impersonate', $this->regularUser));
});

test('impersonate button is shown in admin user show page', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.users.show', $this->regularUser));

    $response->assertStatus(200);
    $response->assertSee('Als Benutzer anmelden');
});

