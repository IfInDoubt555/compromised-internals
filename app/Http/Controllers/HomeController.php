<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $json = file_get_contents(public_path('data/rally-history.json'));
        $data = json_decode($json, true);

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
