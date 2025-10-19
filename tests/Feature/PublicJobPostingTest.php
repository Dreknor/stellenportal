<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\JobPosting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicJobPostingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_view_active_job_postings(): void
    {
        $jobPosting = JobPosting::factory()->active()->create();

        $response = $this->get(route('public.jobs.index'));

        $response->assertStatus(200);
        $response->assertSee($jobPosting->title);
    }

    public function test_public_can_view_single_active_job_posting(): void
    {
        $jobPosting = JobPosting::factory()->active()->create([
            'title' => 'Software Engineer Position',
            'description' => 'We are looking for a talented software engineer',
        ]);

        $response = $this->get(route('public.jobs.show', $jobPosting));

        $response->assertStatus(200);
        $response->assertSee($jobPosting->title);
        $response->assertSee('Stellenbeschreibung'); // Section header from the view
    }

    public function test_public_cannot_view_draft_job_postings(): void
    {
        $jobPosting = JobPosting::factory()->draft()->create();

        $response = $this->get(route('public.jobs.show', $jobPosting));

        $response->assertStatus(404);
    }

    public function test_public_cannot_view_expired_job_postings(): void
    {
        $jobPosting = JobPosting::factory()->expired()->create();

        $response = $this->get(route('public.jobs.show', $jobPosting));

        $response->assertStatus(404);
    }

    public function test_job_postings_can_be_filtered_by_employment_type(): void
    {
        JobPosting::factory()->active()->create(['employment_type' => 'full_time']);
        JobPosting::factory()->active()->create(['employment_type' => 'part_time']);

        $response = $this->get(route('public.jobs.index', ['employment_type' => 'full_time']));

        $response->assertStatus(200);
    }

    public function test_job_postings_can_be_searched(): void
    {
        JobPosting::factory()->active()->create(['title' => 'Software Developer']);
        JobPosting::factory()->active()->create(['title' => 'Nurse']);

        $response = $this->get(route('public.jobs.index', ['search' => 'Software']));

        $response->assertStatus(200);
        $response->assertSee('Software Developer');
    }
}
