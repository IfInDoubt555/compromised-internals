<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Thread;
use Illuminate\Http\Request;

class PublishQueueController extends Controller
{
    public function index(Request $request)
    {
        // Posts
        $postDrafts = Post::where('status', 'draft')
            ->latest('updated_at')->paginate(15, ['*'], 'postDrafts');
        $postScheduled = Post::where('status', 'scheduled')
            ->orderBy('publish_at')->paginate(15, ['*'], 'postScheduled');
        $postPublished = Post::where('status', 'published')
            ->latest('published_at')->limit(10)->get();

        // Threads
        $threadDrafts = Thread::where('status', 'draft')
            ->latest('updated_at')->paginate(15, ['*'], 'threadDrafts');
        $threadScheduled = Thread::where('status', 'scheduled')
            ->orderBy('publish_at')->paginate(15, ['*'], 'threadScheduled');
        $threadPublished = Thread::where('status', 'published')
            ->latest('published_at')->limit(10)->get();

        return view('admin.publish.index', compact(
            'postDrafts', 'postScheduled', 'postPublished',
            'threadDrafts', 'threadScheduled', 'threadPublished'
        ));
    }

    // Optional: forwards to your existing form
    public function create()
    {
        // if you already have data for the form (e.g., $boards), pass it here
        return view('admin.publish.create');
    }
}