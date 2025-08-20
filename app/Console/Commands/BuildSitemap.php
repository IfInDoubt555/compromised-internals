<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;
use App\Models\Post;        // adjust if different
use App\Models\RallyEvent;  // adjust if different
use Carbon\Carbon;

class BuildSitemap extends Command
{
    protected $signature = 'sitemaps:build';
    protected $description = 'Generate XML sitemaps';

    public function handle(): int
    {
        File::ensureDirectoryExists(public_path('sitemaps'));

        // ---- 1) Static pages
        try {
            $static = Sitemap::create()
                ->add(Url::create(url('/'))->setLastModificationDate(now()))
                ->add(Url::create(url('/blog'))->setLastModificationDate(now()))
                ->add(Url::create(url('/shop'))->setLastModificationDate(now()))
                ->add(Url::create(url('/history'))->setLastModificationDate(now()))
                ->add(Url::create(url('/calendar'))->setLastModificationDate(now()));

            $static->writeToFile(public_path('sitemaps/static.xml'));
        } catch (\Throwable $e) {
            Log::warning('Sitemap static section skipped: ' . $e->getMessage());
        }

        // Track which section files we actually produced
        $sectionFiles = [];

        // ---- 2) Blog posts (defensive about schema)
        try {
            if (Schema::hasTable('posts')) {
                $blog = Sitemap::create();

                $query = Post::query();

                if (Schema::hasColumn('posts', 'is_published')) {
                    $query->where('is_published', 1);
                } elseif (Schema::hasColumn('posts', 'published_at')) {
                    $query->whereNotNull('published_at');
                } elseif (Schema::hasColumn('posts', 'status')) {
                    $query->where('status', 'published');
                }

                $query->orderByDesc('updated_at')
                    ->chunk(500, function ($posts) use ($blog) {
                        foreach ($posts as $p) {
                            $blog->add(
                                Url::create(route('blog.show', $p->slug))
                                   ->setLastModificationDate($p->updated_at ?? $p->created_at ?? now())
                            );
                        }
                    });

                $blogPath = public_path('sitemaps/blog.xml');
                $blog->writeToFile($blogPath);
                $sectionFiles[] = $blogPath;
            } else {
                Log::info('Sitemap: posts table not found; skipping blog section.');
            }
        } catch (\Throwable $e) {
            Log::warning('Sitemap blog section skipped: ' . $e->getMessage());
        }

        // ---- 3) Calendar events
        try {
            // Adjust table name if yours differs
            if (Schema::hasTable('rally_events')) {
                $events = Sitemap::create();

                RallyEvent::query()
                    ->orderByDesc('updated_at')
                    ->chunk(500, function ($rows) use ($events) {
                        foreach ($rows as $e) {
                            // Use the correct route/param (slug vs id)
                            $url = isset($e->slug)
                                ? route('calendar.show', $e->slug)
                                : url('/calendar/' . $e->id);

                            $events->add(
                                Url::create($url)->setLastModificationDate($e->updated_at ?? $e->created_at ?? now())
                            );
                        }
                    });

                $eventsPath = public_path('sitemaps/events.xml');
                $events->writeToFile($eventsPath);
                $sectionFiles[] = $eventsPath;
            } else {
                Log::info('Sitemap: rally_events table not found; skipping events section.');
            }
        } catch (\Throwable $e) {
            Log::warning('Sitemap events section skipped: ' . $e->getMessage());
        }

        // ---- 4) History detail pages from JSON (events/cars/drivers by decade)
        try {
            $history = Sitemap::create();
            $jsonFiles = glob(public_path('data/*-*.json')) ?: [];

            foreach ($jsonFiles as $path) {
                $filename = basename($path); // e.g., events-1960s.json
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
                    if (!isset($row['id'])) {
                        continue;
                    }
                    $history->add(
                        Url::create(route('history.show', [
                            'tab'    => $tab,
                            'decade' => $decade,
                            'id'     => (int) $row['id'],
                        ]))->setLastModificationDate($lastmod)
                    );
                }
            }

            // Only write file if we actually added URLs
            $historyPath = public_path('sitemaps/history.xml');
            $history->writeToFile($historyPath);
            $sectionFiles[] = $historyPath;
        } catch (\Throwable $e) {
            Log::warning('Sitemap history section skipped: ' . $e->getMessage());
        }

        // ---- 5) Sitemap index (include only existing section files)
        try {
            $index = SitemapIndex::create();

            foreach (['sitemaps/static.xml', 'sitemaps/blog.xml', 'sitemaps/events.xml', 'sitemaps/history.xml'] as $rel) {
                $abs = public_path($rel);
                if (is_file($abs) && filesize($abs) > 0) {
                    $index->add(url($rel));
                }
            }

            $index->writeToFile(public_path('sitemap.xml'));
        } catch (\Throwable $e) {
            Log::warning('Sitemap index generation failed: ' . $e->getMessage());
        }

        $this->info('Sitemaps generated.');
        return self::SUCCESS;
    }
}