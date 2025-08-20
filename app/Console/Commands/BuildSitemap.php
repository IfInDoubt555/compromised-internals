<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;
use App\Models\Post;        // adjust if different
use App\Models\RallyEvent;  // adjust if different
use Illuminate\Support\Facades\Schema;

class BuildSitemap extends Command
{
    protected $signature = 'sitemaps:build';
    protected $description = 'Generate XML sitemaps';

    public function handle(): int
    {
        // 1) Static pages
        $static = Sitemap::create()
            ->add(Url::create(url('/'))->setLastModificationDate(now()))
            ->add(Url::create(url('/blog'))->setLastModificationDate(now()))
            ->add(Url::create(url('/shop'))->setLastModificationDate(now()))
            ->add(Url::create(url('/history'))->setLastModificationDate(now()))
            ->add(Url::create(url('/calendar'))->setLastModificationDate(now()));
        File::ensureDirectoryExists(public_path('sitemaps'));
        $static->writeToFile(public_path('sitemaps/static.xml'));

        // 2) Blog posts (published only)
        $blog = Sitemap::create();
        Post::query()->where('is_published', true)->orderByDesc('updated_at')
            ->chunk(500, function ($posts) use ($blog) {
                foreach ($posts as $p) {
                    $blog->add(
                        Url::create(route('blog.show', $p->slug))
                           ->setLastModificationDate($p->updated_at ?? $p->created_at ?? now())
                    );
                }
            });
        $blog = Sitemap::create();

        if (Schema::hasTable('posts')) {
            $query = \App\Models\Post::query();
        
            if (Schema::hasColumn('posts', 'is_published')) {
                $query->where('is_published', 1);
            } elseif (Schema::hasColumn('posts', 'published_at')) {
                $query->whereNotNull('published_at');
            } elseif (Schema::hasColumn('posts', 'status')) {
                $query->where('status', 'published');
            } // else: include all posts
        
            $query->orderByDesc('updated_at')
                ->chunk(500, function ($posts) use ($blog) {
                    foreach ($posts as $p) {
                        $blog->add(
                            \Spatie\Sitemap\Tags\Url::create(route('blog.show',$p->slug))
                                ->setLastModificationDate($p->updated_at ?? $p->created_at ?? now())
                        );
                    }
                });
        }

        $blog->writeToFile(public_path('sitemaps/blog.xml'));

        // 4) History detail pages (JSON → routes like history.show)
        $history = Sitemap::create();

        // patterns: events-1960s.json, cars-2010s.json, drivers-1990s.json, etc.
        foreach (glob(public_path('data/*-*.json')) as $path) {
            $filename = basename($path);                 // e.g., events-1960s.json
            if (!preg_match('/^(events|cars|drivers)-(\d{4})s\.json$/', $filename, $m)) {
                continue;
            }
            [$full, $tab, $decade] = $m;
            $json = json_decode(File::get($path), true);
            if (!is_array($json)) continue;

            $lastmod = \Carbon\Carbon::createFromTimestamp(File::lastModified($path));
            foreach ($json as $row) {
                if (!isset($row['id'])) continue;
                $history->add(
                    Url::create(route('history.show', [
                        'tab'    => $tab,
                        'decade' => $decade,
                        'id'     => (int) $row['id'],
                    ]))->setLastModificationDate($lastmod)
                );
            }
        }

        $history->writeToFile(public_path('sitemaps/history.xml'));

        // 5) Sitemap index
        SitemapIndex::create()
            ->add(url('sitemaps/static.xml'))
            ->add(url('sitemaps/blog.xml'))
            ->add(url('sitemaps/events.xml'))
            ->add(url('sitemaps/history.xml'))
            ->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemaps generated.');
        return self::SUCCESS;
    }
}