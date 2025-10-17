<?php

namespace App\Providers;

use App\Models\CreditPackage;
use App\Policies\CreditPackagePolicy;
use App\Policies\CreditPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Credit Policies
        Gate::policy(CreditPackage::class, CreditPackagePolicy::class);

        // Register gates for credit operations
        Gate::define('purchaseCredits', [CreditPolicy::class, 'purchaseCredits']);
        Gate::define('transferCredits', [CreditPolicy::class, 'transferCredits']);
        Gate::define('viewTransactions', [CreditPolicy::class, 'viewTransactions']);
    }
}
