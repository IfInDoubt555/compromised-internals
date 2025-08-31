<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Public blog index.
     * - Shows NEW flow posts: status='published' AND published_at <= now.
     * - Also shows LEGACY posts that were 'approved' or publish_status='published'.
     * - Orders by published_at when present, otherwise created_at.
     */
    public function index(Request $request)
    {
        $q = Post::with(['user', 'board'])
            ->where(function ($q) {
                // New world
                $q->where(function ($q) {
                    $q->where('status', 'published')
                      ->whereNotNull('published_at')
                      ->where('published_at', '<=', now());
                })
                // Legacy: publish_status column
                ->orWhere(function ($q) {
                    $q->whereNull('status')
                      ->where('publish_status', 'published');
                })
                // Legacy: 'approved' status
                ->orWhere(function ($q) {
                    $q->where('status', 'approved');
                });
            })
            ->orderByRaw('COALESCE(published_at, created_at) DESC');

        // Optional search
        if ($term = $request->query('q')) {
            $q->where(function ($sub) use ($term) {
                $sub->where('title', 'like', "%{$term}%")
                    ->orWhere('excerpt', 'like', "%{$term}%")
                    ->orWhere('body', 'like', "%{$term}%");
            });
        }

        // Optional board filter (?board=slug)
        if ($board = $request->query('board')) {
            $q->whereHas('board', fn ($b) => $b->where('slug', $board));
        }

        $posts = $q->paginate(9)->withQueryString();

        return view('blog.index', compact('posts'));
    }
}