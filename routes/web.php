<?php

use App\Http\Controllers\CreditController;
use App\Http\Controllers\CreditPackageController;
use App\Http\Controllers\Settings;
use App\Http\Middleware\PasswordExpired as PasswordExpiredAlias;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified', PasswordExpiredAlias::class])
    ->name('dashboard');

Route::middleware(['auth', 'verified', ])->group(function () {
    Route::get('settings/password', [Settings\PasswordController::class, 'edit'])->name('settings.password.edit');
    Route::put('settings/password', [Settings\PasswordController::class, 'update'])->name('settings.password.update');
});


Route::middleware(['auth', 'verified', PasswordExpiredAlias::class])->group(function () {
    Route::get('settings/profile', [Settings\ProfileController::class, 'edit'])->name('settings.profile.edit');
    Route::put('settings/profile', [Settings\ProfileController::class, 'update'])->name('settings.profile.update');
    Route::delete('settings/profile', [Settings\ProfileController::class, 'destroy'])->name('settings.profile.destroy');
    Route::get('settings/appearance', [Settings\AppearanceController::class, 'edit'])->name('settings.appearance.edit');

    Route::prefix('organizations')->name('organizations.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Organizations\OrganizationController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Organizations\OrganizationController::class, 'store'])->name('store');
        Route::get('/{organization}', [\App\Http\Controllers\Organizations\OrganizationController::class, 'show'])->name('show');
        Route::get('/{organization}/edit', [\App\Http\Controllers\Organizations\OrganizationController::class, 'edit'])->name('edit');
        Route::put('/{organization}', [\App\Http\Controllers\Organizations\OrganizationController::class, 'update'])->name('update');
        Route::delete('/{organization}', [\App\Http\Controllers\Organizations\OrganizationController::class, 'destroy'])->name('destroy');

        // User management routes for organizations
        Route::get('/{organization}/users', [\App\Http\Controllers\Organizations\OrganizationUserController::class, 'index'])->name('users.index');
        Route::post('/{organization}/users', [\App\Http\Controllers\Organizations\OrganizationUserController::class, 'store'])->name('users.store');
        Route::delete('/{organization}/users/{user}', [\App\Http\Controllers\Organizations\OrganizationUserController::class, 'destroy'])->name('users.destroy');
    });

    Route::prefix('facilities')->name('facilities.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Facilities\FacilityController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Facilities\FacilityController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Facilities\FacilityController::class, 'store'])->name('store');
        Route::get('/{facility}', [\App\Http\Controllers\Facilities\FacilityController::class, 'show'])->name('show');
        Route::get('/{facility}/edit', [\App\Http\Controllers\Facilities\FacilityController::class, 'edit'])->name('edit');
        Route::put('/{facility}', [\App\Http\Controllers\Facilities\FacilityController::class, 'update'])->name('update');
        Route::delete('/{facility}', [\App\Http\Controllers\Facilities\FacilityController::class, 'destroy'])->name('destroy');

        // User management routes for facilities
        Route::get('/{facility}/users', [\App\Http\Controllers\Facilities\FacilityUserController::class, 'index'])->name('users.index');
        Route::post('/{facility}/users', [\App\Http\Controllers\Facilities\FacilityUserController::class, 'store'])->name('users.store');
        Route::delete('/{facility}/users/{user}', [\App\Http\Controllers\Facilities\FacilityUserController::class, 'destroy'])->name('users.destroy');
    });

    // Guthaben-Pakete verwalten (nur mit Permission "manage credit packages")
    Route::resource('credits/packages', CreditPackageController::class);

    // Guthaben für Einrichtungen
    Route::get('facilities/{facility}/credits/purchase', [CreditController::class, 'showFacilityPurchase'])
        ->name('credits.facility.purchase');
    Route::post('facilities/{facility}/credits/purchase', [CreditController::class, 'purchaseFacilityCredits'])
        ->name('credits.facility.purchase.store');
    Route::get('facilities/{facility}/credits/transactions', [CreditController::class, 'facilityTransactions'])
        ->name('credits.facility.transactions');

    // Guthaben für Organisationen
    Route::get('organizations/{organization}/credits/purchase', [CreditController::class, 'showOrganizationPurchase'])
        ->name('credits.organization.purchase');
    Route::post('organizations/{organization}/credits/purchase', [CreditController::class, 'purchaseOrganizationCredits'])
        ->name('credits.organization.purchase.store');
    Route::get('organizations/{organization}/credits/transactions', [CreditController::class, 'organizationTransactions'])
        ->name('credits.organization.transactions');

    // Umbuchung von Organisation zu Einrichtung
    Route::get('organizations/{organization}/credits/transfer', [CreditController::class, 'showTransfer'])
        ->name('credits.organization.transfer');
    Route::post('organizations/{organization}/credits/transfer', [CreditController::class, 'transfer'])
        ->name('credits.organization.transfer.store');
});

require __DIR__.'/auth.php';
