<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        // Public: only show published posts, newest first by published_at
        $posts = Post::published()
            ->latest('published_at')
            ->paginate(10);

        return view('blog.index', compact('posts'));
    }

    public function create()
    {
        return view('blog.create');
    }

    public function store(StorePostRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('posts', 'public');
        }

        $post = Post::create($validated);

        return redirect()->route('blog.show', $post->slug)->with('success', 'Post created!');
    }

    public function show($slug)
    {
        // Public: resolve only published posts by slug
        $post = Post::published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('blog.show', compact('post'));
    }
}