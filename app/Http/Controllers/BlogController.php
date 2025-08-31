<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $q = Post::with(['user','board'])
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at');

        if ($term = $request->query('q')) {
            $q->where(function ($sub) use ($term) {
                $sub->where('title', 'like', "%{$term}%")
                   ->orWhere('excerpt', 'like', "%{$term}%")
                   ->orWhere('body', 'like', "%{$term}%");
            });
        }

        if ($board = $request->query('board')) {
            $q->whereHas('board', fn ($b) => $b->where('slug', $board));
        }

        $posts = $q->paginate(9)->withQueryString();

        return view('blog.index', compact('posts'));
    }
}