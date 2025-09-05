<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Http;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;
use App\Models\Post;
use App\Models\RallyEvent;
use Carbon\Carbon;

class BuildSitemap extends Command
{
    protected $signature = 'sitemaps:build';
    protected $description = 'Generate XML sitemaps';

    public function handle(): int
    {
        File::ensureDirectoryExists(public_path('sitemaps'));

        /** helper to safely write and log */
        $write = function (Sitemap $map, string $absPath, string $label, int $count): void {
            File::ensureDirectoryExists(dirname($absPath));
            $map->writeToFile($absPath);
            $this->info(sprintf('%s sitemap written: %s (%d urls)', $label, $absPath, $count));
        };

        // -------- 1) Static
        try {
            $staticCount = 0;
            $static = Sitemap::create();

            $static->add(Url::create(url('/'))->setLastModificationDate(now()));
            $staticCount++;

            $static->add(Url::create(url('/blog'))->setLastModificationDate(now()));
            $staticCount++;

            $static->add(Url::create(url('/shop'))->setLastModificationDate(now()));
            $staticCount++;

            $static->add(Url::create(url('/history'))->setLastModificationDate(now()));
            $staticCount++;

            $static->add(Url::create(url('/calendar'))->setLastModificationDate(now()));
            $staticCount++;

            $write($static, public_path('sitemaps/static.xml'), 'Static', $staticCount);
        } catch (\Throwable $e) {
            Log::warning('Sitemap static: ' . $e->getMessage());
        }

        $sectionFiles = [];

        // -------- 2) Blog (mirror your controller logic)
        try {
            if (Schema::hasTable('posts')) {
                $blog = Sitemap::create();
                $blogCount = 0;

                Post::query()
                    ->select(['slug', 'updated_at', 'published_at', 'status', 'publish_status', 'created_at'])
                    ->whereNotNull('slug')
                    ->where(function ($q) {
                        $q->where(function ($q) {
                            $q->where('status', 'published')
                              ->whereNotNull('published_at')
                              ->where('published_at', '<=', now());
                        })
                        ->orWhere(function ($q) {
                            $q->whereNull('status')
                              ->where('publish_status', 'published');
                        })
                        ->orWhere('status', 'approved');
                    })
                    ->orderByDesc('id')
                    ->chunk(500, function ($posts) use ($blog, &$blogCount) {
                        foreach ($posts as $p) {
                            if (!$p->slug) continue;
                            $lastmod = $p->published_at ?? $p->updated_at ?? $p->created_at ?? now();
                            $blog->add(
                                Url::create(route('blog.show', $p->slug))
                                   ->setLastModificationDate(Carbon::parse($lastmod))
                            );
                            $blogCount++;
                        }
                    });

                $blogPath = public_path('sitemaps/blog.xml');
                $blog->writeToFile($blogPath);
                $this->info("Blog sitemap written: {$blogPath} ({$blogCount} urls)");
                if ($blogCount > 0) $sectionFiles[] = $blogPath;
            } else {
                Log::info('Sitemap: posts table missing; skipped blog.');
            }
        } catch (\Throwable $e) {
            Log::warning('Sitemap blog: ' . $e->getMessage());
        }

        // -------- 3) Events
        try {
            if (Schema::hasTable('rally_events')) {
                $events = Sitemap::create();
                $eventsCount = 0;

                RallyEvent::query()
                    ->select(['id','slug','updated_at','created_at','starts_at'])
                    ->orderByDesc('updated_at')
                    ->chunk(500, function ($rows) use ($events, &$eventsCount) {
                        foreach ($rows as $e) {
                            $url = $e->slug
                                ? route('calendar.show', $e->slug)
                                : url('/calendar/' . $e->id);

                            $events->add(
                                Url::create($url)
                                   ->setLastModificationDate($e->updated_at ?? $e->starts_at ?? $e->created_at ?? now())
                            );
                            $eventsCount++;
                        }
                    });

                $eventsPath = public_path('sitemaps/events.xml');
                $events->writeToFile($eventsPath);
                $this->info("Events sitemap written: {$eventsPath} ({$eventsCount} urls)");
                if ($eventsCount > 0) $sectionFiles[] = $eventsPath;
            } else {
                Log::info('Sitemap: rally_events table missing; skipped events.');
            }
        } catch (\Throwable $e) {
            Log::warning('Sitemap events: ' . $e->getMessage());
        }

        // -------- 4) History (decade JSON – robust lookup)
        try {
            $history = Sitemap::create();
            $historyCount = 0;

            $candidates = array_merge(
                glob(public_path('data/*-*.json')) ?: [],
                glob(storage_path('app/public/data/*-*.json')) ?: []
            );

            foreach ($candidates as $path) {
                $filename = basename($path);
                if (!preg_match('/^(events|cars|drivers)-(\d{4})s\.json$/', $filename, $m)) {
                    continue;
                }
                [, $tab, $decade] = $m;

                $json = json_decode(File::get($path), true);
                if (!is_array($json)) {
                    Log::warning("Sitemap history: failed to decode {$filename}");
                    continue;
                }

                $lastmod = Carbon::createFromTimestamp(File::lastModified($path));

                foreach ($json as $row) {
                    if (!isset($row['id'])) continue;
                    $history->add(
                        Url::create(route('history.show', [
                            'tab'    => $tab,
                            'decade' => $decade,
                            'id'     => (int) $row['id'],
                        ]))->setLastModificationDate($lastmod)
                    );
                    $historyCount++;
                }
            }

            $historyPath = public_path('sitemaps/history.xml');
            $history->writeToFile($historyPath);
            $this->info("History sitemap written: {$historyPath} ({$historyCount} urls)");
            if ($historyCount > 0) $sectionFiles[] = $historyPath;
        } catch (\Throwable $e) {
            Log::warning('Sitemap history: ' . $e->getMessage());
        }

        // -------- 5) Index
        try {
            $index = SitemapIndex::create();
            foreach (['sitemaps/static.xml','sitemaps/blog.xml','sitemaps/events.xml','sitemaps/history.xml'] as $rel) {
                $abs = public_path($rel);
                if (is_file($abs) && filesize($abs) > 0) {
                    $index->add(url($rel));
                }
            }
            $index->writeToFile(public_path('sitemap.xml'));
            $this->info('Sitemap index written: ' . public_path('sitemap.xml'));
        } catch (\Throwable $e) {
            Log::warning('Sitemap index: ' . $e->getMessage());
        }

        // -------- 6) Ping search engines
        try {
            $indexUrl = urlencode(url('sitemap.xml'));
            Http::get("https://www.google.com/ping?sitemap={$indexUrl}");
            Http::get("https://www.bing.com/ping?sitemap={$indexUrl}");
            $this->info("Pinged Google & Bing with {$indexUrl}");
        } catch (\Throwable $e) {
            Log::warning('Sitemap ping failed: ' . $e->getMessage());
        }

        return self::SUCCESS;
    }
}