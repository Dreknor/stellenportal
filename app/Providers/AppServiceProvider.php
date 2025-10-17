<?php

namespace App\Providers;

use App\Models\CreditPackage;
use App\Models\JobPosting;
use App\Policies\CreditPackagePolicy;
use App\Policies\CreditPolicy;
use App\Policies\RolePolicy;
use App\Policies\PermissionPolicy;
use App\Policies\JobPostingPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

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

        // Register Role and Permission Policies
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);

        // Register Job Posting Policy
        Gate::policy(JobPosting::class, JobPostingPolicy::class);
    }
}
