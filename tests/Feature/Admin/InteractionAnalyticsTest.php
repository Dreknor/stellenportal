<?php

use App\Models\JobPosting;
use App\Models\JobPostingInteraction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $adminRole = Role::firstOrCreate(['name' => 'admin']);
    $adminRole->givePermissionTo(['admin view logs']);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');

    $this->regularUser = User::factory()->create();
});

test('admin can view interaction analytics page', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.interaction-analytics.index'));

    $response->assertStatus(200);
    $response->assertViewIs('admin.interaction-analytics.index');
    $response->assertViewHas('stats');
    $response->assertViewHas('topJobPostings');
    $response->assertViewHas('dailyTrend');
    $response->assertViewHas('conversionRate');
    $response->assertViewHas('uniqueVisitors');
});

test('regular user cannot access interaction analytics', function () {
    $response = $this->actingAs($this->regularUser)->get(route('admin.interaction-analytics.index'));

    $response->assertStatus(403);
});

test('guest is redirected from interaction analytics', function () {
    $response = $this->get(route('admin.interaction-analytics.index'));

    $response->assertRedirect();
});

test('interaction analytics shows correct aggregated stats', function () {
    $jobPosting = JobPosting::factory()->active()->create();

    // Create various interactions
    JobPostingInteraction::create([
        'job_posting_id' => $jobPosting->id,
        'interaction_type' => JobPostingInteraction::TYPE_VIEW,
        'ip_address' => '1.2.3.4',
        'session_id' => 'session1',
    ]);
    JobPostingInteraction::create([
        'job_posting_id' => $jobPosting->id,
        'interaction_type' => JobPostingInteraction::TYPE_VIEW,
        'ip_address' => '5.6.7.8',
        'session_id' => 'session2',
    ]);
    JobPostingInteraction::create([
        'job_posting_id' => $jobPosting->id,
        'interaction_type' => JobPostingInteraction::TYPE_APPLY_CLICK,
        'ip_address' => '1.2.3.4',
        'session_id' => 'session1',
    ]);
    JobPostingInteraction::create([
        'job_posting_id' => $jobPosting->id,
        'interaction_type' => JobPostingInteraction::TYPE_DOWNLOAD,
        'ip_address' => '5.6.7.8',
        'session_id' => 'session2',
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.interaction-analytics.index', ['period' => 30]));

    $response->assertStatus(200);

    $stats = $response->viewData('stats');
    expect($stats['views'])->toBe(2);
    expect($stats['apply_clicks'])->toBe(1);
    expect($stats['downloads'])->toBe(1);
    expect($stats['total_interactions'])->toBe(4);

    expect($response->viewData('uniqueVisitors'))->toBe(2);
    expect($response->viewData('conversionRate'))->toBe(50.0);
});

test('interaction analytics period filter works', function () {
    $jobPosting = JobPosting::factory()->active()->create();

    // Recent interaction
    JobPostingInteraction::create([
        'job_posting_id' => $jobPosting->id,
        'interaction_type' => JobPostingInteraction::TYPE_VIEW,
        'ip_address' => '1.2.3.4',
        'session_id' => 'session1',
    ]);

    // Old interaction (outside 7-day window)
    $old = JobPostingInteraction::create([
        'job_posting_id' => $jobPosting->id,
        'interaction_type' => JobPostingInteraction::TYPE_VIEW,
        'ip_address' => '5.6.7.8',
        'session_id' => 'session2',
    ]);
    DB::table('job_posting_interactions')
        ->where('id', $old->id)
        ->update(['created_at' => now()->subDays(10)]);

    // 7-day period should only show 1
    $response = $this->actingAs($this->admin)->get(route('admin.interaction-analytics.index', ['period' => 7]));
    $stats = $response->viewData('stats');
    expect($stats['views'])->toBe(1);

    // 30-day period should show both
    $response = $this->actingAs($this->admin)->get(route('admin.interaction-analytics.index', ['period' => 30]));
    $stats = $response->viewData('stats');
    expect($stats['views'])->toBe(2);
});

test('top job postings are ranked by total interactions', function () {
    $posting1 = JobPosting::factory()->active()->create(['title' => 'Popular Posting']);
    $posting2 = JobPosting::factory()->active()->create(['title' => 'Less Popular Posting']);

    // posting1: 3 interactions
    for ($i = 0; $i < 3; $i++) {
        JobPostingInteraction::create([
            'job_posting_id' => $posting1->id,
            'interaction_type' => JobPostingInteraction::TYPE_VIEW,
            'ip_address' => "1.2.3.$i",
            'session_id' => "session-a-$i",
        ]);
    }

    // posting2: 1 interaction
    JobPostingInteraction::create([
        'job_posting_id' => $posting2->id,
        'interaction_type' => JobPostingInteraction::TYPE_VIEW,
        'ip_address' => '9.9.9.9',
        'session_id' => 'session-b-0',
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.interaction-analytics.index'));
    $topPostings = $response->viewData('topJobPostings');

    expect($topPostings->first()->title)->toBe('Popular Posting');
    expect($topPostings->first()->total_interactions)->toBe(3);
});

