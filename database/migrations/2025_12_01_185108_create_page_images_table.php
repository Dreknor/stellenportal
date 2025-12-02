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
        Schema::create('page_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->onDelete('cascade');
            $table->string('filename');
            $table->string('original_filename');
            $table->string('path');
            $table->integer('size')->comment('File size in bytes');
            $table->string('mime_type');
            $table->string('alt_text')->nullable();
            $table->string('title')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index('page_id');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_images');
    }
};
