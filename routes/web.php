<?php

use App\Http\Controllers\CreditController;
use App\Http\Controllers\CreditPackageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Settings;
use App\Http\Controllers\SitemapController;
use App\Http\Middleware\PasswordExpired as PasswordExpiredAlias;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $latestJobs = \App\Models\JobPosting::active()
        ->with(['facility.address', 'facility.organization'])
        ->orderBy('published_at', 'desc')
        ->limit(5)
        ->get();

    return view('welcome', ['latestJobs' => $latestJobs]);
})->name('home');

// Öffentliche Preisübersicht
Route::get('/preise', [\App\Http\Controllers\PublicPricingController::class, 'index'])->name('public.pricing');

// Sitemap für SEO
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', PasswordExpiredAlias::class])
    ->name('dashboard');

Route::middleware(['auth', 'verified', ])->group(function () {
    Route::get('settings/password', [Settings\PasswordController::class, 'edit'])->name('settings.password.edit');
    Route::put('settings/password', [Settings\PasswordController::class, 'update'])->name('settings.password.update');
});


Route::middleware(['auth', 'verified', PasswordExpiredAlias::class])->group(function () {
    // Hilfe-Seite
    Route::get('help', [HelpController::class, 'index'])->name('help');

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
    Route::resource('credits/packages', CreditPackageController::class)->names([
        'index' => 'credits.packages.index',
        'create' => 'credits.packages.create',
        'store' => 'credits.packages.store',
        'show' => 'credits.packages.show',
        'edit' => 'credits.packages.edit',
        'update' => 'credits.packages.update',
        'destroy' => 'credits.packages.destroy',
    ]);

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

    // Role and Permission management
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);

    // Job Postings
    Route::prefix('job-postings')->name('job-postings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\JobPostingController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\JobPostingController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\JobPostingController::class, 'store'])->name('store');
        Route::get('/{jobPosting}', [\App\Http\Controllers\JobPostingController::class, 'show'])->name('show');
        Route::get('/{jobPosting}/edit', [\App\Http\Controllers\JobPostingController::class, 'edit'])->name('edit');
        Route::put('/{jobPosting}', [\App\Http\Controllers\JobPostingController::class, 'update'])->name('update');
        Route::delete('/{jobPosting}', [\App\Http\Controllers\JobPostingController::class, 'destroy'])->name('destroy');

        // Actions
        Route::post('/{jobPosting}/publish', [\App\Http\Controllers\JobPostingController::class, 'publish'])->name('publish');
        Route::post('/{jobPosting}/extend', [\App\Http\Controllers\JobPostingController::class, 'extend'])->name('extend');
        Route::post('/{jobPosting}/pause', [\App\Http\Controllers\JobPostingController::class, 'pause'])->name('pause');
        Route::post('/{jobPosting}/resume', [\App\Http\Controllers\JobPostingController::class, 'resume'])->name('resume');
    });

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        // Admin Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
            ->middleware('permission:admin view users|admin view organizations|admin view facilities|admin view job postings')
            ->name('dashboard');

        // Admin User Management
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)
            ->middleware([
                'index' => 'permission:admin view users',
                'create' => 'permission:admin create users',
                'store' => 'permission:admin create users',
                'show' => 'permission:admin view users',
                'edit' => 'permission:admin edit users',
                'update' => 'permission:admin edit users',
                'destroy' => 'permission:admin delete users',
            ]);

        // User Impersonation
        Route::post('users/{user}/impersonate', [\App\Http\Controllers\Admin\ImpersonateController::class, 'start'])
            ->middleware('permission:admin impersonate users')
            ->name('users.impersonate');
        Route::post('impersonate/stop', [\App\Http\Controllers\Admin\ImpersonateController::class, 'stop'])
            ->name('impersonate.stop');

        // Admin Organization Management
        Route::resource('organizations', \App\Http\Controllers\Admin\OrganizationController::class)
            ->only(['index', 'show', 'edit', 'update', 'destroy'])
            ->middleware([
                'index' => 'permission:admin view organizations',
                'show' => 'permission:admin view organizations',
                'edit' => 'permission:admin edit organizations',
                'update' => 'permission:admin edit organizations',
                'destroy' => 'permission:admin delete organizations',
            ]);

        // Organization Approval
        Route::post('organizations/{organization}/approve', [\App\Http\Controllers\Admin\OrganizationController::class, 'approve'])
            ->middleware('permission:admin edit organizations')
            ->name('organizations.approve');
        Route::post('organizations/{organization}/unapprove', [\App\Http\Controllers\Admin\OrganizationController::class, 'unapprove'])
            ->middleware('permission:admin edit organizations')
            ->name('organizations.unapprove');

        // Admin Facility Management
        Route::resource('facilities', \App\Http\Controllers\Admin\FacilityController::class)
            ->only(['index', 'show', 'edit', 'update', 'destroy'])
            ->middleware([
                'index' => 'permission:admin view facilities',
                'show' => 'permission:admin view facilities',
                'edit' => 'permission:admin edit facilities',
                'update' => 'permission:admin edit facilities',
                'destroy' => 'permission:admin delete facilities',
            ]);

        // Admin Job Posting Management
        Route::prefix('job-postings')->name('job-postings.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\JobPostingController::class, 'index'])
                ->middleware('permission:admin view job postings')
                ->name('index');
            Route::get('/{jobPosting}', [\App\Http\Controllers\Admin\JobPostingController::class, 'show'])
                ->middleware('permission:admin view job postings')
                ->name('show');
            Route::get('/{jobPosting}/edit', [\App\Http\Controllers\Admin\JobPostingController::class, 'edit'])
                ->middleware('permission:admin edit job postings')
                ->name('edit');
            Route::put('/{jobPosting}', [\App\Http\Controllers\Admin\JobPostingController::class, 'update'])
                ->middleware('permission:admin edit job postings')
                ->name('update');
            Route::delete('/{jobPosting}', [\App\Http\Controllers\Admin\JobPostingController::class, 'destroy'])
                ->middleware('permission:admin delete job postings')
                ->name('destroy');
            Route::post('/{jobPosting}/publish', [\App\Http\Controllers\Admin\JobPostingController::class, 'publish'])
                ->middleware('permission:admin publish job postings')
                ->name('publish');
            Route::post('/{jobPosting}/pause', [\App\Http\Controllers\Admin\JobPostingController::class, 'pause'])
                ->middleware('permission:admin publish job postings')
                ->name('pause');
            Route::post('/{jobPosting}/resume', [\App\Http\Controllers\Admin\JobPostingController::class, 'resume'])
                ->middleware('permission:admin publish job postings')
                ->name('resume');
            Route::post('/{jobPosting}/extend', [\App\Http\Controllers\Admin\JobPostingController::class, 'extend'])
                ->middleware('permission:admin publish job postings')
                ->name('extend');
        });

        // Admin Credit Management
        Route::prefix('credits')->name('credits.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\CreditController::class, 'index'])
                ->middleware('permission:admin view credits')
                ->name('index');
            Route::get('/transactions', [\App\Http\Controllers\Admin\CreditController::class, 'transactions'])
                ->middleware('permission:admin view credits')
                ->name('transactions');
            Route::get('/grant', [\App\Http\Controllers\Admin\CreditController::class, 'grant'])
                ->middleware('permission:admin grant credits')
                ->name('grant');
            Route::post('/grant', [\App\Http\Controllers\Admin\CreditController::class, 'storeGrant'])
                ->middleware('permission:admin grant credits')
                ->name('grant.store');
            Route::get('/revoke', [\App\Http\Controllers\Admin\CreditController::class, 'revoke'])
                ->middleware('permission:admin grant credits')
                ->name('revoke');
            Route::post('/revoke', [\App\Http\Controllers\Admin\CreditController::class, 'storeRevoke'])
                ->middleware('permission:admin grant credits')
                ->name('revoke.store');
        });

        // Admin Job Posting Credit Exemptions
        Route::resource('job-posting-credit-exemptions', \App\Http\Controllers\Admin\JobPostingCreditExemptionController::class)
            ->middleware([
                'index' => 'permission:admin view credits',
                'create' => 'permission:admin grant credits',
                'store' => 'permission:admin grant credits',
                'show' => 'permission:admin view credits',
                'edit' => 'permission:admin grant credits',
                'update' => 'permission:admin grant credits',
                'destroy' => 'permission:admin grant credits',
            ]);
        Route::post('job-posting-credit-exemptions/{jobPostingCreditExemption}/toggle',
            [\App\Http\Controllers\Admin\JobPostingCreditExemptionController::class, 'toggle'])
            ->middleware('permission:admin grant credits')
            ->name('job-posting-credit-exemptions.toggle');

        // Admin Audit Logs
        Route::prefix('audits')->name('audits.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AuditController::class, 'index'])
                ->middleware('permission:admin view logs')
                ->name('index');
            Route::get('/{audit}', [\App\Http\Controllers\Admin\AuditController::class, 'show'])
                ->middleware('permission:admin view logs')
                ->name('show');
        });

        // Admin Search Analytics
        Route::get('/search-analytics', [\App\Http\Controllers\Admin\SearchAnalyticsController::class, 'index'])
            ->middleware('permission:admin view logs')
            ->name('search-analytics.index');

        // Admin Logs (application log entries from database)
        Route::prefix('logs')->name('logs.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\LogController::class, 'index'])
                ->middleware('permission:admin view logs')
                ->name('index');
            Route::get('/export', [\App\Http\Controllers\Admin\LogController::class, 'export'])
                ->middleware('permission:admin view logs')
                ->name('export');
            Route::delete('/clear', [\App\Http\Controllers\Admin\LogController::class, 'clear'])
                ->middleware('permission:admin view logs')
                ->name('clear');
            Route::get('/{id}', [\App\Http\Controllers\Admin\LogController::class, 'show'])
                ->middleware('permission:admin view logs')
                ->name('show');
        });

        // Admin Failed Jobs
        Route::prefix('failed-jobs')->name('failed-jobs.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\FailedJobController::class, 'index'])
                ->middleware('permission:admin view logs')
                ->name('index');
            Route::get('/{id}', [\App\Http\Controllers\Admin\FailedJobController::class, 'show'])
                ->middleware('permission:admin view logs')
                ->name('show');
            Route::post('/{id}/retry', [\App\Http\Controllers\Admin\FailedJobController::class, 'retry'])
                ->middleware('permission:admin manage jobs')
                ->name('retry');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\FailedJobController::class, 'destroy'])
                ->middleware('permission:admin manage jobs')
                ->name('destroy');
        });

        // Admin Footer Settings
        Route::resource('footer-settings', \App\Http\Controllers\Admin\FooterSettingController::class)
            ->middleware([
                'index' => 'permission:admin edit organizations',
                'create' => 'permission:admin edit organizations',
                'store' => 'permission:admin edit organizations',
                'show' => 'permission:admin edit organizations',
                'edit' => 'permission:admin edit organizations',
                'update' => 'permission:admin edit organizations',
                'destroy' => 'permission:admin delete organizations',
            ]);
        Route::post('footer-settings/{footerSetting}/activate',
            [\App\Http\Controllers\Admin\FooterSettingController::class, 'activate'])
            ->middleware('permission:admin edit organizations')
            ->name('footer-settings.activate');
    });
});

