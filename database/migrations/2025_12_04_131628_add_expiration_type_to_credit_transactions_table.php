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
        // SQLite doesn't support MODIFY COLUMN, so we just skip for SQLite (type is already string)
        if (DB::getDriverName() !== 'sqlite') {
            // Ändern des ENUM-Typs um 'expiration' hinzuzufügen
            DB::statement("ALTER TABLE credit_transactions MODIFY COLUMN type ENUM('purchase', 'transfer_in', 'transfer_out', 'usage', 'adjustment', 'expiration') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // SQLite doesn't support MODIFY COLUMN, so we just skip for SQLite
        if (DB::getDriverName() !== 'sqlite') {
            // Entfernen von 'expiration' aus dem ENUM
            DB::statement("ALTER TABLE credit_transactions MODIFY COLUMN type ENUM('purchase', 'transfer_in', 'transfer_out', 'usage', 'adjustment') NOT NULL");
        }
    }
};

