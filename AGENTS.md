# AGENTS.md – Stellenportal

## Overview
German-language **Laravel 12** job-posting portal for educational institutions ("Schulen"). Core domain: **Organizations → Facilities → Job Postings**, funded by a **credit system**. Includes a CMS for static pages and Keycloak SSO.

## Architecture
- **Multi-tenancy via relationships**: `User ↔ Organization (m:n)`, `Organization → Facility (1:n)`, `Facility → JobPosting (1:n)`. Authorization is determined by these associations, not by a tenant column.
- **Polymorphic patterns**: `Address` (morphOne on Organization/Facility), `CreditBalance`/`CreditTransaction` (morphOne/morphMany on Organization/Facility via `creditable`), `Media` (spatie/laravel-medialibrary on Organization/Facility/Address).
- **Service layer** (`app/Services/`): Business logic for credits and job-posting lifecycle lives in `CreditService` and `JobPostingService`, not in controllers. Always use these services for credit mutations or publishing.
- **Policy-based authorization** + **Spatie Permissions**: Policies (`app/Policies/`) guard model access; Spatie roles/permissions guard admin routes. Policies are registered in `AppServiceProvider::boot()`. Admin routes use `->middleware('permission:...')` inline.
- **Observers** (`app/Observers/`): `AddressObserver` geocodes addresses via external API on create/update. `LogEntryObserver` sends critical-log notifications. `PageObserver` cleans up images. `MenuItemObserver` clears menu cache.

## Tech Stack & Frontend
- **PHP 8.2+**, Laravel 12, Pest for testing, Larastan (level 7) for static analysis.
- **Frontend**: Blade + Alpine.js + Tailwind CSS 4 (via `@tailwindcss/vite`). TinyMCE for rich-text editing in CMS. No SPA/Inertia.
- **Vite** entry points: `resources/css/app.css`, `resources/js/app.js`.

## Key Commands
```bash
composer dev              # Runs server + queue worker + vite concurrently
composer test             # Clears config cache, then runs Pest tests
php artisan test --filter=CreditSystemTest   # Run a specific test
./vendor/bin/phpstan analyse                 # Static analysis (level 7)
php artisan db:seed                          # Seeds roles, permissions, credit packages, footer
```

### Scheduled Commands (`routes/console.php`)
| Command | Schedule | Purpose |
|---|---|---|
| `job-postings:mark-expired` | daily | Mark expired postings |
| `job-postings:notify-expiring --days=7` | daily | Warn about expiring postings |
| `credits:expire` | 01:00 | Expire old credits (3-year TTL) |
| `credits:notify-expiring --days=30` | 08:00 | Warn about expiring credits |
| `users:delete-unverified --days=5` | 02:00 | Remove unverified users |

## Conventions & Patterns
- **German UI, English code**: All user-facing strings, error messages, and mail texts are in German. Code identifiers (class/method/variable names) are English. Exception messages in services are German when user-facing.
- **Slugs**: `Organization`, `Facility`, `JobPosting`, `Page` use `spatie/laravel-sluggable`. `JobPosting` routes resolve by slug (`getRouteKeyName() → 'slug'`).
- **Auditing**: Most models implement `OwenIt\Auditing\Contracts\Auditable`. Keep this trait on any new model that stores user-managed data.
- **SoftDeletes**: Used on `JobPosting` and `Page`. Other models use hard deletes.
- **Credit lifecycle**: Purchase → balance update → transfer (org↔facility) → usage (on publish/extend) → expiration. All mutations go through `CreditService` inside DB transactions. 1 credit = 1 posting. Postings last 3 months.
- **JobPosting states**: `draft → active → expired/paused`. Publish and extend deduct credits (unless exempt). See `JobPostingService`.
- **Tests**: Pest (not PHPUnit syntax). Feature tests in `tests/Feature/`, unit tests in `tests/Unit/`. Feature tests use `Tests\TestCase` base. SQLite `:memory:` for test DB.
- **Media uploads**: Use Spatie Media Library collections (`header_image`, `logo`, `map`). Accept JPEG/PNG/WebP.
- **CMS pages**: Dynamic pages at `/{slug}` (catch-all, registered last in `routes/web.php`). Content blocks with reordering.
- **Queue**: Configurable via `QUEUE_WORKER_MODE` env (`cronjob` or `supervisor`). Dev uses `queue:listen`.
- **Authentication**: Standard Laravel auth + Keycloak SSO via `socialiteproviders/keycloak`. `PasswordExpired` middleware forces password change when `change_password` flag is set.
- **Organization approval**: Organizations must be approved (`is_approved`) before their users can create job postings (checked in `JobPostingPolicy::create`).

