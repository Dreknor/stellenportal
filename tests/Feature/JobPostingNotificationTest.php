<?php

use App\Models\JobPosting;
use App\Models\User;
use App\Models\Organization;
use App\Models\Facility;
use App\Models\Address;
use App\Mail\JobPostingExpiredMail;
use App\Mail\JobPostingExpiringMail;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
});

test('expired job posting sends notification email to creator', function () {
    // Create organization, facility, and user
    $organization = Organization::factory()->create();
    $facility = Facility::factory()->create([
        'organization_id' => $organization->id,
    ]);

    $user = User::factory()->create();
    $user->facilities()->attach($facility->id);

    // Create an expired job posting
    $jobPosting = JobPosting::factory()->create([
        'facility_id' => $facility->id,
        'created_by' => $user->id,
        'status' => JobPosting::STATUS_ACTIVE,
        'published_at' => now()->subMonths(4),
        'expires_at' => now()->subDay(), // Expired yesterday
    ]);

    // Run the command
    $this->artisan('job-postings:mark-expired')
        ->expectsOutput('Markiere abgelaufene Stellenausschreibungen...')
        ->assertExitCode(0);

    // Assert email was sent
    Mail::assertSent(JobPostingExpiredMail::class, function ($mail) use ($user, $jobPosting) {
        return $mail->hasTo($user->email) &&
               $mail->user->id === $user->id &&
               $mail->jobPosting->id === $jobPosting->id;
    });

    // Assert job posting status was updated
    expect($jobPosting->fresh()->status)->toBe(JobPosting::STATUS_EXPIRED);
});

test('expiring job posting sends warning email to creator', function () {
    // Create organization, facility, and user
    $organization = Organization::factory()->create();
    $facility = Facility::factory()->create([
        'organization_id' => $organization->id,
    ]);

    $user = User::factory()->create();
    $user->facilities()->attach($facility->id);

    // Create a job posting that expires in 7 days
    $jobPosting = JobPosting::factory()->create([
        'facility_id' => $facility->id,
        'created_by' => $user->id,
        'status' => JobPosting::STATUS_ACTIVE,
        'published_at' => now()->subMonths(2),
        'expires_at' => now()->addDays(7),
    ]);

    // Run the command
    $this->artisan('job-postings:notify-expiring --days=7')
        ->expectsOutput('Sende Benachrichtigungen für Stellenausschreibungen, die in 7 Tagen ablaufen...')
        ->assertExitCode(0);

    // Assert email was sent
    Mail::assertSent(JobPostingExpiringMail::class, function ($mail) use ($user, $jobPosting) {
        return $mail->hasTo($user->email) &&
               $mail->user->id === $user->id &&
               $mail->jobPosting->id === $jobPosting->id &&
               $mail->daysUntilExpiration >= 6 &&
               $mail->daysUntilExpiration <= 7;
    });

    // Assert job posting status is still active
    expect($jobPosting->fresh()->status)->toBe(JobPosting::STATUS_ACTIVE);
});

test('only active job postings expiring on the exact date receive notifications', function () {
    // Create organization, facility, and user
    $organization = Organization::factory()->create();
    $facility = Facility::factory()->create([
        'organization_id' => $organization->id,
    ]);

    $user = User::factory()->create();
    $user->facilities()->attach($facility->id);

    // Create job postings with different expiration dates
    JobPosting::factory()->create([
        'facility_id' => $facility->id,
        'created_by' => $user->id,
        'status' => JobPosting::STATUS_ACTIVE,
        'expires_at' => now()->addDays(7), // Should receive notification
    ]);

    JobPosting::factory()->create([
        'facility_id' => $facility->id,
        'created_by' => $user->id,
        'status' => JobPosting::STATUS_ACTIVE,
        'expires_at' => now()->addDays(6), // Should NOT receive notification
    ]);

    JobPosting::factory()->create([
        'facility_id' => $facility->id,
        'created_by' => $user->id,
        'status' => JobPosting::STATUS_DRAFT, // Should NOT receive notification (not active)
        'expires_at' => now()->addDays(7),
    ]);

    // Run the command
    $this->artisan('job-postings:notify-expiring --days=7')
        ->assertExitCode(0);

    // Assert only one email was sent
    Mail::assertSent(JobPostingExpiringMail::class, 1);
});

test('multiple expired job postings send individual notification emails', function () {
    // Create organization, facility
    $organization = Organization::factory()->create();
    $facility = Facility::factory()->create([
        'organization_id' => $organization->id,
    ]);

    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Create two expired job postings for different users
    JobPosting::factory()->create([
        'facility_id' => $facility->id,
        'created_by' => $user1->id,
        'status' => JobPosting::STATUS_ACTIVE,
        'expires_at' => now()->subDay(),
    ]);

    JobPosting::factory()->create([
        'facility_id' => $facility->id,
        'created_by' => $user2->id,
        'status' => JobPosting::STATUS_ACTIVE,
        'expires_at' => now()->subDay(),
    ]);

    // Run the command
    $this->artisan('job-postings:mark-expired')
        ->assertExitCode(0);

    // Assert two emails were sent
    Mail::assertSent(JobPostingExpiredMail::class, 2);

    // Assert emails were sent to the correct users
    Mail::assertSent(JobPostingExpiredMail::class, function ($mail) use ($user1) {
        return $mail->hasTo($user1->email);
    });

    Mail::assertSent(JobPostingExpiredMail::class, function ($mail) use ($user2) {
        return $mail->hasTo($user2->email);
    });
});

