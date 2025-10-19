<?php

namespace Tests\Unit\Models;

use App\Models\Address;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    public function test_address_belongs_to_addressable(): void
    {
        $organization = Organization::factory()->create();
        $address = Address::factory()->create([
            'addressable_id' => $organization->id,
            'addressable_type' => Organization::class,
        ]);

        $this->assertTrue($address->addressable->is($organization));
    }

    public function test_address_can_store_coordinates(): void
    {
        $organization = Organization::factory()->create();
        $address = Address::factory()->create([
            'addressable_id' => $organization->id,
            'addressable_type' => Organization::class,
            'latitude' => 52.5200,
            'longitude' => 13.4050,
        ]);

        $this->assertEquals(52.5200, $address->latitude);
        $this->assertEquals(13.4050, $address->longitude);
    }
}