// CMS Routes (separate from Admin, accessible with CMS permissions only)
Route::prefix('cms')->middleware(['auth', 'verified'])->group(function () {
    // CMS Pages Management
    Route::prefix('pages')->name('cms.pages.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PageController::class, 'index'])
            ->middleware('permission:admin view pages')
            ->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\PageController::class, 'create'])
            ->middleware('permission:admin create pages')
            ->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\PageController::class, 'store'])
            ->middleware('permission:admin create pages')
            ->name('store');
        Route::get('/{page}', [\App\Http\Controllers\Admin\PageController::class, 'show'])
            ->middleware('permission:admin view pages')
            ->name('show');
        Route::get('/{page}/edit', [\App\Http\Controllers\Admin\PageController::class, 'edit'])
            ->middleware('permission:admin edit pages')
            ->name('edit');
        Route::put('/{page}', [\App\Http\Controllers\Admin\PageController::class, 'update'])
            ->middleware('permission:admin edit pages')
            ->name('update');
        Route::delete('/{page}', [\App\Http\Controllers\Admin\PageController::class, 'destroy'])
            ->middleware('permission:admin delete pages')
            ->name('destroy');
        Route::post('/{page}/publish', [\App\Http\Controllers\Admin\PageController::class, 'publish'])
            ->middleware('permission:admin publish pages')
            ->name('publish');
        Route::post('/{page}/unpublish', [\App\Http\Controllers\Admin\PageController::class, 'unpublish'])
            ->middleware('permission:admin publish pages')
            ->name('unpublish');
        Route::post('/{page}/duplicate', [\App\Http\Controllers\Admin\PageController::class, 'duplicate'])
            ->middleware('permission:admin create pages')
            ->name('duplicate');
        Route::get('/{page}/preview', [\App\Http\Controllers\Admin\PageController::class, 'preview'])
            ->middleware('permission:admin view pages')
            ->name('preview');

        // Page Images
        Route::prefix('{page}/images')->name('images.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\PageImageController::class, 'index'])
                ->middleware('permission:admin manage page images')
                ->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\PageImageController::class, 'store'])
                ->middleware('permission:admin manage page images')
                ->name('store');
            Route::put('/{image}', [\App\Http\Controllers\Admin\PageImageController::class, 'update'])
                ->middleware('permission:admin manage page images')
                ->name('update');
            Route::delete('/{image}', [\App\Http\Controllers\Admin\PageImageController::class, 'destroy'])
                ->middleware('permission:admin manage page images')
                ->name('destroy');
            Route::post('/reorder', [\App\Http\Controllers\Admin\PageImageController::class, 'reorder'])
                ->middleware('permission:admin manage page images')
                ->name('reorder');
        });

        // Content Blocks
        Route::prefix('{page}/blocks')->name('blocks.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ContentBlockController::class, 'index'])
                ->middleware('permission:admin edit pages')
                ->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\ContentBlockController::class, 'store'])
                ->middleware('permission:admin edit pages')
                ->name('store');
            Route::put('/{block}', [\App\Http\Controllers\Admin\ContentBlockController::class, 'update'])
                ->middleware('permission:admin edit pages')
                ->name('update');
            Route::delete('/{block}', [\App\Http\Controllers\Admin\ContentBlockController::class, 'destroy'])
                ->middleware('permission:admin edit pages')
                ->name('destroy');
            Route::post('/reorder', [\App\Http\Controllers\Admin\ContentBlockController::class, 'reorder'])
                ->middleware('permission:admin edit pages')
                ->name('reorder');
        });
    });

    // CMS Menu Management
    Route::prefix('menus')->name('cms.menus.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\MenuController::class, 'index'])
            ->middleware('permission:admin manage menus')
            ->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\MenuController::class, 'store'])
            ->middleware('permission:admin manage menus')
            ->name('store');
        Route::put('/{menuItem}', [\App\Http\Controllers\Admin\MenuController::class, 'update'])
            ->middleware('permission:admin manage menus')
            ->name('update');
        Route::delete('/{menuItem}', [\App\Http\Controllers\Admin\MenuController::class, 'destroy'])
            ->middleware('permission:admin manage menus')
            ->name('destroy');
        Route::post('/reorder', [\App\Http\Controllers\Admin\MenuController::class, 'reorder'])
            ->middleware('permission:admin manage menus')
            ->name('reorder');
    });
});

// Public job postings (no auth required)
Route::prefix('jobs')->name('public.jobs.')->group(function () {
    Route::get('/', [\App\Http\Controllers\PublicJobPostingController::class, 'index'])->name('index');
    Route::get('/{jobPosting}', [\App\Http\Controllers\PublicJobPostingController::class, 'show'])->name('show');
    Route::get('/{jobPosting}/pdf', [\App\Http\Controllers\PublicJobPostingController::class, 'exportPdf'])->name('pdf');

    // Interaction tracking API
    Route::post('/{jobPosting}/track', [\App\Http\Controllers\Api\JobPostingInteractionController::class, 'track'])
        ->name('track');
});

require __DIR__.'/auth.php';

// Public CMS pages (must be last to not interfere with other routes)
Route::get('/{slug}', [\App\Http\Controllers\PageController::class, 'show'])
    ->where('slug', '[a-zA-Z0-9\-]+')
    ->name('pages.show');

