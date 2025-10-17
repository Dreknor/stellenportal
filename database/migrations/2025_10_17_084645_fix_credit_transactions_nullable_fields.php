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
        // Use raw SQL to ensure the columns are properly set to nullable
        DB::statement('ALTER TABLE `credit_transactions` MODIFY `related_creditable_type` VARCHAR(255) NULL');
        DB::statement('ALTER TABLE `credit_transactions` MODIFY `related_creditable_id` BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE `credit_transactions` MODIFY `related_creditable_type` VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE `credit_transactions` MODIFY `related_creditable_id` BIGINT UNSIGNED NOT NULL');
    }
};
