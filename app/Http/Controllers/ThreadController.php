<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\Board;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreThreadRequest;
use App\Http\Requests\UpdateThreadRequest;

class ThreadController extends Controller
{
    public function show(Thread $thread): View
    {
        if (! $thread->isPublished() && Gate::denies('update', $thread)) {
            abort(404);
        }

        $thread->load([
            'board',
            'user.profile',
            'replies' => fn ($q) => $q->oldest()->with('user.profile'),
        ]);

        return view('threads.show', compact('thread'));
    }

    public function create(Board $board): View
    {
        $this->authorize('create', Thread::class);

        return view('threads.create', compact('board'));
    }

    public function store(StoreThreadRequest $request, Board $board): RedirectResponse
    {
        $this->authorize('create', Thread::class);

        $data = $request->validated();

        // Slug: use provided slug or derive from title, ensure uniqueness
        $slug = $data['slug'] ?? Str::slug($data['title']);
        if (Thread::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::lower(Str::random(6));
        }

        $thread = Thread::create([
            'board_id'         => $board->id,
            'user_id'          => $request->user()->id,
            'title'            => $data['title'],
            'slug'             => $slug,
            'body'             => $data['body'],   // markdown accepted; HTML stripped by request
            'last_activity_at' => now(),
            'status'           => 'published',
            'published_at'     => now(),
        ]);

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

    public function update(UpdateThreadRequest $request, Thread $thread): RedirectResponse
    {
        $this->authorize('update', $thread);

        $data = $request->validated();

        // Only change slug if provided
        if ($request->filled('slug')) {
            $proposed = Str::slug($request->input('slug'));
            $newSlug  = $proposed;
            if ($newSlug !== $thread->slug && Thread::where('slug', $newSlug)->exists()) {
                $newSlug .= '-' . Str::lower(Str::random(6));
            }
        } else {
            $newSlug = $thread->slug;
        }

        $boardChanged = ((int) $data['board_id'] !== (int) $thread->board_id);

        $thread->fill([
            'board_id'         => $data['board_id'],
            'title'            => $data['title'],
            'slug'             => $newSlug,
            'body'             => $data['body'],   // markdown accepted; HTML stripped by request
            'last_activity_at' => now(),
        ]);

        if ($thread->status !== 'scheduled') {
            $thread->status = 'published';
            $thread->published_at = $thread->published_at ?: now();
        }

        $thread->save();

        if ($boardChanged && class_exists(\App\Models\Tag::class) && method_exists($thread, 'tags')) {
            $newBoard = Board::find($data['board_id']);
            if ($newBoard) {
                $newTag = \App\Models\Tag::firstOrCreate(
                    ['slug' => 'board-' . $newBoard->slug],
                    ['name' => $newBoard->name]
                );
                $oldBoardTagIds = $thread->tags()->where('slug', 'like', 'board-%')->pluck('tags.id')->all();
                if ($oldBoardTagIds) $thread->tags()->detach($oldBoardTagIds);
                $thread->tags()->syncWithoutDetaching([$newTag->id]);
            }
        }

        return redirect()->route('threads.show', $thread)->with('success', 'Thread updated.');
    }

    public function destroy(Thread $thread): RedirectResponse
    {
        $this->authorize('delete', $thread);

        $boardSlug = optional($thread->board)->slug;
        $thread->delete();

        return $boardSlug
            ? redirect()->route('boards.show', $boardSlug)->with('success', 'Thread deleted.')
            : redirect()->route('dashboard')->with('success', 'Thread deleted.');
    }
}
