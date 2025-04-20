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
        return view('home', compact('posts'));
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
}
