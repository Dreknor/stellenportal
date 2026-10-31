<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Remove password hashes and remember tokens that were audited before
     * User::$auditExclude existed.
     */
    public function up(): void
    {
        $table = config('audit.drivers.database.table', 'audits');

        DB::table($table)
            ->where('auditable_type', \App\Models\User::class)
            ->orderBy('id')
            ->each(function ($audit) use ($table) {
                $changes = [];

                foreach (['old_values', 'new_values'] as $column) {
                    $values = json_decode((string) $audit->{$column}, true);

                    if (is_array($values) && (isset($values['password']) || isset($values['remember_token']))) {
                        unset($values['password'], $values['remember_token']);
                        $changes[$column] = json_encode($values);
                    }
                }

                if ($changes) {
                    DB::table($table)->where('id', $audit->id)->update($changes);
                }
            });
    }

    public function down(): void
    {
        // Removed credentials cannot be restored
    }
};
