<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the posts.
     */
    public function index()
    {
        $posts = Post::latest()->get();

        return view('blog.index', compact('posts'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'excerpt' => 'required|max:500',
            'body' => 'required',
            'image_path' => 'nullable|image|max:2048',
        ]);
    
        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('posts', 'public');
        }
    
        $validated['slug'] = $this->createUniqueSlug($validated['title']);
        $validated['user_id'] = Auth::id();
    
        Post::create($validated);
    
        return redirect()->route('blog.index')->with('success', 'Post created successfully!');
    }
    
    /**
     * Create a unique slug for a post.
     */
    protected function createUniqueSlug($title, $ignoreId = null)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;
    
        $query = Post::where('slug', $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId); // 🛡️ ignore the current post's ID
        }
    
        while ($query->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
            $query = Post::where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
        }
    
        return $slug;
    }    
    /**
     * Display the specified post.
     */
    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified post in storage.
     */
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'excerpt' => 'required|max:500',
            'body' => 'required',
            'image_path' => 'nullable|image|max:2048',
        ]);
    
        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('posts', 'public');
        }
    
        // 🔥 Always generate a new slug when the title changes
        $validated['slug'] = $this->createUniqueSlug($validated['title'], $post->id);
    
        $post->update($validated);
    
        return redirect()->route('blog.index')->with('success', 'Post updated successfully!');
    }
    

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->route('blog.index')->with('success', 'Post deleted successfully!');
    }
}
