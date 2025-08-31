<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of published posts (public blog index).
     */
    public function index(Request $request)
    {
        $posts = Post::query()
            ->where('status', 'published')       // only published posts
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->paginate(10);

        return view('blog.index', compact('posts'));
    }
}