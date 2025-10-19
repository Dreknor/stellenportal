<?php

namespace Tests\Unit;

use App\Models\CreditBalance;
use App\Models\CreditTransaction;
use App\Models\Facility;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreditSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_credit_balance_starts_at_zero(): void
    {
        $organization = Organization::factory()->create();

        $this->assertEquals(0, $organization->getCurrentCreditBalance());
    }

    public function test_credit_balance_increases_after_purchase(): void
    {
        $organization = Organization::factory()->create();
        $organization->creditBalance()->create(['balance' => 0]);

        $balance = $organization->creditBalance;
        $balance->balance += 10;
        $balance->save();

        $this->assertEquals(10, $organization->getCurrentCreditBalance());
    }

    public function test_credit_balance_decreases_after_usage(): void
    {
        $facility = Facility::factory()->create();
        $facility->creditBalance()->create(['balance' => 10]);

        $balance = $facility->creditBalance;
        $balance->balance -= 1;
        $balance->save();

        $this->assertEquals(9, $facility->getCurrentCreditBalance());
    }

    public function test_credit_transaction_records_balance_after(): void
    {
        $organization = Organization::factory()->create();
        $organization->creditBalance()->create(['balance' => 10]);

        $transaction = CreditTransaction::create([
            'creditable_id' => $organization->id,
            'creditable_type' => Organization::class,
            'user_id' => \App\Models\User::factory()->create()->id,
            'type' => CreditTransaction::TYPE_PURCHASE,
            'amount' => 10,
            'balance_after' => 20,
        ]);

        $this->assertEquals(20, $transaction->balance_after);
    }

    public function test_organization_and_facility_have_separate_balances(): void
    {
        $organization = Organization::factory()->create();
        $facility = Facility::factory()->create(['organization_id' => $organization->id]);

        $organization->creditBalance()->create(['balance' => 50]);
        $facility->creditBalance()->create(['balance' => 10]);

        $this->assertEquals(50, $organization->getCurrentCreditBalance());
        $this->assertEquals(10, $facility->getCurrentCreditBalance());
    }
}
