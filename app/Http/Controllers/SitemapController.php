<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;
use Illuminate\Support\Carbon;
use App\Models\Post;
// Use your real calendar model class here
use App\Models\RallyEvent;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    private int $ttl = 60 * 60 * 24; // 24h

    public function index(): Response
    {
        $xml = Cache::remember('sitemap:index', $this->ttl, function () {
            return SitemapIndex::create()
                ->add(route('sitemap.static'))
                ->add(route('sitemap.blog'))
                ->add(route('sitemap.calendar'))
                ->add(route('sitemap.history'))
                ->render();
        });

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    public function static(): Response
    {
        $xml = Cache::remember('sitemap:static', $this->ttl, function () {
            return Sitemap::create()
                ->add(Url::create(url('/'))->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)->setPriority(1.0))
                ->add(Url::create(url('/blog'))->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)->setPriority(0.9))
                ->add(Url::create(url('/history'))->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)->setPriority(0.8))
                ->add(Url::create(url('/calendar'))->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)->setPriority(0.9))
                ->add(Url::create(url('/shop'))->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)->setPriority(0.6))
                ->render();
        });

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    public function blog(): Response
    {
        $xml = Cache::remember('sitemap:blog', $this->ttl, function () {
            $sm = Sitemap::create();

            Post::query()
                ->select(['slug','updated_at','published_at','created_at','status','publish_status'])
                ->where(function ($q) {
                    $q->where(function ($q) {
                        $q->where('status','published')
                          ->whereNotNull('published_at')
                          ->where('published_at','<=',now());
                    })
                    ->orWhere(function ($q) {
                        $q->whereNull('status')->where('publish_status','published');
                    })
                    ->orWhere('status','approved');
                })
                ->orderByDesc('id')
                ->chunk(500, function ($posts) use ($sm) {
                    foreach ($posts as $p) {
                        $lastmod = $p->published_at ?? $p->updated_at ?? $p->created_at;
                        $sm->add(
                            Url::create(route('blog.show', $p->slug))
                                ->setLastModificationDate(Carbon::parse($lastmod))
                                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                                ->setPriority(0.7)
                        );
                    }
                });

            return $sm->render();
        });

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    public function calendar(): Response
    {
        $xml = Cache::remember('sitemap:calendar', $this->ttl, function () {
            $sm = Sitemap::create();

            RallyEvent::query()
                ->select(['slug','updated_at','starts_at','created_at'])
                ->orderByDesc('starts_at')
                ->chunk(500, function ($events) use ($sm) {
                    foreach ($events as $e) {
                        $lastmod = $e->updated_at ?? $e->starts_at ?? $e->created_at;
                        $sm->add(
                            Url::create(url('/calendar/'.$e->slug))
                                ->setLastModificationDate(Carbon::parse($lastmod))
                                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                                ->setPriority(0.8)
                        );
                    }
                });

            return $sm->render();
        });

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    public function history(): Response
    {
        $xml = Cache::remember('sitemap:history', $this->ttl, function () {
            $sm = Sitemap::create();

            // Master JSON (adjust path if yours differs)
            $path = storage_path('app/rally-history.json');
            if (is_file($path)) {
                $data = json_decode(file_get_contents($path), true);

                // Events
                if (!empty($data['events']) && is_array($data['events'])) {
                    foreach ($data['events'] as $ev) {
                        if (!isset($ev['year'],$ev['id'])) continue;
                        $sm->add(
                            Url::create(url("/history/events/{$ev['year']}/{$ev['id']}"))
                               ->setLastModificationDate(now()->subDays(3))
                               ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                               ->setPriority(0.6)
                        );
                    }
                }

                // Drivers (optional—comment out if routes differ)
                if (!empty($data['drivers']) && is_array($data['drivers'])) {
                    foreach ($data['drivers'] as $d) {
                        if (!isset($d['decade'],$d['id'])) continue;
                        $sm->add(
                            Url::create(url("/history/drivers/{$d['decade']}/{$d['id']}"))
                               ->setLastModificationDate(now()->subDays(5))
                               ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                               ->setPriority(0.5)
                        );
                    }
                }

                // Cars (optional)
                if (!empty($data['cars']) && is_array($data['cars'])) {
                    foreach ($data['cars'] as $c) {
                        if (!isset($c['decade'],$c['id'])) continue;
                        $sm->add(
                            Url::create(url("/history/cars/{$c['decade']}/{$c['id']}"))
                               ->setLastModificationDate(now()->subDays(7))
                               ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                               ->setPriority(0.5)
                        );
                    }
                }
            }

            return $sm->render();
        });

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}