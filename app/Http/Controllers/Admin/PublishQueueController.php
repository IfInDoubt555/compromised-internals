<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class PublishQueueController extends Controller
{
    public function index(Request $request): View
    {
        // POSTS
        $postDrafts = Post::where('status', 'draft')
            ->latest('updated_at')
            ->paginate(15, ['*'], 'postDrafts');

        $postScheduled = Post::where('status', 'scheduled')
            ->orderBy('published_at')   // canonical schedule moment
            ->paginate(15, ['*'], 'postScheduled');

        $postPublished = Post::where('status', 'published')
            ->latest('published_at')
            ->limit(10)
            ->get();

        // THREADS
        $threadDrafts = Thread::where('status', 'draft')
            ->latest('updated_at')
            ->paginate(15, ['*'], 'threadDrafts');

        $threadScheduled = Thread::where('status', 'scheduled')
            ->orderBy('scheduled_for')   // <- was publish_at
            ->paginate(15, ['*'], 'threadScheduled');

        $threadPublished = Thread::where('status', 'published')
            ->latest('published_at')
            ->limit(10)
            ->get();

        return view('admin.publish.index', compact(
            'postDrafts',
            'postScheduled',
            'postPublished',
            'threadDrafts',
            'threadScheduled',
            'threadPublished'
        ));
    }
}