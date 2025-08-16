<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ThreadController extends Controller
{
    public function show(Thread $thread): View
    {
        $thread->load(['board','user','replies.user']);
        return view('threads.show', compact('thread'));
    }

    public function create(Board $board): View
    {
        return view('threads.create', compact('board'));
    }

    public function store(Request $request, Board $board): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required','string','max:140'],
            'body'  => ['required','string','max:20000'],
        ]);

        $slug = Str::slug($data['title']);
        // keep it unique without hitting collisions
        if (Thread::where('slug', $slug)->exists()) {
            $slug .= '-'.Str::lower(Str::random(6));
        }

        $thread = Thread::create([
            'board_id'         => $board->id,
            'user_id'          => $request->user()->id,
            'title'            => $data['title'],
            'slug'             => $slug,
            'body'             => $data['body'],
            'last_activity_at' => now(),
        ]);

        return redirect()->route('threads.show', $thread->slug)
            ->with('success', 'Thread created!');
    }
}