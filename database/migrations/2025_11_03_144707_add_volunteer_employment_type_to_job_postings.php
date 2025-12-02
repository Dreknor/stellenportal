<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Füge 'volunteer' nur auf MySQL/MariaDB zur ENUM-Liste hinzu.
        // SQLite unterstützt dieses ALTER/MODIFY-Statement nicht und benötigt im Testkontext keine strikte ENUM-Validierung.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE job_postings MODIFY COLUMN employment_type ENUM('full_time', 'part_time', 'mini_job', 'internship', 'apprenticeship', 'volunteer') NOT NULL DEFAULT 'full_time'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Entferne 'volunteer' nur auf MySQL/MariaDB aus der ENUM-Liste.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE job_postings MODIFY COLUMN employment_type ENUM('full_time', 'part_time', 'mini_job', 'internship', 'apprenticeship') NOT NULL DEFAULT 'full_time'");
        }
    }
};
