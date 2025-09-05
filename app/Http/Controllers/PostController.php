<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Jobs\GenerateImageVariants;
use App\Models\Board;
use App\Models\Post;
use App\Services\ImageService;
use App\Services\SlugService;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class PostController extends Controller
{
    use AuthorizesRequests;

    /** Sizes & formats for generated variants used by cards/hero */
    private const VARIANT_SIZES   = [160, 320, 640, 960, 1280];
    private const VARIANT_FORMATS = ['webp', 'avif'];

    /**
     * Public blog index — show only truly published posts.
     */
    public function index(Request $request): View
    {
        $q = Post::with(['user', 'board'])
            ->where(function ($q) {
                $now = now();

                // New publishing flow
                $q->where(function ($q) use ($now) {
                    $q->where('status', 'published')
                        ->whereNotNull('published_at')
                        ->where('published_at', '<=', $now);
                })
                    // Legacy publish_status (older posts before status refactor)
                    ->orWhere(function ($q) {
                        $q->whereNull('status')
                            ->where('publish_status', 'published');
                    })
                    // Legacy "approved" (moderation state from old system)
                    ->orWhere('status', 'approved');
            })
            ->orderByRaw('COALESCE(published_at, created_at) DESC');

        // Optional: filter by board (?board=slug)
        if ($request->filled('board')) {
            if ($board = Board::where('slug', $request->string('board'))->first()) {
                $q->where('board_id', $board->getKey());
            }
        }

        // Optional: legacy "tag" filter (kept as-is)
        if ($request->filled('tag')) {
            $q->where('slug', 'like', '%' . (string) $request->tag . '%');
        }

        $posts = $q->paginate(9)->appends($request->only(['tag', 'board']));

        /** @var view-string $view */
        $view = 'blog.index';
        return view($view, compact('posts'));
    }

    public function create(Request $request): View
    {
        // Optional board context (?board=slug)
        $board = null;
        if ($request->filled('board')) {
            $board = Board::where('slug', $request->string('board'))->first();
        }

        // Provide all boards for the selector when no fixed board is set
        $boards = Board::orderBy('position')->get();

        /** @var view-string $view */
        $view = 'posts.create';
        return view($view, compact('board', 'boards'));
    }

    public function store(StorePostRequest $request): RedirectResponse
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
            $this->queueImageVariants($processedPath);
        }

        // Slug (match SlugService signature: table, column, base, [ignoreId])
        $validated['slug'] = $request->slug_mode === 'manual' && $request->filled('slug')
            ? SlugService::generate(
                table: 'posts',
                column: 'slug',
                base: (string) $request->slug
            )
            : SlugService::generate(
                table: 'posts',
                column: 'slug',
                base: (string) $validated['title']
            );

        // Ensure new posts are visible under the new scheme
        $validated['status']       = $validated['status']       ?? 'published';
        $validated['published_at'] = $validated['published_at'] ?? now();

        $validated['user_id'] = (int) Auth::id();

        $post = Post::create($validated);

        // --- tags (unchanged from your version, with small safety casts) ---
        if (class_exists(\App\Models\Tag::class)) {
            $attachIds = [];

            if ($request->string('slug_mode') === 'manual') {
                /** @var array<int,string>|string $rawTags */
                $rawTags = $request->input('tags', []);
                if (is_string($rawTags)) {
                    $rawTags = array_map('trim', explode(',', $rawTags));
                }
                /** @var \Illuminate\Support\Collection<int,string> $tags */
                $tags = collect($rawTags)
                    ->map(fn ($t) => (string) $t)
                    ->map(fn (string $t) => Str::of($t)->lower()->trim()->value())
                    ->filter()
                    ->map(fn (string $t) => Str::slug($t, '-'))
                    ->filter()
                    ->unique()
                    ->values();

                foreach ($tags as $slug) {
                    $name = Str::headline(str_replace('-', ' ', $slug));
                    $tag  = \App\Models\Tag::firstOrCreate(['slug' => $slug], ['name' => $name]);
                    $attachIds[] = $tag->getKey();
                }
            }

            if (!empty($validated['board_id'])) {
                $board = Board::find((int) $validated['board_id']);
                if ($board) {
                    $boardTag = \App\Models\Tag::firstOrCreate(
                        ['slug' => 'board-' . $board->slug],
                        ['name' => $board->name]
                    );
                    $attachIds[] = $boardTag->getKey();
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
    public function show(Post $post): View
    {
        $now = now();

        // Accept: new published posts, legacy publish_status, or legacy approved
        $isPublic =
            (($post->status === 'published') && $post->published_at && $post->published_at->lte($now))
            || (is_null($post->status) && $post->publish_status === 'published')
            || ($post->status === 'approved');

        abort_unless($isPublic, 404);

        // Eager-load core relations + comments oldest→newest (composer will sit at the end)
        $relations = [
            'user.profile',
            'board',
            // comments ordered oldest-first and include commenter profile
            'comments' => fn ($q) => $q->oldest()->with('user.profile'),
        ];
        if (class_exists(\App\Models\Tag::class)) {
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

        $previous = Post::where('id', '<', $post->getKey())
            ->where($visibility)
            ->orderBy('id', 'desc')
            ->first();

        $next = Post::where('id', '>', $post->getKey())
            ->where($visibility)
            ->orderBy('id')
            ->first();

        $boardColor = optional($post->board)->color_token ?? 'sky';

        /** @var view-string $view */
        $view = 'posts.show';
        return view($view, compact('post', 'previous', 'next', 'boardColor'));
    }

    public function edit(Post $post): View
    {
        // Provide boards for the selector (to change board association)
        $boards = Board::orderBy('position')->get();

        /** @var view-string $view */
        $view = 'posts.edit';
        return view($view, compact('post', 'boards'));
    }

    public function update(StorePostRequest $request, Post $post): RedirectResponse
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
            $this->queueImageVariants($processedPath);
        }

        // Slug (respect ignoreId for uniqueness)
        $validated['slug'] = $request->slug_mode === 'manual' && $request->filled('slug')
            ? SlugService::generate(
                table: 'posts',
                column: 'slug',
                base: (string) $request->slug,
                ignoreId: $post->getKey()
            )
            : SlugService::generate(
                table: 'posts',
                column: 'slug',
                base: (string) $validated['title'],
                ignoreId: $post->getKey()
            );

        // Keep published fields intact unless changing scheduling elsewhere
        $validated['status']       = $validated['status']       ?? ($post->status ?? 'published');
        $validated['published_at'] = $validated['published_at'] ?? ($post->published_at ?? now());

        $post->update($validated);

        return redirect()->route('blog.index')->with('success', 'Post updated successfully!');
    }

    public function destroy(Post $post): RedirectResponse
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

    public function toggleLike(Post $post): RedirectResponse
    {
        $user = Auth::user();

        if (!$user || !$user->hasVerifiedEmail()) {
            return back()->withErrors(['You must verify your email address to like posts.']);
        }

        if ($post->likes()->where('user_id', $user->getKey())->exists()) {
            $post->likes()->detach($user->getKey());
        } else {
            $post->likes()->attach($user->getKey());
        }

        return back()->with('success', 'Toggled like.');
    }

    /**
     * Kick off variant generation for a stored image.
     */
    private function queueImageVariants(?string $path): void
    {
        if (!$path) {
            return;
        }
    
        // Job expects path (string) + sizes (list<int>) + formats (list<string>)
        GenerateImageVariants::dispatch(
            $path,                          // Ensure $path is a string (path to the image)
            self::VARIANT_SIZES,            // Sizes should be an array of integers
            self::VARIANT_FORMATS           // Formats should be an array of strings
        );
    }
}