<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Services\SlugService;
use App\Services\ImageService;
use App\Models\Post;
use App\Models\Board;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;

class PostController extends Controller
{
    use AuthorizesRequests;

    /**
     * Public blog index — show only truly published posts.
     */
    public function index(Request $request)
    {
        $q = Post::with('user')
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at');

        // Optional: filter by board (?board=slug)
        if ($request->filled('board')) {
            if ($board = Board::where('slug', $request->string('board'))->first()) {
                $q->where('board_id', $board->id);
            }
        }

        // Optional: legacy "tag" filter (kept as-is)
        if ($request->filled('tag')) {
            $q->where('slug', 'like', '%' . $request->tag . '%');
        }

        $posts = $q->paginate(9)->appends($request->only(['tag', 'board']));

        return view('blog.index', compact('posts'));
    }

    public function create(Request $request)
    {
        $board = null;
        if ($request->filled('board')) {
            $board = Board::where('slug', $request->string('board'))->first();
        }
        return view('posts.create', compact('board'));
    }

    public function store(StorePostRequest $request)
    {
        $this->authorize('create', Post::class);
        $validated = $request->validated();

        // Optional board association
        if ($request->filled('board_id')) {
            $validated['board_id'] = (int) $request->input('board_id');
        }

        // Image
        if ($request->hasFile('image_path') && $request->file('image_path')->isValid()) {
            $processedPath = ImageService::processAndStore(
                $request->file('image_path'),
                'posts',
                'post_',
                1280,
                null
            );
            if (!$processedPath) {
                return back()->withErrors(['image_path' => 'Invalid image uploaded.']);
            }
            $validated['image_path'] = $processedPath;
        }

        // Slug
        $validated['slug'] = $request->slug_mode === 'manual' && $request->filled('slug')
            ? SlugService::generate($request->slug)
            : SlugService::generate($validated['title']);

        $validated['user_id'] = Auth::id();

        $post = Post::create($validated);

        // Optional auto-tag by board (if Tag model exists)
        if (!empty($validated['board_id'])
            && class_exists(\App\Models\Tag::class)
            && method_exists($post, 'tags')) {
            $board = Board::find($validated['board_id']);
            if ($board) {
                $tag = \App\Models\Tag::firstOrCreate(
                    ['slug' => 'board-' . $board->slug],
                    ['name' => $board->name]
                );
                $post->tags()->syncWithoutDetaching([$tag->id]);
            }
        }

        return redirect()->route('blog.index')->with('success', 'Post created successfully!');
    }

    /**
     * Public show — only allow truly published posts.
     */
    public function show(Post $post)
    {
        abort_unless(
            $post->status === 'published' &&
            !is_null($post->published_at) &&
            $post->published_at->lte(now()),
            404
        );

        $previous = Post::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('id', '<', $post->id)
            ->orderBy('id', 'desc')
            ->first();

        $next = Post::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('id', '>', $post->id)
            ->orderBy('id')
            ->first();

        return view('posts.show', [
            'post' => $post,
            'previous' => $previous,
            'next' => $next,
        ]);
    }

    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    public function update(StorePostRequest $request, Post $post)
    {
        $validated = $request->validated();

        if ($request->filled('board_id')) {
            $validated['board_id'] = (int) $request->input('board_id');
        }

        if ($request->hasFile('image_path') && $request->file('image_path')->isValid()) {
            if ($post->image_path && Storage::disk('public')->exists($post->image_path)) {
                Storage::disk('public')->delete($post->image_path);
            }
            $processedPath = ImageService::processAndStore(
                $request->file('image_path'),
                'posts',
                'post_',
                1280,
                null
            );
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

        $previousUrl = url()->previous();
        $postShowUrl = route('blog.show', $post->slug);

        if (Str::contains($previousUrl, $postShowUrl)) {
            return redirect()->route('blog.index')->with('success', 'Post deleted successfully!');
        }

        return redirect()->back()->with('success', 'Post deleted successfully!');
    }

    public function toggleLike(Post $post)
    {
        $user = auth()->user();

        if (!$user->hasVerifiedEmail()) {
            return back()->withErrors(['You must verify your email address to like posts.']);
        }

        if ($post->likes()->where('user_id', $user->id)->exists()) {
            $post->likes()->detach($user->id);
        } else {
            $post->likes()->attach($user->id);
        }

        return back()->with('success', 'Toggled like.');
    }
}