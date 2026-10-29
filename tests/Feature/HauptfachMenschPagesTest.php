<?php

use Database\Seeders\HauptfachMenschSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;



it('rendert die Hauptfach-Mensch-Seiten öffentlich', function () {
    $this->seed(HauptfachMenschSeeder::class);
    $this->seed(HauptfachMenschSeeder::class); // idempotent

    foreach (['', '-lehrer', '-vorbereitungsdienst', '-studierende', '-schueler', '-faq'] as $suffix) {
        $this->get('/hauptfach-mensch'.$suffix)->assertOk()->assertSee('hm-hero', false);
    }
    expect(\App\Models\Page::where('slug', 'like', 'hauptfach-mensch%')->count())->toBe(6);
});
