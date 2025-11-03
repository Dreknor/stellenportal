<?php

namespace Database\Seeders;

use App\Models\FooterSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FooterSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FooterSetting::create([
            'content' => '© ' . date('Y') . ' ' . config('app.name') . '. Alle Rechte vorbehalten.',
            'links' => [
                [
                    'title' => 'Impressum',
                    'url' => url('/impressum'),
                ],
                [
                    'title' => 'Datenschutz',
                    'url' => url('/datenschutz'),
                ],
                [
                    'title' => 'Kontakt',
                    'url' => url('/kontakt'),
                ],
            ],
            'background_color' => '#ffffff',
            'text_color' => '#6b7280',
            'link_color' => '#2563eb',
            'is_active' => true,
        ]);
    }
}

