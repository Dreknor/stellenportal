<?php

use App\Models\CreditTransaction;
use App\Models\CreditPackage;
use App\Models\Organization;
use App\Models\User;
use App\Services\CreditService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('purchased credits have expiration date set to 3 years', function () {
    $organization = Organization::factory()->create();
    $package = CreditPackage::factory()->create(['credits' => 100, 'price' => 50]);
    $user = User::factory()->create();

    $service = app(CreditService::class);
    $transaction = $service->purchaseCredits($organization, $package, $user);

    expect($transaction->expires_at)->not->toBeNull();
    expect($transaction->expires_at->diffInYears($transaction->created_at))->toBe(CreditTransaction::EXPIRATION_YEARS);
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

    // Setze initiales Guthaben
    $balance = $organization->creditBalance()->create(['balance' => 100]);

    // Erstelle einen abgelaufenen Credit-Kauf
    CreditTransaction::factory()->create([
        'creditable_id' => $organization->id,
        'creditable_type' => Organization::class,
        'user_id' => $user->id,
        'type' => CreditTransaction::TYPE_PURCHASE,
        'amount' => 50,
        'balance_after' => 100,
        'expires_at' => now()->subDay(),
    ]);

    $service = app(CreditService::class);
    $expiredCount = $service->processExpiredCredits();

    expect($expiredCount)->toBe(50);
    expect($balance->fresh()->balance)->toBe(50);
});

test('expiration transaction is created for expired credits', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create();

    // Setze initiales Guthaben
    $balance = $organization->creditBalance()->create(['balance' => 100]);

    // Erstelle einen abgelaufenen Credit-Kauf
    $expiredTransaction = CreditTransaction::factory()->create([
        'creditable_id' => $organization->id,
        'creditable_type' => Organization::class,
        'user_id' => $user->id,
        'type' => CreditTransaction::TYPE_PURCHASE,
        'amount' => 50,
        'balance_after' => 100,
        'expires_at' => now()->subDay(),
    ]);

    $service = app(CreditService::class);
    $service->processExpiredCredits();

    // Prüfe, ob eine Expiration-Transaktion erstellt wurde
    $expirationTransaction = CreditTransaction::where('type', CreditTransaction::TYPE_EXPIRATION)
        ->where('related_transaction_id', $expiredTransaction->id)
        ->first();

    expect($expirationTransaction)->not->toBeNull();
    expect($expirationTransaction->amount)->toBe(-50);
});


