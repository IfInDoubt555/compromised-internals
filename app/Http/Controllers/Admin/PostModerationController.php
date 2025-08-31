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
        $pendingPosts = Post::where('status', 'pending')->with('user')->latest()->get();
        return view('admin.posts.moderation', compact('pendingPosts'));
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
            'publish_status' => ['required','in:draft,scheduled,published'],
        ]);

        // normalize schedule → UTC
        $scheduled = $data['scheduled_for'] ?? null;
        if ($scheduled) {
            $scheduled = Carbon::parse($scheduled, config('app.timezone'))->utc();
        }

        if ($data['publish_status'] === 'published') {
            $data['published_at']  = now()->utc();
            $data['scheduled_for'] = null;
        } elseif ($data['publish_status'] === 'scheduled') {
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

    public function edit(Post $post)
    {
        // Admin-only edit of blog post (scheduling + basics)
        $boards = Board::orderBy('name')->get();

        return view('admin.posts.edit', [
            'post'   => $post,
            'boards' => $boards,
        ]);
    }

    public function approve($post)
    {
        $post = Post::findOrFail($post);
        $post->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Post approved.');
    }

    public function reject($post)
    {
        $post = Post::findOrFail($post);
        $post->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Post rejected.');
    }
}
