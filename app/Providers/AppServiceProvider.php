<?php

namespace App\Providers;

use App\Mail\UserMailVerification;
use App\Models\CreditPackage;
use App\Models\JobPosting;
use App\Policies\CreditPackagePolicy;
use App\Policies\CreditPolicy;
use App\Policies\RolePolicy;
use App\Policies\PermissionPolicy;
use App\Policies\JobPostingPolicy;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
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

        // Use custom mailable for email verification
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            Log::debug('Generating custom email verification mail for user: ' . $notifiable->email);
            return (new UserMailVerification($notifiable, $url))->to($notifiable->email);
        });

        Event::listen(MessageSending::class, function (MessageSending $event) {

            if (config('mail.audit_bcc_address') === false) {
                return;
            }

            $bcc = config('mail.audit_bcc_address');

            // Symfony Email (Laravel 9+) hat bcc/addBcc
            if (method_exists($event->message, 'bcc')) {
                $event->message->bcc($bcc);
                return;
            }
        });
    }
}
