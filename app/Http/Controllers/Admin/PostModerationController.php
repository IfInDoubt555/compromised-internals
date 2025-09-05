<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PostModerationController extends Controller
{
    public function index(): View
    {
        // Only items that actually need review
        $pendingPosts = Post::where('status', 'pending')
            ->with('user')
            ->latest()
            ->get();

        return view('admin.posts.moderation', compact('pendingPosts'));
    }

    public function edit(Post $post): View
    {
        $boards = Board::orderBy('name')->get();

        return view('admin.posts.edit', compact('post', 'boards'));
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $data = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'slug'         => ['nullable', 'string', 'max:255'],
            'excerpt'      => ['nullable', 'string', 'max:160'],
            'body'         => ['required', 'string', 'max:20000'],
            'board_id'     => ['nullable', 'exists:boards,id'],
            'status'       => ['required', 'in:draft,scheduled,published'],
            'published_at' => ['nullable', 'date'], // local time from datetime-local
            'image_path'   => ['nullable','file','image','mimes:jpg,jpeg,png,webp,avif','max:5120'],
        ]);

        // Slug: prefer provided, else from title
        $slug = $data['slug'] ? Str::slug($data['slug']) : Str::slug($data['title']);

        // Handle optional image upload (delete old file if replacing)
        $imagePath = $post->image_path;
        if ($request->hasFile('image_path')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image_path')->store('posts', 'public');
        }

        // Determine final status + timestamps (normalize to UTC)
        $finalStatus    = $data['status'];
        $publishedAtUtc = null;

        if ($finalStatus === 'published') {
            $publishedAtUtc = now()->utc();
        } elseif ($finalStatus === 'scheduled') {
            $request->validate(['published_at' => ['required', 'date']]);
            $scheduledUtc = Carbon::parse($data['published_at'], config('app.timezone'))->utc();

            if ($scheduledUtc->lte(now()->utc())) {
                $finalStatus    = 'published';
                $publishedAtUtc = now()->utc();
            } else {
                $publishedAtUtc = $scheduledUtc;
            }
        }

        // Persist (mirror legacy columns for BC)
        $post->forceFill([
            'title'         => $data['title'],
            'slug'          => $slug,
            'excerpt'       => $data['excerpt'] ?? null,
            'body'          => $data['body'],
            'board_id'      => $data['board_id'] ?? null,
            'status'        => $finalStatus,
            'published_at'  => $finalStatus === 'draft' ? null : $publishedAtUtc,
            'scheduled_for' => $finalStatus === 'scheduled' ? $publishedAtUtc : null,
            'publish_status'=> $finalStatus,
            'image_path'    => $imagePath,
        ])->save();

        return redirect()
            ->route('admin.publish.index')
            ->with('status', 'Post saved.');
    }

    public function approve(Post $post): RedirectResponse
    {
        if ($post->status !== 'pending') {
            return back()->with('info', 'This post is already processed.');
        }

        if ($post->published_at && $post->published_at->isFuture()) {
            $post->status = 'scheduled';
        } else {
            $post->status        = 'published';
            $post->published_at  = now()->utc();
        }

        $post->scheduled_for  = $post->published_at;
        $post->publish_status = $post->status;

        $post->save();

        return back()->with('success', 'Post approved.');
    }

    public function reject(Post $post): RedirectResponse
    {
        $post->forceFill([
            'status'         => 'rejected',
            'publish_status' => 'rejected',
        ])->save();

        return back()->with('success', 'Post rejected.');
    }
}