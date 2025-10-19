<?php

namespace Tests\Unit\Models;

use App\Models\CreditPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreditPackageTest extends TestCase
{
    use RefreshDatabase;

    public function test_credit_package_calculates_price_per_credit(): void
    {
        $package = CreditPackage::factory()->create([
            'credits' => 10,
            'price' => 100,
        ]);

        $this->assertEquals(10, $package->price_per_credit);
    }

    public function test_credit_package_price_per_credit_zero_when_no_credits(): void
    {
        $package = CreditPackage::factory()->create([
            'credits' => 0,
            'price' => 100,
        ]);

        $this->assertEquals(0, $package->price_per_credit);
    }

    public function test_credit_package_active_scope(): void
    {
        CreditPackage::factory()->create(['is_active' => true]);
        CreditPackage::factory()->create(['is_active' => false]);

        $activePackages = CreditPackage::active()->get();

        $this->assertCount(1, $activePackages);
        $this->assertTrue($activePackages->first()->is_active);
    }

    public function test_credit_package_casts_correctly(): void
    {
        $package = CreditPackage::factory()->create([
            'credits' => '50',
            'price' => '199.99',
            'is_active' => '1',
        ]);

        $this->assertIsInt($package->credits);
        $this->assertIsString($package->price);
        $this->assertIsBool($package->is_active);
    }
}

