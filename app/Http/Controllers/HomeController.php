<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Models\Post;
use App\Models\RallyEvent;
use View;

class HomeController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->take(6)->get();

        // History spotlights (guaranteed non-null via placeholders)
        // Cache a bit so the home page isn't picking a new random on every hit
        $event  = Cache::remember('home.random_event',  now()->addMinutes(15), fn() => $this->pickRandomFromDecadeFiles('events'));
        $car    = Cache::remember('home.random_car',    now()->addMinutes(15), fn() => $this->pickRandomFromDecadeFiles('cars'));
        $driver = Cache::remember('home.random_driver', now()->addMinutes(15), fn() => $this->pickRandomFromDecadeFiles('drivers'));

        // Ensure all 3 cards are present (fallbacks if any are missing)
        $event  = $event  ?? $this->placeholder('events');
        $car    = $car    ?? $this->placeholder('cars');
        $driver = $driver ?? $this->placeholder('drivers');

        // Upcoming rallies panel
        $nextEvents = RallyEvent::query()
            ->whereDate('start_date', '>=', today())
            ->orderBy('start_date')
            ->take(6)
            ->select(['name as title', 'start_date', 'end_date', 'location', 'slug'])
            ->get();

        return view('home', compact('posts', 'event', 'car', 'driver', 'nextEvents'));
    }

    /**
     * Pick one random item for a type from any decade file that has data.
     * Scans: public/data/{type}-*s.json  (e.g., events-1980s.json, cars-1970s.json)
     */
    private function pickRandomFromDecadeFiles(string $type): ?array
    {
        $pattern = public_path("data/{$type}-*s.json");
        $files   = glob($pattern);

        if (!$files) {
            return null;
        }

        // Shuffle so we don't always read the same decade first
        shuffle($files);

        foreach ($files as $file) {
            $items = json_decode(File::get($file), true);

            if (!is_array($items) || empty($items)) {
                continue;
            }

            // Clean/normalize and pick one random item
            $item = Arr::first(Arr::shuffle($items));

            if (!is_array($item) || empty($item)) {
                continue;
            }

            // Derive {decade} from file name, e.g. "events-1980s.json" -> 1980
            $base = pathinfo($file, PATHINFO_FILENAME);
            if (preg_match('/(\d{4})s$/', $base, $m)) {
                $item['decade'] = (int) $m[1];
            }

            // Guarantee a minimal shape the Blade expects
            if ($type === 'events') {
                $item['title'] = $item['title'] ?? ($item['name'] ?? 'Untitled Event');
            } else {
                $item['name']  = $item['name']  ?? 'Unnamed';
            }
            $item['bio'] = $item['bio'] ?? ($item['description'] ?? null);

            // Ensure an id if available
            $item['id'] = $item['id'] ?? ($item['slug'] ?? Str::uuid()->toString());

            return $item;
        }

        return null; // none of the files had usable items
    }

    /**
     * Friendly placeholders so the UI always has 3 cards.
     */
    private function placeholder(string $type): array
    {
        return match ($type) {
            'events'  => [
                'id'     => 'placeholder-event',
                'title'  => 'More Events Coming Soon',
                'bio'    => 'We’re adding more legendary rallies. Check back shortly.',
                'decade' => 0,
            ],
            'cars'    => [
                'id'     => 'placeholder-car',
                'name'   => 'Iconic Cars Incoming',
                'bio'    => 'Group 4 to WRC monsters—new car profiles are in the works.',
                'decade' => 0,
            ],
            'drivers' => [
                'id'     => 'placeholder-driver',
                'name'   => 'Driver Profiles Loading',
                'bio'    => 'Champions and cult heroes—driver spotlights are on the way.',
                'decade' => 0,
            ],
            default   => [
                'id'     => 'placeholder',
                'name'   => 'Coming soon',
                'bio'    => 'New content will appear here.',
                'decade' => 0,
            ],
        };
    }
}