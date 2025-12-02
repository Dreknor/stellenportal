<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Erstelle Test-Menu-Items wenn noch keine existieren
        if (DB::table('menu_items')->where('menu_location', 'header')->count() === 0) {
            // Prüfe ob es veröffentlichte Seiten gibt
            $page = DB::table('pages')->where('is_published', true)->first();

            if ($page) {
                // Erstelle Menü-Item mit Seite
                DB::table('menu_items')->insert([
                    'menu_location' => 'header',
                    'label' => 'Test Seite',
                    'page_id' => $page->id,
                    'target' => '_self',
                    'order' => 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Erstelle externes Link Beispiel
            DB::table('menu_items')->insert([
                'menu_location' => 'header',
                'label' => 'Beispiel Link',
                'url' => 'https://example.com',
                'target' => '_blank',
                'order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optional: Lösche Test-Einträge
        DB::table('menu_items')
            ->where('menu_location', 'header')
            ->whereIn('label', ['Test Seite', 'Beispiel Link'])
            ->delete();
    }
};

