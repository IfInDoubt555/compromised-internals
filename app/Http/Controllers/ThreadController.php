<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ThreadController extends Controller
{
    // If you prefer, you can rely on route middleware instead of this:
    // public function __construct()
    // {
    //     $this->middleware('auth')->only(['create','store','edit','update','destroy']);
    // }

    public function show(Thread $thread): View
    {
        $thread->load(['board','user','replies.user']);
        return view('threads.show', compact('thread'));
    }

    public function create(Board $board): View
    {
        $this->authorize('create', Thread::class);

        return view('threads.create', compact('board'));
    }

    public function store(Request $request, Board $board): RedirectResponse
    {
        $this->authorize('create', Thread::class);

        $data = $request->validate([
            'title' => ['required','string','max:160'],
            'slug'  => ['nullable','string','max:180'],
            'body'  => ['required','string','max:20000'],
        ]);

        // Slug: use provided slug or derive from title
        $slug = $data['slug'] ?: Str::slug($data['title']);
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

        // Optional tagging by board
        if (class_exists(\App\Models\Tag::class) && method_exists($thread, 'tags')) {
            $tag = \App\Models\Tag::firstOrCreate(
                ['slug' => 'board-' . $board->slug],
                ['name' => $board->name]
            );
            $thread->tags()->syncWithoutDetaching([$tag->id]);
        }

        return redirect()
            ->route('threads.show', $thread)
            ->with('success', 'Thread created!');
    }

    public function edit(Thread $thread): View
    {
        $this->authorize('update', $thread);

        return view('threads.edit', compact('thread'));
    }

    public function update(Request $request, Thread $thread): RedirectResponse
    {
        $this->authorize('update', $thread);

        $data = $request->validate([
            'title' => ['required','string','max:160'],
            'slug'  => ['nullable','string','max:180',
                // if slug is supplied, keep it unique except for this thread
                Rule::unique('threads','slug')->ignore($thread->id),
            ],
            'body'  => ['required','string','max:20000'],
        ]);

        // If slug left blank, regenerate from title (and ensure uniqueness)
        $slug = $data['slug'] ?: Str::slug($data['title']);
        if ($slug !== $thread->slug && Thread::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::lower(Str::random(6));
        }

        $thread->update([
            'title'            => $data['title'],
            'slug'             => $slug,
            'body'             => $data['body'],
            'last_activity_at' => now(),
        ]);

        return redirect()
            ->route('threads.show', $thread)
            ->with('success', 'Thread updated.');
    }

    public function destroy(Thread $thread): RedirectResponse
    {
        $this->authorize('delete', $thread);

        $boardSlug = optional($thread->board)->slug;
        $thread->delete();

        // After delete, send user back to the board (or dashboard fallback)
        return $boardSlug
            ? redirect()->route('boards.show', $boardSlug)->with('success', 'Thread deleted.')
            : redirect()->route('dashboard')->with('success', 'Thread deleted.');
    }
}