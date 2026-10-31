<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_lots', function (Blueprint $table) {
            $table->id();
            $table->morphs('creditable');
            $table->foreignId('purchase_transaction_id')->nullable()->constrained('credit_transactions')->nullOnDelete();
            $table->integer('amount');
            $table->integer('remaining');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['creditable_type', 'creditable_id', 'remaining'], 'cl_creditable_remaining_idx');
            $table->index(['remaining', 'expires_at'], 'cl_remaining_expires_idx');
        });

        $this->backfillLots();
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_lots');
    }

    /**
     * Distribute each existing balance over the entity's incoming credits,
     * newest first (older credits are assumed to have been used first).
     * Whatever cannot be attributed becomes a lot without expiry.
     */
    private function backfillLots(): void
    {
        $now = now();
        $expiredPurchaseIds = DB::table('credit_transactions')
            ->where('type', 'expiration')
            ->whereNotNull('related_transaction_id')
            ->pluck('related_transaction_id')
            ->flip();

        $balances = DB::table('credit_balances')->where('balance', '>', 0)->get();

        foreach ($balances as $balance) {
            $rest = (int) $balance->balance;

            $incoming = DB::table('credit_transactions')
                ->where('creditable_type', $balance->creditable_type)
                ->where('creditable_id', $balance->creditable_id)
                ->whereIn('type', ['purchase', 'transfer_in', 'adjustment'])
                ->where('amount', '>', 0)
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->get();

            foreach ($incoming as $transaction) {
                if ($rest <= 0) {
                    break;
                }

                if ($transaction->type === 'purchase' && $expiredPurchaseIds->has($transaction->id)) {
                    continue;
                }

                $expiresAt = match ($transaction->type) {
                    'purchase' => $transaction->expires_at,
                    // Transferred credits were bought at the latest on the transfer date
                    'transfer_in' => Carbon::parse($transaction->created_at)->addYears(3),
                    default => null,
                };

                $amount = min((int) $transaction->amount, $rest);
                $rest -= $amount;

                DB::table('credit_lots')->insert([
                    'creditable_type' => $balance->creditable_type,
                    'creditable_id' => $balance->creditable_id,
                    'purchase_transaction_id' => $transaction->type === 'purchase' ? $transaction->id : null,
                    'amount' => $amount,
                    'remaining' => $amount,
                    'expires_at' => $expiresAt,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            if ($rest > 0) {
                DB::table('credit_lots')->insert([
                    'creditable_type' => $balance->creditable_type,
                    'creditable_id' => $balance->creditable_id,
                    'purchase_transaction_id' => null,
                    'amount' => $rest,
                    'remaining' => $rest,
                    'expires_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
};
