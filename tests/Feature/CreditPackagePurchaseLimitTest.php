<?php

use App\Models\CreditPackage;
use App\Models\CreditTransaction;
use App\Models\Facility;
use App\Models\Organization;
use App\Models\User;
use App\Services\CreditService;

beforeEach(function () {
    $this->creditService = app(CreditService::class);
});

test('package without limit can be purchased multiple times', function () {
    $organization = Organization::factory()->create(['is_approved' => true]);
    $user = User::factory()->create();

    $package = CreditPackage::factory()->create([
        'purchase_limit_per_organization' => null,
        'is_active' => true,
    ]);

    // Purchase 3 times
    for ($i = 0; $i < 3; $i++) {
        expect($package->canBePurchasedBy($organization))->toBeTrue();
        $this->creditService->purchaseCredits($organization, $package, $user);
    }

    expect($package->getPurchaseCountForOrganization($organization))->toBe(3);
    expect($package->canBePurchasedBy($organization))->toBeTrue();
});

test('package with limit can be purchased up to limit', function () {
    $organization = Organization::factory()->create(['is_approved' => true]);
    $user = User::factory()->create();

    $package = CreditPackage::factory()->create([
        'purchase_limit_per_organization' => 2,
        'is_active' => true,
    ]);

    // First purchase - should work
    expect($package->canBePurchasedBy($organization))->toBeTrue();
    expect($package->getRemainingPurchasesFor($organization))->toBe(2);
    $this->creditService->purchaseCredits($organization, $package, $user);

    // Second purchase - should work
    expect($package->canBePurchasedBy($organization))->toBeTrue();
    expect($package->getRemainingPurchasesFor($organization))->toBe(1);
    $this->creditService->purchaseCredits($organization, $package, $user);

    // Third purchase - should fail
    expect($package->canBePurchasedBy($organization))->toBeFalse();
    expect($package->getRemainingPurchasesFor($organization))->toBe(0);

    expect(fn() => $this->creditService->purchaseCredits($organization, $package, $user))
        ->toThrow(\Exception::class, 'Kauflimit');
});

test('purchase limit counts facility purchases', function () {
    $organization = Organization::factory()->create(['is_approved' => true]);
    $facility = Facility::factory()->create(['organization_id' => $organization->id]);
    $user = User::factory()->create();

    $package = CreditPackage::factory()->create([
        'purchase_limit_per_organization' => 1,
        'is_active' => true,
    ]);

    // Purchase from facility
    $this->creditService->purchaseCredits($facility, $package, $user);

    // Organization should not be able to purchase anymore
    expect($package->canBePurchasedBy($organization))->toBeFalse();
    expect($package->getRemainingPurchasesFor($organization))->toBe(0);

    // Facility should also not be able to purchase
    expect($package->canBePurchasedBy($facility))->toBeFalse();
    expect($package->getRemainingPurchasesFor($facility))->toBe(0);
});

test('purchase limit is organization specific', function () {
    $organization1 = Organization::factory()->create(['is_approved' => true]);
    $organization2 = Organization::factory()->create(['is_approved' => true]);
    $user = User::factory()->create();

    $package = CreditPackage::factory()->create([
        'purchase_limit_per_organization' => 1,
        'is_active' => true,
    ]);

    // Organization 1 purchases
    $this->creditService->purchaseCredits($organization1, $package, $user);
    expect($package->canBePurchasedBy($organization1))->toBeFalse();

    // Organization 2 should still be able to purchase
    expect($package->canBePurchasedBy($organization2))->toBeTrue();
    expect($package->getRemainingPurchasesFor($organization2))->toBe(1);
    $this->creditService->purchaseCredits($organization2, $package, $user);
    expect($package->canBePurchasedBy($organization2))->toBeFalse();
});

test('has purchase limit returns correct value', function () {
    $package1 = CreditPackage::factory()->create(['purchase_limit_per_organization' => null]);
    $package2 = CreditPackage::factory()->create(['purchase_limit_per_organization' => 0]);
    $package3 = CreditPackage::factory()->create(['purchase_limit_per_organization' => 1]);

    expect($package1->hasPurchaseLimit())->toBeFalse();
    expect($package2->hasPurchaseLimit())->toBeFalse();
    expect($package3->hasPurchaseLimit())->toBeTrue();
});

test('get remaining purchases returns null for unlimited', function () {
    $organization = Organization::factory()->create(['is_approved' => true]);
    $package = CreditPackage::factory()->create(['purchase_limit_per_organization' => null]);

    expect($package->getRemainingPurchasesFor($organization))->toBeNull();
});

