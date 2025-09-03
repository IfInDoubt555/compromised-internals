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
use App\Jobs\GenerateImageVariants;

class PostController extends Controller
{
    use AuthorizesRequests;
    /** Sizes & formats for generated variants used across blog cards/hero */
    private const VARIANT_SIZES   = [160, 320, 640, 960, 1280];
    private const VARIANT_FORMATS = ['webp','avif'];

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
        // Optional board context (?board=slug) – when present, form hides selector and posts to that board.
        $board = null;
        if ($request->filled('board')) {
            $board = Board::where('slug', $request->string('board'))->first();
        }

        // Provide all boards for the selector when no fixed board is set
        $boards = Board::orderBy('position')->get();

        return view('posts.create', compact('board', 'boards'));
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
                'posts', 'post_', 1280, null
            );
            if (!$processedPath) {
                return back()->withErrors(['image_path' => 'Invalid image uploaded.']);
            }
            $validated['image_path'] = $processedPath;
            $this->queueImageVariants($processedPath);
        }

        // Slug
        $validated['slug'] = $request->slug_mode === 'manual' && $request->filled('slug')
            ? SlugService::generate($request->slug)
            : SlugService::generate($validated['title']);

        // Ensure new posts are visible under the new scheme
        $validated['status']       = $validated['status']       ?? 'published';
        $validated['published_at'] = $validated['published_at'] ?? now();

        $validated['user_id'] = Auth::id();

        $post = Post::create($validated);

        // --- tags (unchanged from your version) ---
        if (class_exists(\App\Models\Tag::class) && method_exists($post, 'tags')) {
            $attachIds = [];

            if ($request->string('slug_mode') === 'manual') {
                $tags = collect($request->input('tags', []));
                if ($tags->isEmpty() && $request->filled('tags')) {
                    $tags = collect(explode(',', (string) $request->input('tags')));
                }

                $tags = $tags->map(fn ($t) => \Illuminate\Support\Str::of($t)->lower()  ->trim())
                             ->filter()
                             ->map(fn ($t) => \Illuminate\Support\Str::slug($t,     '-'))
                             ->filter()
                             ->unique()
                             ->values();

                foreach ($tags as $slug) {
                    $name = \Illuminate\Support\Str::headline(str_replace('-', ' ',     $slug));
                    $tag  = \App\Models\Tag::firstOrCreate(['slug' => $slug], ['name' => $name]);
                    $attachIds[] = $tag->id;
                }
            }

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

            if ($attachIds) {
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
        $now = now();

        // Accept: new published posts, legacy publish_status, or legacy approved
        $isPublic =
            (($post->status === 'published') && $post->published_at && $post->published_at->lte($now))
            || (is_null($post->status) && $post->publish_status === 'published')
            || ($post->status === 'approved');

        abort_unless($isPublic, 404);

        $relations = ['user', 'board'];
        if (class_exists(\App\Models\Tag::class) && method_exists($post, 'tags')) {
            $relations[] = 'tags';
        }
        $post->load($relations);

        // Prev/next using the same visibility rules
        $visibility = function ($q) use ($now) {
            $q->where(function ($q) use ($now) {
                $q->where(function ($q) use ($now) {
                    $q->where('status', 'published')
                      ->whereNotNull('published_at')
                      ->where('published_at', '<=', $now);
                })
                ->orWhere(function ($q) {
                    $q->whereNull('status')->where('publish_status', 'published');
                })
                ->orWhere('status', 'approved');
            });
        };

        $previous = Post::where('id', '<', $post->id)
                        ->where($visibility)
                        ->orderBy('id', 'desc')
                        ->first();

        $next = Post::where('id', '>', $post->id)
                    ->where($visibility)
                    ->orderBy('id')
                    ->first();

        $boardColor = optional($post->board)->color_token ?? 'sky';

        return view('posts.show', compact('post', 'previous', 'next', 'boardColor'));
    }

    public function edit(Post $post)
    {
        // Provide boards for the selector (to change board association)
        $boards = Board::orderBy('position')->get();

        return view('posts.edit', compact('post', 'boards'));
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
                'posts', 'post_', 1280, null
            );
            if (!$processedPath) {
                return back()->withErrors(['image_path' => 'Invalid image uploaded.']);
            }
            $validated['image_path'] = $processedPath;
            $this->queueImageVariants($processedPath);
        }
    
        $validated['slug'] = $request->slug_mode === 'manual' && $request->filled('slug')
            ? SlugService::generate($request->slug, $post->id)
            : SlugService::generate($validated['title'], $post->id);
    
        // Keep published fields intact unless you’re changing scheduling elsewhere
        $validated['status']       = $validated['status']       ?? $post->status ?? 'published';
        $validated['published_at'] = $validated['published_at'] ?? $post->published_at ?? now();
    
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
    /**
     * Kick off variant generation for a stored image.
     */
    private function queueImageVariants(?string $path): void
    {
        if (!$path) return;
        GenerateImageVariants::dispatch($path, self::VARIANT_SIZES, self::VARIANT_FORMATS);
    }
}