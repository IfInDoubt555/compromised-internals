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
            'slug'  => ['nullable','string','max:180'],
            'body'  => ['required','string','max:20000'],
        ]);

        // Manual slug (if provided) or from title
        $slug = $data['slug'] ?: Str::slug($data['title']);

        // Ensure uniqueness
        if (Thread::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::lower(Str::random(6));
        }

        $thread = Thread::create([
            'board_id'         => $board->id,
            'user_id'          => $request->user()->id,
            'title'            => $data['title'],
            'slug'             => $slug,
            'body'             => $data['body'],
            'last_activity_at' => now(),
        ]);

        // Optional: auto-tag with Tag model if your app has it
        if (class_exists(\App\Models\Tag::class) && method_exists($thread, 'tags')) {
            $tag = \App\Models\Tag::firstOrCreate(
                ['slug' => 'board-' . $board->slug],
                ['name' => $board->name]
            );
            $thread->tags()->syncWithoutDetaching([$tag->id]);
        }

        return redirect()
            ->route('threads.show', $thread->slug)
            ->with('success', 'Thread created!');
    }
}