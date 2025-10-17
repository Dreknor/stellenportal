<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('credit_transactions')){
            Schema::create('credit_transactions', function (Blueprint $table) {
                $table->id();
                $table->morphs('creditable');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('credit_package_id')->nullable()->constrained()->onDelete('set null');
                $table->enum('type', ['purchase', 'transfer_in', 'transfer_out', 'usage', 'adjustment']);
                $table->integer('amount');
                $table->integer('balance_after');
                $table->decimal('price_paid', 10, 2)->nullable();
                $table->text('note')->nullable();

                // Related creditable with custom index name
                $table->unsignedBigInteger('related_creditable_id')->nullable();
                $table->string('related_creditable_type')->nullable();
                $table->index(['related_creditable_type', 'related_creditable_id'], 'ct_related_creditable_idx');

                $table->foreignId('related_transaction_id')->nullable()->constrained('credit_transactions');
                $table->timestamps();

                $table->index(['creditable_type', 'creditable_id', 'created_at']);
            });

        }
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_transactions');
    }
};
