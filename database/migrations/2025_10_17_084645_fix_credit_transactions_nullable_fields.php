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
        // Skip for SQLite (used in testing)
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        // Use Laravel's Schema Builder which handles differences between database systems
        Schema::table('credit_transactions', function (Blueprint $table) {
            $table->string('related_creditable_type')->nullable()->change();
            $table->unsignedBigInteger('related_creditable_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Skip for SQLite (used in testing)
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('credit_transactions', function (Blueprint $table) {
            $table->string('related_creditable_type')->nullable(false)->change();
            $table->unsignedBigInteger('related_creditable_id')->nullable(false)->change();
        });
    }
};
