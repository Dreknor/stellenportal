<?php

namespace Tests\Unit\Services;

use App\Models\CreditPackage;
use App\Models\CreditTransaction;
use App\Models\Facility;
use App\Models\Organization;
use App\Models\User;
use App\Services\CreditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CreditServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CreditService $creditService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->creditService = app(CreditService::class);
        Mail::fake();
    }

    public function test_purchase_credits_for_organization(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create();
        $package = CreditPackage::factory()->create([
            'credits' => 10,
            'price' => 100,
        ]);

        $transaction = $this->creditService->purchaseCredits($organization, $package, $user);

        $this->assertEquals(10, $organization->getCurrentCreditBalance());
        $this->assertDatabaseHas('credit_transactions', [
            'creditable_id' => $organization->id,
            'creditable_type' => Organization::class,
            'type' => CreditTransaction::TYPE_PURCHASE,
            'amount' => 10,
        ]);
    }

    public function test_purchase_credits_for_facility(): void
    {
        $facility = Facility::factory()->create();
        $user = User::factory()->create();
        $package = CreditPackage::factory()->create([
            'credits' => 5,
            'price' => 50,
        ]);

        $transaction = $this->creditService->purchaseCredits($facility, $package, $user);

        $this->assertEquals(5, $facility->getCurrentCreditBalance());
        $this->assertDatabaseHas('credit_transactions', [
            'creditable_id' => $facility->id,
            'creditable_type' => Facility::class,
            'type' => CreditTransaction::TYPE_PURCHASE,
            'amount' => 5,
        ]);
    }

    public function test_transfer_credits_from_organization_to_facility(): void
    {
        $organization = Organization::factory()->create();
        $facility = Facility::factory()->create(['organization_id' => $organization->id]);
        $user = User::factory()->create();

        // Give organization credits
        $organization->creditBalance()->create(['balance' => 20]);

        $result = $this->creditService->transferCredits($organization, $facility, 10, $user);

        $this->assertEquals(10, $organization->getCurrentCreditBalance());
        $this->assertEquals(10, $facility->getCurrentCreditBalance());

        $this->assertDatabaseHas('credit_transactions', [
            'creditable_id' => $organization->id,
            'type' => CreditTransaction::TYPE_TRANSFER_OUT,
            'amount' => -10,
        ]);

        $this->assertDatabaseHas('credit_transactions', [
            'creditable_id' => $facility->id,
            'type' => CreditTransaction::TYPE_TRANSFER_IN,
            'amount' => 10,
        ]);
    }

    public function test_transfer_credits_fails_for_unrelated_facility(): void
    {
        $organization = Organization::factory()->create();
        $facility = Facility::factory()->create(); // Different organization
        $user = User::factory()->create();

        $organization->creditBalance()->create(['balance' => 20]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Facility does not belong to this organization');

        $this->creditService->transferCredits($organization, $facility, 10, $user);
    }

    public function test_transfer_credits_fails_when_insufficient_balance(): void
    {
        $organization = Organization::factory()->create();
        $facility = Facility::factory()->create(['organization_id' => $organization->id]);
        $user = User::factory()->create();

        $organization->creditBalance()->create(['balance' => 5]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient credits');

        $this->creditService->transferCredits($organization, $facility, 10, $user);
    }

    public function test_use_credits(): void
    {
        $facility = Facility::factory()->create();
        $user = User::factory()->create();

        $facility->creditBalance()->create(['balance' => 10]);

        $transaction = $this->creditService->useCredits($facility, 3, $user, 'Test usage');

        $this->assertEquals(7, $facility->getCurrentCreditBalance());
        $this->assertDatabaseHas('credit_transactions', [
            'creditable_id' => $facility->id,
            'type' => CreditTransaction::TYPE_USAGE,
            'amount' => -3,
            'balance_after' => 7,
        ]);
    }

    public function test_use_credits_fails_when_insufficient_balance(): void
    {
        $facility = Facility::factory()->create();
        $user = User::factory()->create();

        $facility->creditBalance()->create(['balance' => 2]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient credits');

        $this->creditService->useCredits($facility, 5, $user);
    }

    public function test_adjust_credits(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create();

        $organization->creditBalance()->create(['balance' => 10]);

        $transaction = $this->creditService->adjustCredits($organization, 5, $user, 'Admin adjustment');

        $this->assertEquals(15, $organization->getCurrentCreditBalance());
        $this->assertDatabaseHas('credit_transactions', [
            'type' => CreditTransaction::TYPE_ADJUSTMENT,
            'amount' => 5,
        ]);
    }
}

