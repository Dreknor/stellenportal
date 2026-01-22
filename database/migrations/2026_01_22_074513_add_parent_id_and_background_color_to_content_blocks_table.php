<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            Schema::table('content_blocks', function (Blueprint $table) {
                // Allow blocks to be nested within other blocks (e.g., row container)
                $table->foreignId('parent_id')->nullable()->after('page_id')->constrained('content_blocks')->onDelete('cascade');

                // Custom background color for row/container blocks
                $table->string('background_color', 20)->nullable()->after('settings');

                // Index for better query performance
                $table->index('parent_id');
            });
        } catch (\Exception $e) {
            Log::error('Migration Error: ' . $e->getMessage());
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_blocks', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['parent_id']);
            $table->dropColumn(['parent_id', 'background_color']);
        });
    }
};
