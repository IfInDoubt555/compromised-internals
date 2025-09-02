<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Models\Post;
use App\Models\RallyEvent;

class HomeController extends Controller
{
    public function index()
    {
        // Latest posts for the homepage carousel
        $latestPosts = Post::published()
            ->with(['user:id,name,profile_picture', 'board:id,name,slug'])
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->limit(6)
            ->get();

        // Back-compat if the view still expects $posts
        $posts = $latestPosts;

        // History spotlights (cached)
        $event  = Cache::remember('home.random_event',  now()->addMinutes(15), fn() => $this->pickRandomFromDecadeFiles('events')) ?? $this->placeholder('events');
        $car    = Cache::remember('home.random_car',    now()->addMinutes(15), fn() => $this->pickRandomFromDecadeFiles('cars'))   ?? $this->placeholder('cars');
        $driver = Cache::remember('home.random_driver', now()->addMinutes(15), fn() => $this->pickRandomFromDecadeFiles('drivers'))?? $this->placeholder('drivers');

        // Upcoming rallies panel
        $nextEvents = RallyEvent::query()
            ->whereDate('start_date', '>=', today())
            ->orderBy('start_date')
            ->take(6)
            ->select(['name as title', 'start_date', 'end_date', 'location', 'slug'])
            ->get();

        return view('home', compact('latestPosts', 'posts', 'event', 'car', 'driver', 'nextEvents'));
    }

    private function pickRandomFromDecadeFiles(string $type): ?array
    {
        $pattern = public_path("data/{$type}-*s.json");
        $files   = glob($pattern);
        if (!$files) return null;

        shuffle($files);

        foreach ($files as $file) {
            $items = json_decode(File::get($file), true);
            if (!is_array($items) || empty($items)) continue;

            $item = Arr::first(Arr::shuffle($items));
            if (!is_array($item) || empty($item)) continue;

            $base = pathinfo($file, PATHINFO_FILENAME);
            if (preg_match('/(\d{4})s$/', $base, $m)) $item['decade'] = (int) $m[1];

            if ($type === 'events') {
                $item['title'] = $item['title'] ?? ($item['name'] ?? 'Untitled Event');
            } else {
                $item['name']  = $item['name']  ?? 'Unnamed';
            }
            $item['bio'] = $item['bio'] ?? ($item['description'] ?? null);
            $item['id']  = $item['id']  ?? ($item['slug'] ?? Str::uuid()->toString());

            return $item;
        }
        return null;
    }

    private function placeholder(string $type): array
    {
        return match ($type) {
            'events'  => ['id'=>'placeholder-event','title'=>'More Events Coming Soon','bio'=>'We’re adding more legendary rallies. Check back shortly.','decade'=>0],
            'cars'    => ['id'=>'placeholder-car','name'=>'Iconic Cars Incoming','bio'=>'Group 4 to WRC monsters—new car profiles are in the works.','decade'=>0],
            'drivers' => ['id'=>'placeholder-driver','name'=>'Driver Profiles Loading','bio'=>'Champions and cult heroes—driver spotlights are on the way.','decade'=>0],
            default   => ['id'=>'placeholder','name'=>'Coming soon','bio'=>'New content will appear here.','decade'=>0],
        };
    }
}