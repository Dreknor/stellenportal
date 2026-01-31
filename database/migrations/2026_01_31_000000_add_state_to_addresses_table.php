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
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('state')->nullable()->after('city');
        });

        // Set default state to "Sachsen" for existing addresses if they don't have one
        DB::table('addresses')->whereNull('state')->update(['state' => 'Sachsen']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('state');
        });
    }
};
