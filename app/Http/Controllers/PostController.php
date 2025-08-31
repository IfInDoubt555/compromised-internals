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
        $q = Post::with(['user', 'board'])
            ->where(function ($q) {
                $now = now();
            
                // ✅ New publishing flow
                $q->where(function ($q) use ($now) {
                    $q->where('status', 'published')
                      ->whereNotNull('published_at')
                      ->where('published_at', '<=', $now);
                })
            
                // ✅ Legacy publish_status (older posts before status refactor)
                ->orWhere(function ($q) {
                    $q->whereNull('status')
                      ->where('publish_status', 'published');
                })
            
                // ✅ Legacy "approved" (moderation state from old system)
                ->orWhere('status', 'approved');
            })
            ->orderByRaw('COALESCE(published_at, created_at) DESC');
        
        // 🔹 Optional: filter by board (?board=slug)
        if ($request->filled('board')) {
            if ($board = \App\Models\Board::where('slug', $request->string('board'))->first()) {
                $q->where('board_id', $board->id);
            }
        }
    
        // 🔹 Optional: legacy "tag" filter
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
    
        // Slug (post slug, not tags)
        $validated['slug'] = $request->slug_mode === 'manual' && $request->filled('slug')
            ? SlugService::generate($request->slug)
            : SlugService::generate($validated['title']);
    
        $validated['user_id'] = Auth::id();
    
        $post = Post::create($validated);
    
        /**
         * Tags
         * - When slug_mode === 'manual', accept user-entered tags.
         * - Supports both tags[] (array) and legacy comma string 'tags'.
         * - Always add auto tag for board if present (as before).
         */
        if (
            class_exists(\App\Models\Tag::class) &&
            method_exists($post, 'tags')
        ) {
            $attachIds = [];
        
            // Manual/user-defined tags only when manual mode is selected
            if ($request->string('slug_mode') === 'manual') {
                // Prefer array payload tags[]
                $tags = collect($request->input('tags', []));
            
                // Fallback: legacy comma-joined 'tags'
                if ($tags->isEmpty() && $request->filled('tags')) {
                    $tags = collect(explode(',', (string) $request->input('tags')));
                }
            
                // Normalize -> slug -> unique
                $tags = $tags
                    ->map(fn ($t) => Str::of($t)->lower()->trim())
                    ->filter()
                    ->map(fn ($t) => Str::slug($t, '-'))
                    ->filter()
                    ->unique()
                    ->values();
            
                if ($tags->isNotEmpty()) {
                    foreach ($tags as $slug) {
                        // Human-ish name for display, e.g. "rally-winter-blast" -> "Rally Winter Blast"
                        $name = Str::headline(str_replace('-', ' ', $slug));
                        $tag  = \App\Models\Tag::firstOrCreate(
                            ['slug' => $slug],
                            ['name' => $name]
                        );
                        $attachIds[] = $tag->id;
                    }
                }
            }
        
            // Auto-tag by board (existing behavior)
            if (!empty($validated['board_id'])) {
                $board = Board::find($validated['board_id']);
                if ($board) {
                    $boardTag = \App\Models\Tag::firstOrCreate(
                        ['slug' => 'board-' . $board->slug],
                        ['name' => $board->name]
                    );
                    $attachIds[] = $boardTag->id;
                }
            }
        
            if (!empty($attachIds)) {
                $post->tags()->syncWithoutDetaching($attachIds);
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
    
        // Eager-load relations (tags only if the relation exists)
        $relations = ['user', 'board'];
        if (class_exists(\App\Models\Tag::class) && method_exists($post, 'tags')) {
            $relations[] = 'tags';
        }
        $post->load($relations);
    
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
    
        // One source of truth for the board theme color
        $boardColor = optional($post->board)->color_token ?? 'sky';
    
        return view('posts.show', [
            'post'        => $post,
            'previous'    => $previous,
            'next'        => $next,
            'boardColor'  => $boardColor,
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