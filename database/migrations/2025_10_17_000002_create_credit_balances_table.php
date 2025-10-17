<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_balances', function (Blueprint $table) {
            $table->id();
            $table->morphs('creditable');
            $table->integer('balance')->default(0);
            $table->timestamps();

            $table->unique(['creditable_id', 'creditable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_balances');
    }
};

