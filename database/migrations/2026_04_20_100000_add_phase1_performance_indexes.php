<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Ergänzt Indizes, die für häufige Queries fehlen:
     *  - `organizations.is_approved` wird in JobPostingController::create via
     *    whereHas gefiltert und in Dashboard-Widgets genutzt.
     *  - `credit_transactions.expires_at` wird vom ExpireCredits-Cron und
     *    vom `expiredPurchases()`-Scope benötigt.
     */
    public function up(): void
    {
        if (Schema::hasTable('organizations') && Schema::hasColumn('organizations', 'is_approved')) {
            Schema::table('organizations', function (Blueprint $table) {
                $table->index('is_approved', 'organizations_is_approved_idx');
            });
        }

        if (Schema::hasTable('credit_transactions') && Schema::hasColumn('credit_transactions', 'expires_at')) {
            Schema::table('credit_transactions', function (Blueprint $table) {
                $table->index('expires_at', 'ct_expires_at_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('organizations')) {
            Schema::table('organizations', function (Blueprint $table) {
                $table->dropIndex('organizations_is_approved_idx');
            });
        }

        if (Schema::hasTable('credit_transactions')) {
            Schema::table('credit_transactions', function (Blueprint $table) {
                $table->dropIndex('ct_expires_at_idx');
            });
        }
    }
};

