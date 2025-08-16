<?php

namespace Database\Seeders;

use App\Models\Board;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BoardSeeder extends Seeder
{
    public function run(): void
    {
        $boards = [
            ['name' => 'General Rally Chat',         'icon' => 'message-circle', 'color' => 'slate'],
            ['name' => 'Event News & Rumors',        'icon' => 'megaphone',      'color' => 'amber'],
            ['name' => 'WRC Live Threads',           'icon' => 'radio',          'color' => 'red'],
            ['name' => 'Local & Regional Rallies',   'icon' => 'map-pin',        'color' => 'green'],
            ['name' => 'Historic Rally Archive',     'icon' => 'library',        'color' => 'indigo'],
            ['name' => 'Tech, Builds & Setup',       'icon' => 'wrench',         'color' => 'orange'],
            ['name' => 'Sim & Games (RBR, DiRT, WRC)','icon' => 'gamepad-2',     'color' => 'cyan'],
            ['name' => 'Photography & Media',        'icon' => 'camera',         'color' => 'purple'],
            ['name' => 'Volunteering & Safety',      'icon' => 'shield',         'color' => 'emerald'],
            ['name' => 'Site Feedback & Roadmap',    'icon' => 'compass',        'color' => 'blue'],
        ];

        foreach ($boards as $i => $b) {
            Board::updateOrCreate(
                ['name' => $b['name']],
                ['slug' => Str::slug($b['name']), 'icon' => $b['icon'], 'color' => $b['color'], 'position' => $i]
            );
        }
    }
}