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
        Schema::table('credit_packages', function (Blueprint $table) {
            $table->integer('purchase_limit_per_organization')->nullable()->after('for_cooperative_members')
                ->comment('Maximale Anzahl der Käufe dieses Pakets pro Träger (null = unbegrenzt)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_packages', function (Blueprint $table) {
            $table->dropColumn('purchase_limit_per_organization');
        });
    }
};

