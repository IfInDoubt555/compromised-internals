<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Board;

class PostModerationController extends Controller
{
    public function index()
    {
        // Only items that actually need review
        $pendingPosts = Post::where('status', 'pending')
            ->with('user')
            ->latest()
            ->get();

        return view('admin.posts.moderation', compact('pendingPosts'));
    }

    public function edit(Post $post)
    {
        $boards = Board::orderBy('name')->get();

        return view('admin.posts.edit', [
            'post'   => $post,
            'boards' => $boards,
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title'         => ['required','string','max:255'],
            'slug'          => ['nullable','string','max:255'],
            'excerpt'       => ['nullable','string'],
            'body'          => ['required','string'],
            'board_id'      => ['nullable','exists:boards,id'],
            'status'        => ['required','in:draft,scheduled,published'],
            'scheduled_for' => ['nullable','date'],
        ]);

        // normalize schedule → UTC
        $scheduled = $data['scheduled_for'] ?? null;
        if ($scheduled) {
            $scheduled = Carbon::parse($scheduled, config('app.timezone'))->utc();
        }

        // derive timestamps from status
        if ($data['status'] === 'published') {
            $data['published_at']  = now()->utc();
            $data['scheduled_for'] = null;
        } elseif ($data['status'] === 'scheduled') {
            $data['scheduled_for'] = $scheduled;
            $data['published_at']  = null;
        } else { // draft
            $data['scheduled_for'] = null;
            $data['published_at']  = null;
        }

        $post->fill($data)->save();

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('status', 'Post saved.');
    }

    public function approve($post)
    {
        $post = Post::findOrFail($post);

        // Only process pending; otherwise no-op
        if ($post->status !== 'pending') {
            return back()->with('info', 'This post is already processed.');
        }

        if ($post->scheduled_for && $post->scheduled_for->isFuture()) {
            $post->status = 'scheduled';
        } else {
            $post->status       = 'published';
            $post->published_at = now()->utc();
            $post->scheduled_for = null;
        }

        $post->save();

        return back()->with('success', 'Post approved.');
    }

    public function reject($post)
    {
        $post = Post::findOrFail($post);
        $post->update(['status' => 'rejected']);

        return back()->with('success', 'Post rejected.');
    }
}