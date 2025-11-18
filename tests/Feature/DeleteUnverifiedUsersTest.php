<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

test('unverified users older than 7 days are deleted', function () {
    // Create a verified user (should not be deleted)
    $verifiedUser = User::factory()->create([
        'email_verified_at' => now(),
        'created_at' => now()->subDays(10),
    ]);

    // Create an unverified user older than 7 days (should be deleted)
    $oldUnverifiedUser = User::factory()->unverified()->create([
        'created_at' => now()->subDays(8),
        'updated_at' => now()->subDays(8),
    ]);

    // Create an unverified user less than 7 days old (should not be deleted)
    $recentUnverifiedUser = User::factory()->unverified()->create([
        'created_at' => now()->subDays(5),
        'updated_at' => now()->subDays(5),
    ]);

    // Run the command
    $this->artisan('users:delete-unverified --days=7')
        ->expectsOutput('Lösche nicht verifizierte Benutzerkonten, die älter als 7 Tage sind...')
        ->expectsOutput('Es wurden 1 nicht verifizierte Benutzerkonten gelöscht.')
        ->assertExitCode(0);

    // Assert that only the old unverified user was deleted
    expect(User::find($verifiedUser->id))->not->toBeNull();
    expect(User::find($oldUnverifiedUser->id))->toBeNull();
    expect(User::find($recentUnverifiedUser->id))->not->toBeNull();
});

test('no users are deleted when all are verified or recent', function () {
    // Create verified users
    User::factory()->count(3)->create([
        'email_verified_at' => now(),
        'created_at' => now()->subDays(10),
    ]);

    // Create recent unverified user
    User::factory()->unverified()->create([
        'created_at' => now()->subDays(3),
    ]);

    $initialCount = User::count();

    // Run the command
    $this->artisan('users:delete-unverified --days=7')
        ->expectsOutput('Keine nicht verifizierten Benutzerkonten zum Löschen gefunden.')
        ->assertExitCode(0);

    // Assert that no users were deleted
    expect(User::count())->toBe($initialCount);
});

test('custom days parameter works correctly', function () {
    // Create an unverified user that is 5 days old
    $user = User::factory()->unverified()->create([
        'created_at' => now()->subDays(5),
        'updated_at' => now()->subDays(5),
    ]);

    // Run with 3 days threshold (should delete the 5-day-old user)
    $this->artisan('users:delete-unverified --days=3')
        ->expectsOutput('Lösche nicht verifizierte Benutzerkonten, die älter als 3 Tage sind...')
        ->expectsOutput('Es wurden 1 nicht verifizierte Benutzerkonten gelöscht.')
        ->assertExitCode(0);

    expect(User::find($user->id))->toBeNull();
});

test('deletion is logged correctly', function () {
    Log::spy();

    $user = User::factory()->unverified()->create([
        'email' => 'test@example.com',
        'created_at' => now()->subDays(10),
        'updated_at' => now()->subDays(10),
    ]);

    $this->artisan('users:delete-unverified --days=7');

    Log::shouldHaveReceived('info')
        ->once()
        ->with('Deleting unverified user', Mockery::on(function ($context) use ($user) {
            return $context['user_id'] === $user->id
                && $context['email'] === 'test@example.com'
                && $context['days_since_creation'] >= 10;
        }));
});

