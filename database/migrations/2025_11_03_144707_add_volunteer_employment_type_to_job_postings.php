<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the employment_type enum to include 'volunteer'
        \DB::statement("ALTER TABLE job_postings MODIFY COLUMN employment_type ENUM('full_time', 'part_time', 'mini_job', 'internship', 'apprenticeship', 'volunteer') NOT NULL DEFAULT 'full_time'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'volunteer' from the employment_type enum
        \DB::statement("ALTER TABLE job_postings MODIFY COLUMN employment_type ENUM('full_time', 'part_time', 'mini_job', 'internship', 'apprenticeship') NOT NULL DEFAULT 'full_time'");
    }
};
