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
        Schema::table('footer_settings', function (Blueprint $table) {
            $table->string('background_color')->default('#ffffff')->after('links');
            $table->string('text_color')->default('#6b7280')->after('background_color');
            $table->string('link_color')->default('#2563eb')->after('text_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('footer_settings', function (Blueprint $table) {
            $table->dropColumn(['background_color', 'text_color', 'link_color']);
        });
    }
};

