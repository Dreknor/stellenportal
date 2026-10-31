<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->text('geocoding_error')->nullable()->after('longitude');
            $table->timestamp('geocoding_failed_at')->nullable()->after('geocoding_error');
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn(['geocoding_error', 'geocoding_failed_at']);
        });
    }
};
