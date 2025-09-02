<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

        return view('admin.posts.edit', compact('post', 'boards'));
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'slug'         => ['nullable', 'string', 'max:255'],
            'excerpt'      => ['nullable', 'string', 'max:160'],
            'body'         => ['required', 'string', 'max:20000'],
            'board_id'     => ['nullable', 'exists:boards,id'],
            'status'       => ['required', 'in:draft,scheduled,published'],
            'published_at' => ['nullable', 'date'], // local time from datetime-local
            'image_path'   => ['nullable','image','mimes:jpg,jpeg,png,webp,avif','max:5120'],
        ]);
    
        // Slug: prefer provided, else from title
        $slug = $data['slug'] ? Str::slug($data['slug']) : Str::slug($data['title']);
    
        // Optional image upload
        $imagePath = null;
        if ($request->hasFile('image_path')) {
            $imagePath = $request->file('image_path')->store('posts', 'public');
        }
    
        // Normalize published_at to UTC if present
        $publishedAtUtc = null;
        if (!empty($data['published_at'])) {
            $publishedAtUtc = Carbon::parse($data['published_at'], config('app.timezone'))->utc();
        }
    
        // Derive timestamps from status
        $finalStatus = $data['status'];
        if ($finalStatus === 'published') {
            // Publish now if no date provided
            $publishedAtUtc = $publishedAtUtc ?: now()->utc();
        } elseif ($finalStatus === 'scheduled') {
            // Require a date; if it's in the past, promote to published
            $request->validate(['published_at' => ['required', 'date']]);
            $publishedAtUtc = Carbon::parse($data['published_at'], config('app.timezone'))->utc();
        
            if ($publishedAtUtc->lte(now()->utc())) {
                $finalStatus = 'published';
            }
        } else { // draft
            $publishedAtUtc = null;
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
            // Legacy mirror only when scheduled
            'scheduled_for' => $finalStatus === 'scheduled' ? $publishedAtUtc : null,
            'publish_status'=> $finalStatus, // legacy mirror
            'image_path'    => $imagePath ?? $post->image_path,
        ])->save();
        
        return redirect()
            ->route('admin.publish.index')
            ->with('status', 'Post saved.');
    }

    public function approve(Post $post)
    {
        // Only process pending; otherwise no-op
        if ($post->status !== 'pending') {
            return back()->with('info', 'This post is already processed.');
        }

        // If a future publish_at exists -> scheduled; else publish now
        if ($post->published_at && $post->published_at->isFuture()) {
            $post->status = 'scheduled';
        } else {
            $post->status        = 'published';
            $post->published_at  = now()->utc();
        }

        // Legacy mirrors
        $post->scheduled_for  = $post->published_at;
        $post->publish_status = $post->status;

        $post->save();

        return back()->with('success', 'Post approved.');
    }

    public function reject(Post $post)
    {
        $post->forceFill([
            'status'         => 'rejected',
            'publish_status' => 'rejected', // legacy mirror
        ])->save();

        return back()->with('success', 'Post rejected.');
    }
}