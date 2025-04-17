<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $posts = \App\Models\Post::latest()->get();
    
        return view('blog.index', compact('posts'));
    }
    
    public function create()
    {
        return view('blog.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'slug' => 'required|alpha_dash|unique:posts,slug',
            'excerpt' => 'required|max:500',
            'body' => 'required',
            'image_path' => 'nullable|image|max:2048',
        ]);
    
        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('posts', 'public');
        }
    
        $post = \App\Models\Post::create($validated);
    
        return redirect()->route('blog.show', $post->slug)->with('success', 'Post created!');
    }
    public function show($slug)
    {
        $post = \App\Models\Post::where('slug', $slug)->firstOrFail();
    
        return view('blog.show', compact('post'));
    }
}
