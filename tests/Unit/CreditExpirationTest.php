<?php

use App\Models\CreditTransaction;
use App\Models\CreditPackage;
use App\Models\Facility;
use App\Models\Organization;
use App\Models\User;
use App\Services\CreditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();
});

test('purchased credits have expiration date set to 3 years', function () {
    $organization = Organization::factory()->create();
    $package = CreditPackage::factory()->create(['credits' => 100, 'price' => 50]);
    $user = User::factory()->create();

    $service = app(CreditService::class);
    $transaction = $service->purchaseCredits($organization, $package, $user);

    expect($transaction->expires_at)->not->toBeNull();
    expect((int) $transaction->created_at->diffInYears($transaction->expires_at))->toBe(CreditTransaction::EXPIRATION_YEARS);
});

test('expired credits are correctly identified', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create();

    // Erstelle einen abgelaufenen Credit-Kauf
    $expiredTransaction = CreditTransaction::factory()->create([
        'creditable_id' => $organization->id,
        'creditable_type' => Organization::class,
        'user_id' => $user->id,
        'type' => CreditTransaction::TYPE_PURCHASE,
        'amount' => 100,
        'expires_at' => now()->subDay(),
    ]);

    expect($expiredTransaction->isExpired())->toBeTrue();
});

test('non-expired credits are not identified as expired', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create();

    // Erstelle einen noch nicht abgelaufenen Credit-Kauf
    $validTransaction = CreditTransaction::factory()->create([
        'creditable_id' => $organization->id,
        'creditable_type' => Organization::class,
        'user_id' => $user->id,
        'type' => CreditTransaction::TYPE_PURCHASE,
        'amount' => 100,
        'expires_at' => now()->addYear(),
    ]);

    expect($validTransaction->isExpired())->toBeFalse();
});

test('process expired credits reduces balance', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create();
    $package = CreditPackage::factory()->create(['credits' => 50, 'price' => 50]);

    $service = app(CreditService::class);
    $service->addCredits($organization, 50, $user);
    $service->purchaseCredits($organization, $package, $user);

    $this->travel(CreditTransaction::EXPIRATION_YEARS)->years();
    $this->travel(1)->day();

    expect($service->processExpiredCredits())->toBe(50);
    expect($organization->getCurrentCreditBalance())->toBe(50);
});

test('expiration transaction is created for expired credits', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create();
    $package = CreditPackage::factory()->create(['credits' => 50, 'price' => 50]);

    $service = app(CreditService::class);
    $purchase = $service->purchaseCredits($organization, $package, $user);

    $this->travel(CreditTransaction::EXPIRATION_YEARS)->years();
    $this->travel(1)->day();
    $service->processExpiredCredits();

    $expirationTransaction = CreditTransaction::where('type', CreditTransaction::TYPE_EXPIRATION)
        ->where('related_transaction_id', $purchase->id)
        ->first();

    expect($expirationTransaction)->not->toBeNull();
    expect($expirationTransaction->amount)->toBe(-50);
});

test('fully used purchase does not expire credits bought later', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create();
    $package = CreditPackage::factory()->create(['credits' => 10, 'price' => 50]);

    $service = app(CreditService::class);
    $service->purchaseCredits($organization, $package, $user);
    $service->useCredits($organization, 10, $user);

    $this->travel(CreditTransaction::EXPIRATION_YEARS)->years();
    $this->travel(1)->day();
    $service->purchaseCredits($organization, $package, $user);

    expect($service->processExpiredCredits())->toBe(0);
    expect($service->processExpiredCredits())->toBe(0);
    expect($organization->getCurrentCreditBalance())->toBe(10);
});

test('only the unused remainder of a purchase expires', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create();
    $package = CreditPackage::factory()->create(['credits' => 10, 'price' => 50]);

    $service = app(CreditService::class);
    $service->purchaseCredits($organization, $package, $user);
    $service->useCredits($organization, 3, $user);

    $this->travel(CreditTransaction::EXPIRATION_YEARS)->years();
    $this->travel(1)->day();
    $service->purchaseCredits($organization, $package, $user);

    expect($service->processExpiredCredits())->toBe(7);
    expect($organization->getCurrentCreditBalance())->toBe(10);
});

test('credits transferred to a facility keep their expiry date', function () {
    $organization = Organization::factory()->create();
    $facility = Facility::factory()->create(['organization_id' => $organization->id]);
    $user = User::factory()->create();
    $package = CreditPackage::factory()->create(['credits' => 10, 'price' => 50]);

    $service = app(CreditService::class);
    $service->purchaseCredits($organization, $package, $user);
    $service->transferCredits($organization, $facility, 4, $user);

    $this->travel(CreditTransaction::EXPIRATION_YEARS)->years();
    $this->travel(1)->day();

    expect($service->processExpiredCredits())->toBe(10);
    expect($organization->getCurrentCreditBalance())->toBe(0);
    expect($facility->getCurrentCreditBalance())->toBe(0);
});

test('admin granted credits do not expire', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create();

    $service = app(CreditService::class);
    $service->addCredits($organization, 5, $user);

    $this->travel(CreditTransaction::EXPIRATION_YEARS + 1)->years();

    expect($service->processExpiredCredits())->toBe(0);
    expect($organization->getCurrentCreditBalance())->toBe(5);
});
