<?php

use App\Models\Facility;
use App\Models\Organization;
use App\Models\User;
use App\Services\GeocodingService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $geocoding = Mockery::mock(GeocodingService::class);
    $geocoding->shouldReceive('geocode')->andReturn(['lat' => '51.1', 'lon' => '13.2']);
    $geocoding->shouldReceive('generateMapImage');
    $this->app->instance(GeocodingService::class, $geocoding);

    foreach (['admin edit organizations', 'admin edit facilities', 'admin view organizations', 'admin view facilities', 'admin delete organizations', 'admin delete facilities'] as $p) {
        Permission::firstOrCreate(['name' => $p]);
    }
    $role = Role::firstOrCreate(['name' => 'admin']);
    $role->givePermissionTo(Permission::all());

    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');
    $this->organization = Organization::factory()->create();
    $this->facility = Facility::factory()->create(['organization_id' => $this->organization->id]);
});

test('admin can update organization address and coordinates are resolved', function () {
    $this->actingAs($this->admin)->put(route('admin.organizations.update', $this->organization), [
        'name' => 'Org',
        'street' => 'Hauptstraße',
        'number' => '1',
        'zip_code' => '01067',
        'city' => 'Dresden',
    ])->assertRedirect();

    $address = $this->organization->fresh()->address;
    expect($address->city)->toBe('Dresden')
        ->and((float) $address->latitude)->toBe(51.1);
});

test('admin can update facility address', function () {
    $this->actingAs($this->admin)->put(route('admin.facilities.update', $this->facility), [
        'name' => 'Fac',
        'organization_id' => $this->organization->id,
        'street' => 'Nebenweg',
        'number' => '2',
        'zip_code' => '04109',
        'city' => 'Leipzig',
    ])->assertRedirect();

    expect($this->facility->fresh()->address->city)->toBe('Leipzig');
});

test('admin can trigger geocoding for organization and facility', function () {
    foreach ([$this->organization, $this->facility] as $model) {
        $model->address()->create(['street' => 'A', 'number' => '1', 'zip_code' => '01067', 'city' => 'Dresden']);
        $model->address()->update(['latitude' => null, 'longitude' => null]);
    }

    $this->actingAs($this->admin)->post(route('admin.organizations.geocode', $this->organization))
        ->assertSessionHas('success');
    $this->actingAs($this->admin)->post(route('admin.facilities.geocode', $this->facility))
        ->assertSessionHas('success');

    expect((float) $this->organization->fresh()->address->latitude)->toBe(51.1)
        ->and((float) $this->facility->fresh()->address->longitude)->toBe(13.2);
});

test('regular user cannot geocode', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('admin.organizations.geocode', $this->organization))
        ->assertForbidden();
});
