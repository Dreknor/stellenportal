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
        Schema::create('search_queries', function (Blueprint $table) {
            $table->id();
            $table->string('query'); // Der Suchbegriff
            $table->string('location')->nullable(); // Standortsuche (optional)
            $table->integer('radius')->nullable(); // Suchradius in km
            $table->string('employment_type')->nullable(); // Beschäftigungsart-Filter
            $table->integer('results_count')->default(0); // Anzahl der Ergebnisse
            $table->ipAddress('ip_address')->nullable(); // IP-Adresse des Suchenden
            $table->text('user_agent')->nullable(); // Browser/Device Info
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Falls eingeloggt
            $table->timestamps();

            // Indizes für Performance
            $table->index('query');
            $table->index('location');
            $table->index('created_at');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_queries');
    }
};

