<?php

use App\Models\Facility;
use App\Models\JobPosting;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();
    Http::fake();
});

function facilityPayload(Facility $facility, array $overrides = []): array
{
    return array_merge([
        'organization_id' => $facility->organization_id,
        'name' => $facility->name,
        'street' => 'Teststraße',
        'number' => '1',
        'city' => 'Radebeul',
        'zip_code' => '01445',
    ], $overrides);
}

// K1: Benutzerverwaltung fremder Organisationen

test('user cannot join a foreign organization via the user management', function () {
    $organization = Organization::factory()->approved()->create();
    $attacker = User::factory()->create();

    $this->actingAs($attacker)
        ->post(route('organizations.users.store', $organization), [
            'first_name' => 'Eve',
            'last_name' => 'Attacker',
            'email' => $attacker->email,
        ])
        ->assertRedirect(route('organizations.index'));

    expect($organization->users()->whereKey($attacker->id)->exists())->toBeFalse();
});

test('user cannot list or remove members of a foreign organization', function () {
    $organization = Organization::factory()->approved()->create();
    $member = User::factory()->create();
    $member->organizations()->attach($organization);
    $attacker = User::factory()->create();

    $this->actingAs($attacker)
        ->get(route('organizations.users.index', $organization))
        ->assertRedirect(route('organizations.index'));

    $this->actingAs($attacker)
        ->delete(route('organizations.users.destroy', [$organization, $member]))
        ->assertRedirect(route('organizations.index'));

    expect($organization->users()->whereKey($member->id)->exists())->toBeTrue();
});

test('member can add users to their own organization but not remove the last member', function () {
    $organization = Organization::factory()->approved()->create();
    $member = User::factory()->create();
    $member->organizations()->attach($organization);

    $this->actingAs($member)
        ->post(route('organizations.users.store', $organization), [
            'first_name' => 'Neu',
            'last_name' => 'Kollegin',
            'email' => 'neu@example.com',
        ])
        ->assertRedirect(route('organizations.users.index', $organization));

    expect($organization->users()->count())->toBe(2);

    $other = $organization->users()->where('email', 'neu@example.com')->first();
    $this->actingAs($member)
        ->delete(route('organizations.users.destroy', [$organization, $other]))
        ->assertSessionHas('success');

    $this->actingAs($member)
        ->delete(route('organizations.users.destroy', [$organization, $member]))
        ->assertSessionHas('error');

    expect($organization->users()->whereKey($member->id)->exists())->toBeTrue();
});

// K2: JSON-LD darf kein </script> enthalten

test('job posting title cannot break out of the json-ld script tag', function () {
    $organization = Organization::factory()->approved()->create();
    $facility = Facility::factory()->create(['organization_id' => $organization->id]);
    $jobPosting = JobPosting::factory()->create([
        'facility_id' => $facility->id,
        'title' => 'Lehrer</script><script>alert(1)</script>',
        'status' => 'active',
        'published_at' => now(),
        'expires_at' => now()->addMonth(),
    ]);

    $html = $this->get(route('public.jobs.show', $jobPosting))->assertOk()->getContent();

    expect($html)->not->toContain('<script>alert(1)</script>');
});

// K3: Uploads

test('organization logo upload rejects non-image files and stores images with a server-side name', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('organizations.store'), [
            'name' => 'Schule',
            'street' => 'Teststraße',
            'number' => '1',
            'city' => 'Radebeul',
            'zip_code' => '01445',
            'logo' => UploadedFile::fake()->create('evil.html', 10, 'text/html'),
        ])
        ->assertSessionHasErrors('logo');

    $this->actingAs($user)
        ->post(route('organizations.store'), [
            'name' => 'Schule',
            'street' => 'Teststraße',
            'number' => '1',
            'city' => 'Radebeul',
            'zip_code' => '01445',
            'logo' => UploadedFile::fake()->image('logo.png'),
        ])
        ->assertSessionHasNoErrors();

    $media = Organization::where('name', 'Schule')->first()->getFirstMedia('logo');
    expect($media)->not->toBeNull();
    expect($media->file_name)->not->toBe('logo.png');
    expect($media->file_name)->toEndWith('.png');
});

// K4: Einrichtung in fremde Organisation verschieben

test('facility cannot be moved into a foreign organization', function () {
    $own = Organization::factory()->approved()->create();
    $foreign = Organization::factory()->approved()->create();
    $facility = Facility::factory()->create(['organization_id' => $own->id]);
    $user = User::factory()->create();
    $user->organizations()->attach($own);

    $this->actingAs($user)
        ->put(route('facilities.update', $facility), facilityPayload($facility, ['organization_id' => $foreign->id]))
        ->assertSessionHas('error');

    expect($facility->fresh()->organization_id)->toBe($own->id);
});

test('facility can be moved between own approved organizations', function () {
    $own = Organization::factory()->approved()->create();
    $second = Organization::factory()->approved()->create();
    $facility = Facility::factory()->create(['organization_id' => $own->id]);
    $user = User::factory()->create();
    $user->organizations()->attach([$own->id, $second->id]);

    $this->actingAs($user)
        ->put(route('facilities.update', $facility), facilityPayload($facility, ['organization_id' => $second->id]));

    expect($facility->fresh()->organization_id)->toBe($second->id);
});

// K7: Rollenvergabe im Admin

function adminWithPermissions(array $permissions): User
{
    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission, 'web');
    }
    $admin = User::factory()->create();
    $admin->givePermissionTo($permissions);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    return $admin;
}

function userPayload(User $user, array $overrides = []): array
{
    return array_merge([
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        'email' => $user->email,
    ], $overrides);
}

test('admin without assign roles permission cannot grant roles', function () {
    $admin = adminWithPermissions(['admin edit users']);
    Permission::findOrCreate('assign roles', 'web');
    Role::findOrCreate('Super Admin', 'web');

    $this->actingAs($admin)
        ->put(route('admin.users.update', $admin), userPayload($admin, ['roles' => ['Super Admin']]))
        ->assertForbidden();

    expect($admin->fresh()->hasRole('Super Admin'))->toBeFalse();
});

test('admin cannot grant a role with permissions they do not have', function () {
    $admin = adminWithPermissions(['admin edit users', 'assign roles']);
    $powerful = Role::findOrCreate('Mächtig', 'web');
    $powerful->givePermissionTo(Permission::findOrCreate('admin delete users', 'web'));
    $target = User::factory()->create();

    $this->actingAs($admin)
        ->put(route('admin.users.update', $target), userPayload($target, ['roles' => ['Mächtig']]))
        ->assertSessionHasErrors('roles');

    expect($target->fresh()->hasRole('Mächtig'))->toBeFalse();
});

test('admin cannot edit a super admin account', function () {
    $admin = adminWithPermissions(['admin edit users']);
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(Role::findOrCreate('Super Admin', 'web'));

    $this->actingAs($admin)
        ->put(route('admin.users.update', $superAdmin), userPayload($superAdmin, ['email' => 'attacker@example.com']))
        ->assertForbidden();

    expect($superAdmin->fresh()->email)->not->toBe('attacker@example.com');
});
