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
        Schema::create('job_posting_credit_exemptions', function (Blueprint $table) {
            $table->id();
            $table->enum('employment_type', ['full_time', 'part_time', 'mini_job', 'internship', 'apprenticeship', 'volunteer']);
            $table->enum('applies_to', ['all', 'cooperative_members_only'])->default('all');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Ensure unique combination of employment_type and applies_to
            $table->unique(['employment_type', 'applies_to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_posting_credit_exemptions');
    }
};
