<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\Post;

class HomeController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->take(3)->get();

        // Define paths to all available decades
        $decades = range(1960, 1990, 10);
        $event = $car = $driver = null;

        // Helper to load random item from first non-empty file
        $loadRandom = function ($type) use ($decades) {
            foreach ($decades as $decade) {
                $file = public_path("data/{$type}-{$decade}s.json");
                if (File::exists($file)) {
                    $items = json_decode(file_get_contents($file), true);
                    if (is_array($items) && count($items)) {
                        $item = collect($items)->random();
                        $item['decade'] = $decade;
                        return $item;
                    }
                }
            }
            return null;
        };

        $event = $loadRandom('events');
        $car = $loadRandom('cars');
        $driver = $loadRandom('drivers');

        return view('home', compact('posts', 'event', 'car', 'driver'));
    }

    public function history()
    {
        return view('history');
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
            'event' => $eventData,
            'decade' => $decade,
        ]);
    }

    private function getRandomFromDecadeFile($type)
    {
        $dataDir = public_path('data');
        $files = glob("{$dataDir}/{$type}-*s.json"); // e.g., cars-1980s.json
        if (empty($files)) return null;

        $file = $files[array_rand($files)];
        $basename = pathinfo($file, PATHINFO_FILENAME);
        preg_match('/(\d{4})s$/', $basename, $matches);
        $decade = $matches[1] ?? null;

        $data = json_decode(file_get_contents($file), true);
        if (!is_array($data) || empty($data)) return null;

        $item = $data[array_rand($data)];
        $item['decade'] = $decade;

        return $item;
    }
}
