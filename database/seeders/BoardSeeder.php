<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Board;

class BoardSeeder extends Seeder
{
    public function run(): void
    {
        $boards = [
            ['name'=>'General Rally Chat',          'color'=>'slate'],
            ['name'=>'Event News & Rumors',         'color'=>'amber'],
            ['name'=>'WRC Live Threads',            'color'=>'red'],
            ['name'=>'Local & Regional Rallies',    'color'=>'green'],
            ['name'=>'Historic Rally Archive',      'color'=>'indigo'],
            ['name'=>'Tech, Builds & Setup',        'color'=>'orange'],
            ['name'=>'Sim & Games (RBR, DiRT, WRC)','color'=>'cyan'],
            ['name'=>'Photography & Media',         'color'=>'purple'],
            ['name'=>'Volunteering & Safety',       'color'=>'emerald'],
            ['name'=>'Site Feedback & Roadmap',     'color'=>'blue'],
        ];

        foreach ($boards as $i => $b) {
            Board::updateOrCreate(
                ['slug' => Str::slug($b['name'])],
                ['name' => $b['name'], 'position' => $i, 'color' => $b['color'], 'is_public' => true]
            );
        }
    }
}