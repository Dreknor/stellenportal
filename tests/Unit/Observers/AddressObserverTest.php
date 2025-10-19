<?php

namespace Tests\Unit\Observers;

use App\Models\Address;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_address_observer_handles_address_creation(): void
    {
        $organization = Organization::factory()->create();

        $address = new Address([
            'addressable_id' => $organization->id,
            'addressable_type' => Organization::class,
            'street' => 'Teststraße',
            'number' => '123',
            'city' => 'Berlin',
            'zip_code' => '10115',
        ]);

        $address->save();

        $this->assertDatabaseHas('addresses', [
            'street' => 'Teststraße',
            'city' => 'Berlin',
        ]);
    }
}

