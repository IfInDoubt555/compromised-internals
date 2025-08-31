<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Thread;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PublisherController extends Controller
{
    public function create()
    {
        $boards = Board::orderBy('name')->get();

        return view('admin.publish.create', [
            'boards' => $boards,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'           => ['required','in:blog,thread'],

            // shared
            'title'          => ['required','string','max:255'],
            'slug'           => ['nullable','string','max:255'],
            'body'           => ['required','string','max:20000'],

            // scheduling (names differ by type; we accept both and map)
            'publish_status' => ['nullable','in:draft,scheduled,published'], // for blog
            'status'         => ['nullable','in:draft,scheduled,published'], // for thread
            'scheduled_for'  => ['nullable','date'],

            // only for blog posts
            'board_id'       => ['nullable','exists:boards,id'], // blog can associate to a board if you like
            'image_path'     => ['nullable','image','mimes:jpg,jpeg,png,webp','max:4096'],

            // only for threads
            'thread_board_id'=> ['nullable','exists:boards,id'], // required when type=thread (validated below)
        ]);

        $nowUtc = now()->utc();
        $scheduledUtc = null;
        if (!empty($data['scheduled_for'])) {
            $scheduledUtc = Carbon::parse($data['scheduled_for'], config('app.timezone'))->utc();
        }

        if ($data['type'] === 'blog') {
            // determine publish_status for posts
            $publish = $data['publish_status'] ?? 'draft';

            $payload = [
                'title'          => $data['title'],
                'slug'           => $data['slug'] ? Str::slug($data['slug']) : Str::slug($data['title']),
                'excerpt'        => Str::limit(strip_tags($data['body']), 160),
                'body'           => $data['body'],
                'user_id'        => Auth::id(),
                'board_id'       => $data['board_id'] ?? null,   // optional association
                'publish_status' => $publish,
                'scheduled_for'  => $publish === 'scheduled' ? $scheduledUtc : null,
                'published_at'   => $publish === 'published' ? $nowUtc : null,
            ];

            if ($request->hasFile('image_path')) {
                $payload['image_path'] = $request->file('image_path')->store('posts', 'public');
            }

            $post = Post::create($payload);

            return redirect()
                ->route('admin.posts.edit', $post)   // go to admin edit for final tweaks if desired
                ->with('status', 'Blog post created.');
        }

        // THREAD
        // require a board for threads
        $request->validate([
            'thread_board_id' => ['required','exists:boards,id'],
        ]);

        $status = $data['status'] ?? 'draft';
        $slug = $data['slug'] ? Str::slug($data['slug']) : Str::slug($data['title']);
        if (Thread::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::lower(Str::random(6));
        }

        $thread = Thread::create([
            'board_id'        => (int) $data['thread_board_id'],
            'user_id'         => Auth::id(),
            'title'           => $data['title'],
            'slug'            => $slug,
            'body'            => $data['body'],
            'last_activity_at'=> $nowUtc,
            'status'          => $status,
            'scheduled_for'   => $status === 'scheduled' ? $scheduledUtc : null,
            'published_at'    => $status === 'published' ? $nowUtc : null,
        ]);

        return redirect()
            ->route('admin.threads.edit', $thread)
            ->with('status', 'Thread created.');
    }
}