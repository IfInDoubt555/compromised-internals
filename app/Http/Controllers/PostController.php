<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Services\SlugService;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user')->latest()->get();
        return view('blog.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(StorePostRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('posts', 'public');
        }

        $validated['slug'] = $request->slug_mode === 'manual' && $request->filled('slug')
            ? SlugService::generate($request->slug)
            : SlugService::generate($validated['title']);

        $validated['user_id'] = Auth::id();

        Post::create($validated);

        return redirect()->route('blog.index')->with('success', 'Post created successfully!');
    }

    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    public function update(StorePostRequest $request, Post $post)
    {
        $validated = $request->validated();

        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('posts', 'public');
        }

        $validated['slug'] = $request->slug_mode === 'manual' && $request->filled('slug')
            ? SlugService::generate($request->slug, $post->id)
            : SlugService::generate($validated['title'], $post->id);

        $post->update($validated);

        return redirect()->route('blog.index')->with('success', 'Post updated successfully!');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('blog.index')->with('success', 'Post deleted successfully!');
    }
}
