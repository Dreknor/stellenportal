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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('menu_location')->default('header')->comment('header, footer, etc.');
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->onDelete('cascade');
            $table->foreignId('page_id')->nullable()->constrained('pages')->onDelete('cascade');
            $table->string('label');
            $table->string('url')->nullable()->comment('For external links');
            $table->enum('target', ['_self', '_blank'])->default('_self');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('css_class')->nullable();
            $table->string('icon')->nullable();
            $table->timestamps();

            $table->index(['menu_location', 'order']);
            $table->index('parent_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
