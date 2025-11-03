<?php

namespace Database\Seeders;

use App\Models\JobPosting;
use App\Models\JobPostingCreditExemption;
use Illuminate\Database\Seeder;

class JobPostingCreditExemptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ehrenamt für alle Organisationen von Guthabenpflicht befreien
        JobPostingCreditExemption::updateOrCreate(
            [
                'employment_type' => JobPosting::EMPLOYMENT_TYPE_VOLUNTEER,
                'applies_to' => JobPostingCreditExemption::APPLIES_TO_COOPERATIVE_MEMBERS_ONLY,
            ],
            [
                'is_active' => true,
                'description' => 'Ehrenamtliche Stellen sind grundsätzlich von der Guthabenpflicht befreit, um gemeinnützige Arbeit zu fördern.',
            ]
        );

        $this->command->info('Standard-Guthabenausnahmen wurden erstellt.');
    }
}

