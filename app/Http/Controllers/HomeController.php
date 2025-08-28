<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\Post;
use App\Models\RallyEvent; // ⬅️ use your RallyEvent model

class HomeController extends Controller
{
    public function index()
    {
        // Blog: grab 7 (1 featured + 6 list), eager-load author for the byline
        $posts = Post::query()
            ->with('user')
            ->latest()
            ->take(7)
            ->get();

        // History spotlights (random from any decade file you have)
        $event  = $this->getRandomFromDecadeFile('events');
        $car    = $this->getRandomFromDecadeFile('cars');
        $driver = $this->getRandomFromDecadeFile('drivers');

        // Next rallies (from RallyEvent)
        // Alias "name" -> "title" so the Blade can use $e->title seamlessly
        $nextEvents = RallyEvent::query()
            ->whereDate('start_date', '>=', today())
            ->orderBy('start_date')
            ->take(6)
            ->select([
                'name as title',
                'start_date',
                'end_date',
                'location',
                'slug',
            ])
            ->get();

        return view('home', compact('posts', 'event', 'car', 'driver', 'nextEvents'));
    }

    public function history()
    {
        return view('history.index');
    }

    public function showHistoryEvent($decade, $event)
    {
        $jsonPath = public_path('data/rally-history.json');

        if (!File::exists($jsonPath)) {
            abort(500, 'History data file not found.');
        }

        $data = json_decode(file_get_contents($jsonPath), true);
        $eventData = collect($data[$decade] ?? [])->firstWhere('id', $event);

        if (!$eventData) {
            abort(404);
        }

        return view('history.show', [
            'event'  => $eventData,
            'decade' => $decade,
        ]);
    }

    /**
     * Pick a random item from any decade JSON file for the given type.
     * Adds 'decade' to the returned array for routing.
     *
     * @param string $type 'events' | 'cars' | 'drivers'
     * @return array|null
     */
    private function getRandomFromDecadeFile($type)
    {
        $dataDir = public_path('data');
        $files = glob("{$dataDir}/{$type}-*s.json"); // e.g., events-1960s.json

        if (empty($files)) {
            return null;
        }

        $file = $files[array_rand($files)];
        $basename = pathinfo($file, PATHINFO_FILENAME);

        // Extract decade (e.g., 1960 from "events-1960s")
        preg_match('/(\d{4})s$/', $basename, $matches);
        $decade = $matches[1] ?? null;

        $data = json_decode(file_get_contents($file), true);
        if (!is_array($data) || empty($data)) {
            return null;
        }

        $item = $data[array_rand($data)];
        $item['decade'] = $decade;

        return $item;
    }
}