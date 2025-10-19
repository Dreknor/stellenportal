<?php

namespace Database\Factories;

use App\Models\JobPosting;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobPostingFactory extends Factory
{
    protected $model = JobPosting::class;

    public function definition(): array
    {
        return [
            'facility_id' => Facility::factory(),
            'created_by' => User::factory(),
            'title' => fake()->jobTitle(),
            'description' => fake()->paragraphs(3, true),
            'employment_type' => fake()->randomElement([
                JobPosting::EMPLOYMENT_TYPE_FULL_TIME,
                JobPosting::EMPLOYMENT_TYPE_PART_TIME,
                JobPosting::EMPLOYMENT_TYPE_MINI_JOB,
                JobPosting::EMPLOYMENT_TYPE_INTERNSHIP,
                JobPosting::EMPLOYMENT_TYPE_APPRENTICESHIP,
            ]),
            'job_category' => fake()->word(),
            'requirements' => fake()->paragraph(),
            'benefits' => fake()->paragraph(),
            'contact_email' => fake()->companyEmail(),
            'contact_phone' => fake()->phoneNumber(),
            'contact_person' => fake()->name(),
            'status' => JobPosting::STATUS_DRAFT,
            'credits_used' => 0,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => JobPosting::STATUS_DRAFT,
            'published_at' => null,
            'expires_at' => null,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => JobPosting::STATUS_ACTIVE,
            'published_at' => now()->subDays(rand(1, 30)),
            'expires_at' => now()->addMonths(JobPosting::POSTING_DURATION_MONTHS),
            'credits_used' => JobPosting::CREDITS_PER_POSTING,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => JobPosting::STATUS_EXPIRED,
            'published_at' => now()->subMonths(4),
            'expires_at' => now()->subDay(),
            'credits_used' => JobPosting::CREDITS_PER_POSTING,
        ]);
    }

    public function paused(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => JobPosting::STATUS_PAUSED,
            'published_at' => now()->subDays(rand(1, 30)),
            'expires_at' => now()->addMonth(),
            'credits_used' => JobPosting::CREDITS_PER_POSTING,
        ]);
    }
}

