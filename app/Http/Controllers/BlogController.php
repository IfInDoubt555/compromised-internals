<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\View\View;


class BlogController extends Controller
{
    /**
     * Public blog index.
     * - Shows NEW flow posts: status='published' AND published_at <= now.
     * - Also shows LEGACY posts that were 'approved' or publish_status='published'.
     * - Orders by published_at when present, otherwise created_at.
     */
    public function index(Request $request): View
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

        // Optional search (?q=… or ?tag=…)
        if ($term = $request->query('q', $request->query('tag'))) {
            $q->where(function ($sub) use ($term) {
                $like = '%' . $term . '%';
                $sub->where('title', 'like', $like)
                    ->orWhere('excerpt', 'like', $like)
                    ->orWhere('body', 'like', $like);
            });
        }

        // Optional board filter (?board=slug)
        if ($board = $request->query('board')) {
            $q->whereHas('board', fn ($b) => $b->where('slug', $board));
        }

        $posts = $q->paginate(9)->withQueryString();

        // Feed "Hot Right Now" (cached 10 minutes). Requires Post::scopeHot().
        $hotPosts = Cache::remember('hot-posts:v1', 600, function () {
            return Post::query()->hot(14)->limit(5)->get();
        });

        return view(
            /** @var view-string $view */
            $view = 'blog.index',
            compact('posts', 'hotPosts')
        );    }
}