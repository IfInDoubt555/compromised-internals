<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Services\SlugService;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user')->latest()->paginate(6);
        return view('blog.index', compact('posts'));
    }

    public function create()
    {
        // You may authorize here too if desired
        $this->authorize('create', Post::class);
        return view('posts.create');
    }
    
    public function store(StorePostRequest $request)
    {
        // Explicitly authorize the creation of the post
        $this->authorize('create', Post::class);

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
    
        if ($request->hasFile('image_path') && $request->file('image_path')->isValid()) {
            $manager = new ImageManager(new Driver());
            $file = $request->file('image_path');
    
            try {
                // Read image from file contents
                $image = $manager->read($file->getContent());
    
                // Resize while preserving aspect ratio
                $image->resize(1280, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
    
                $filename = uniqid('post_') . '.jpg';
    
                // Save image to storage
                Storage::disk('public')->put("posts/{$filename}", (string) $image->toJpeg(90));
    
                // Update the validated data with the image path
                $validated['image_path'] = "posts/{$filename}";
            } catch (\Throwable $e) {
                \Log::error('Image decode failed', [
                    'message' => $e->getMessage(),
                ]);
    
                return back()->withErrors(['image_path' => 'Invalid image uploaded.']);
            }
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
