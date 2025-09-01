<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SitemapWarmCommand extends Command
{
    protected $signature = 'sitemap:warm';
    protected $description = 'Warm sitemap cache and ping search engines';

    public function handle(): int
    {
        $urls = [
            route('sitemap.index'),
            route('sitemap.static'),
            route('sitemap.blog'),
            route('sitemap.calendar'),
            route('sitemap.history'),
            route('sitemap.drivers'),
            route('sitemap.cars'),
        ];

        foreach ($urls as $u) {
            file_get_contents($u); // warm cache
            $this->info("Warmed: {$u}");
        }

        // Ping Google & Bing
        $indexUrl = urlencode(route('sitemap.index'));
        @Http::get("https://www.google.com/ping?sitemap={$indexUrl}");
        @Http::get("https://www.bing.com/ping?sitemap={$indexUrl}");
        $this->info('Pinged Google & Bing');

        return self::SUCCESS;
    }
}