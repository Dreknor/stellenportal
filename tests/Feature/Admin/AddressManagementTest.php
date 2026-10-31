<?php

use App\Models\Facility;
use App\Models\Organization;
use App\Models\User;
use App\Services\Geocoding\GeocodingFailureReason;
use App\Services\Geocoding\GeocodingResult;
use App\Services\GeocodingService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->withoutVite();

    // Partial-Mock: applyResult() läuft echt, nur der HTTP-nahe Teil wird ersetzt
    $this->geocoding = Mockery::mock(GeocodingService::class)->makePartial();
    $this->geocoding->shouldReceive('geocodeWithResult')->andReturn(GeocodingResult::success('51.1', '13.2'))->byDefault();
    $this->geocoding->shouldReceive('generateMapImage');
    $this->app->instance(GeocodingService::class, $this->geocoding);

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

test('admin can copy organization coordinates to facility', function () {
    $this->organization->address()->create(['street' => 'A', 'number' => '1', 'zip_code' => '01067', 'city' => 'Dresden']);
    $this->organization->address()->update(['latitude' => '50.5', 'longitude' => '12.5']);
    $this->facility->address()->create(['street' => 'B', 'number' => '2', 'zip_code' => '04109', 'city' => 'Leipzig']);
    $this->facility->address()->update(['latitude' => null, 'longitude' => null, 'geocoding_error' => 'Alter Fehler', 'geocoding_failed_at' => now()]);

    $this->actingAs($this->admin)
        ->post(route('admin.facilities.copy-organization-coordinates', $this->facility))
        ->assertSessionHas('success');

    $address = $this->facility->fresh()->address;
    expect((float) $address->latitude)->toBe(50.5)
        ->and((float) $address->longitude)->toBe(12.5)
        ->and($address->geocoding_error)->toBeNull()
        ->and($address->geocoding_failed_at)->toBeNull();
});

test('copying coordinates fails when organization has no coordinates', function () {
    $this->organization->address()->create(['street' => 'A', 'number' => '1', 'zip_code' => '01067', 'city' => 'Dresden']);
    $this->organization->address()->update(['latitude' => null, 'longitude' => null]);
    $this->facility->address()->create(['street' => 'B', 'number' => '2', 'zip_code' => '04109', 'city' => 'Leipzig']);

    $this->actingAs($this->admin)
        ->post(route('admin.facilities.copy-organization-coordinates', $this->facility))
        ->assertSessionHas('error');

    expect((float) $this->facility->fresh()->address->latitude)->toBe(51.1);
});

test('copying coordinates fails when facility has no address', function () {
    $this->organization->address()->create(['street' => 'A', 'number' => '1', 'zip_code' => '01067', 'city' => 'Dresden']);

    $this->actingAs($this->admin)
        ->post(route('admin.facilities.copy-organization-coordinates', $this->facility))
        ->assertSessionHas('error', 'Bitte legen Sie zuerst eine Adresse für die Einrichtung an.');

    expect($this->facility->fresh()->address)->toBeNull();
});

test('regular user cannot copy organization coordinates', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('admin.facilities.copy-organization-coordinates', $this->facility))
        ->assertForbidden();
});

test('facility show page offers copying only when organization has coordinates', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.facilities.show', $this->facility))
        ->assertOk()
        ->assertDontSee('Koordinaten der Organisation übernehmen');

    $this->organization->address()->create(['street' => 'A', 'number' => '1', 'zip_code' => '01067', 'city' => 'Dresden']);

    $this->actingAs($this->admin)
        ->get(route('admin.facilities.show', $this->facility))
        ->assertOk()
        ->assertSee('Koordinaten der Organisation übernehmen');
});

test('edit pages show a map when coordinates exist', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.facilities.edit', $this->facility))
        ->assertOk()
        ->assertDontSee('id="addressMap"', false);

    $this->organization->address()->create(['street' => 'A', 'number' => '1', 'zip_code' => '01067', 'city' => 'Dresden']);
    $this->facility->address()->create(['street' => 'B', 'number' => '2', 'zip_code' => '04109', 'city' => 'Leipzig']);

    $this->actingAs($this->admin)
        ->get(route('admin.organizations.edit', $this->organization))
        ->assertOk()
        ->assertSee('id="addressMap"', false);
    $this->actingAs($this->admin)
        ->get(route('admin.facilities.edit', $this->facility))
        ->assertOk()
        ->assertSee('id="addressMap"', false);
});

test('failed geocoding shows the reason in flash message and on the address card', function () {
    $this->organization->address()->create(['street' => 'A', 'number' => '1', 'zip_code' => '01067', 'city' => 'Dresden']);
    $this->geocoding->shouldReceive('geocodeWithResult')
        ->andReturn(GeocodingResult::failure(GeocodingFailureReason::InvalidApiKey, [
            ['label' => 'Original', 'reason' => GeocodingFailureReason::InvalidApiKey, 'detail' => 'HTTP 401'],
        ]));

    $this->actingAs($this->admin)->post(route('admin.organizations.geocode', $this->organization))
        ->assertSessionHas('error', fn (string $message) => str_contains($message, 'API-Schlüssel ist ungültig'));

    $address = $this->organization->fresh()->address;
    expect($address->geocoding_error)->toContain('API-Schlüssel ist ungültig')
        ->and($address->geocoding_error)->toContain('HTTP 401')
        ->and($address->geocoding_failed_at)->not->toBeNull();

    $this->actingAs($this->admin)->get(route('admin.organizations.show', $this->organization))
        ->assertSee('Letzte Adresssuche fehlgeschlagen')
        ->assertSee('API-Schlüssel ist ungültig');
});

test('automatic geocoding on save persists the failure reason', function () {
    $this->geocoding->shouldReceive('geocodeWithResult')
        ->andReturn(GeocodingResult::failure(GeocodingFailureReason::NoResults, [
            ['label' => 'Original', 'reason' => GeocodingFailureReason::NoResults, 'detail' => null],
        ]));

    $this->actingAs($this->admin)->put(route('admin.organizations.update', $this->organization), [
        'name' => 'Org',
        'street' => 'Gibtsnichtweg',
        'number' => '1',
        'zip_code' => '01067',
        'city' => 'Dresden',
    ])->assertRedirect();

    $address = $this->organization->fresh()->address;
    expect($address->latitude)->toBeNull()
        ->and($address->geocoding_error)->toContain('keine Treffer');
});

test('successful geocoding resets a previous failure reason', function () {
    $address = $this->organization->address()->create(['street' => 'A', 'number' => '1', 'zip_code' => '01067', 'city' => 'Dresden']);
    $address->forceFill(['geocoding_error' => 'Alter Fehler', 'geocoding_failed_at' => now()])->saveQuietly();

    $this->actingAs($this->admin)->post(route('admin.organizations.geocode', $this->organization))
        ->assertSessionHas('success');

    $address->refresh();
    expect($address->geocoding_error)->toBeNull()
        ->and($address->geocoding_failed_at)->toBeNull()
        ->and((float) $address->latitude)->toBe(51.1);
});

test('saving an address without changed address fields keeps existing coordinates', function () {
    $address = $this->facility->address()->create(['street' => 'B', 'number' => '2', 'zip_code' => '04109', 'city' => 'Leipzig']);
    $address->forceFill(['latitude' => '50.5', 'longitude' => '12.5'])->saveQuietly();

    $address->update(['state' => 'Sachsen']);

    $address->refresh();
    expect((float) $address->latitude)->toBe(50.5)
        ->and((float) $address->longitude)->toBe(12.5);
});
