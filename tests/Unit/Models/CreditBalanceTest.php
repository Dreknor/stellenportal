<?php

namespace Tests\Unit\Models;

use App\Models\CreditBalance;
use App\Models\CreditTransaction;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreditBalanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_credit_balance_belongs_to_creditable(): void
    {
        $organization = Organization::factory()->create();
        $balance = CreditBalance::factory()->create([
            'creditable_id' => $organization->id,
            'creditable_type' => Organization::class,
            'balance' => 100,
        ]);

        $this->assertTrue($balance->creditable->is($organization));
    }

    public function test_credit_balance_has_transactions(): void
    {
        $organization = Organization::factory()->create();
        $balance = CreditBalance::factory()->create([
            'creditable_id' => $organization->id,
            'creditable_type' => Organization::class,
        ]);

        CreditTransaction::factory()->create([
            'creditable_id' => $organization->id,
            'creditable_type' => Organization::class,
        ]);

        $this->assertCount(1, $balance->transactions);
    }
}

