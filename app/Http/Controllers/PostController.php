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
        $this->authorize('create', Post::class);
        return view('posts.create');
    }

    public function store(StorePostRequest $request)
    {
        $this->authorize('create', Post::class);
        $validated = $request->validated();

        // Handle image if provided
        if ($request->hasFile('image_path') && $request->file('image_path')->isValid()) {
            $processedPath = $this->processAndStoreImage($request->file('image_path'));
            if (!$processedPath) {
                return back()->withErrors(['image_path' => 'Invalid image uploaded.']);
            }
            $validated['image_path'] = $processedPath;
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
            // Optional: delete old image
            if ($post->image_path && Storage::disk('public')->exists($post->image_path)) {
                Storage::disk('public')->delete($post->image_path);
            }

            $processedPath = $this->processAndStoreImage($request->file('image_path'));
            if (!$processedPath) {
                return back()->withErrors(['image_path' => 'Invalid image uploaded.']);
            }
            $validated['image_path'] = $processedPath;
        }

        $validated['slug'] = $request->slug_mode === 'manual' && $request->filled('slug')
            ? SlugService::generate($request->slug, $post->id)
            : SlugService::generate($validated['title'], $post->id);

        $post->update($validated);

        return redirect()->route('blog.index')->with('success', 'Post updated successfully!');
    }

    public function destroy(Post $post)
    {
        if ($post->image_path && Storage::disk('public')->exists($post->image_path)) {
            Storage::disk('public')->delete($post->image_path);
        }

        $post->delete();

        return redirect()->route('blog.index')->with('success', 'Post deleted successfully!');
    }

    /**
     * Process and store an uploaded image using Intervention
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string|null
     */
    private function processAndStoreImage($file): ?string
    {
        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getContent());

            $image->resize(1280, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $filename = uniqid('post_') . '.jpg';
            Storage::disk('public')->put("posts/{$filename}", (string) $image->toJpeg(90));

            return "posts/{$filename}";
        } catch (\Throwable $e) {
            Log::error('Image processing failed', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
