<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Thread;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class PublisherController extends Controller
{
    // NEW: queue page (drafts + scheduled)
    public function index()
    {
        $drafts = Post::with(['user','board'])
            ->where(function ($q) {
                $q->where('status','draft')
                  ->orWhere(function ($q) {
                      $q->whereNull('status')->where('publish_status','draft'); // legacy
                  });
            })
            ->latest('updated_at')
            ->get();

        $scheduled = Post::where('status','scheduled')
            ->orderBy('published_at')
            ->get();

        return view('admin.publish.index', compact('drafts','scheduled'));
    }

    // (create/store exist already)

    // NEW: admin-only preview for draft/scheduled
    public function preview(Post $post)
    {
        // only allow preview if not public yet
        $isPreviewable = ($post->status === 'draft')
            || ($post->status === 'scheduled')
            || (is_null($post->status) && in_array($post->publish_status, ['draft','scheduled']));

        abort_unless($isPreviewable, 404);

        return view('admin.publish.preview', [
            'post' => $post,
            'isPreview' => true,
        ]);
    }

    // NEW: quick publish now
    public function publishNow(Post $post)
    {
        $this->authorize('update', $post);

        $post->forceFill([
            'status'        => 'published',
            'publish_status'=> 'published', // keep legacy in sync
            'published_at'  => now()->utc(),
            'scheduled_for' => null,
        ])->save();

        return redirect()->route('admin.publish.index')->with('status', 'Post published.');
    }

    // NEW: quick schedule
    public function schedule(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $data = $request->validate([
            'published_at' => ['required','date'],
        ]);

        $post->forceFill([
            'status'        => 'scheduled',
            'publish_status'=> 'scheduled',
            'published_at'  => Carbon::parse($data['published_at'], config('app.timezone'))->utc(),
            'scheduled_for' => Carbon::parse($data['published_at'], config('app.timezone'))->utc(),
        ])->save();

        return redirect()->route('admin.publish.index')->with('status', 'Post scheduled.');
    }


    public function create()
    {
        $boards = Board::orderBy('name')->get();

        return view('admin.publish.create', [
            'boards' => $boards,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'            => ['required','in:blog,thread'],

            // shared
            'title'           => ['required','string','max:255'],
            'slug'            => ['nullable','string','max:255'],
            'body'            => ['required','string','max:20000'],

            // preferred single status; accepts 'now' from UI
            'status'          => ['nullable','in:draft,scheduled,now,published'],

            'scheduled_for'   => ['nullable','date'],

            // blog-only
            'board_id'        => ['nullable','exists:boards,id'],
            'image_path'      => ['nullable','image','mimes:jpg,jpeg,png,webp','max:4096'],

            // thread-only
            'thread_board_id' => ['nullable','exists:boards,id'],
        ]);

        $nowUtc = now()->utc();
        $scheduledUtc = !empty($data['scheduled_for'])
            ? Carbon::parse($data['scheduled_for'], config('app.timezone'))->utc()
            : null;

        $isAdmin = Auth::user()?->isAdmin();
        $intent  = $data['status'] ?? 'draft';
        if ($intent === 'now') $intent = 'published';

        if ($data['type'] === 'blog') {
            $slug = $data['slug'] ? Str::slug($data['slug']) : Str::slug($data['title']);

            $payload = [
                'title'    => $data['title'],
                'slug'     => $slug,
                'excerpt'  => Str::limit(strip_tags($data['body']), 160),
                'body'     => $data['body'],
                'user_id'  => Auth::id(),
                'board_id' => $data['board_id'] ?? null,
            ];

            if ($isAdmin) {
                // BYPASS moderation: write final status + timestamps
                if ($intent === 'published') {
                    $payload['status']         = 'published';
                    $payload['published_at']   = $nowUtc;
                    $payload['scheduled_for']  = null;
                } elseif ($intent === 'scheduled') {
                    $payload['status']         = 'scheduled';
                    $payload['scheduled_for']  = $scheduledUtc;
                    $payload['published_at']   = null;
                } else {
                    $payload['status']         = 'draft';
                    $payload['scheduled_for']  = null;
                    $payload['published_at']   = null;
                }
            } else {
                $payload['status'] = 'pending';
            }

            // keep legacy column in sync (optional but helpful while you transition)
            $payload['publish_status'] = $payload['status'];

            if ($request->hasFile('image_path')) {
                $payload['image_path'] = $request->file('image_path')->store('posts', 'public');
            }

            $post = Post::create($payload);

            return $isAdmin
                ? redirect()->route('admin.publish.index')->with('status', 'Post saved.')
                : redirect()->route('admin.posts.moderation')->with('status', 'Submitted for review.');
        }

        // THREAD
        $request->validate([
            'thread_board_id' => ['required','exists:boards,id'],
        ]);

        $slug = $data['slug'] ? Str::slug($data['slug']) : Str::slug($data['title']);
        if (Thread::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::lower(Str::random(6));
        }

        $payload = [
            'board_id'         => (int) $data['thread_board_id'],
            'user_id'          => Auth::id(),
            'title'            => $data['title'],
            'slug'             => $slug,
            'body'             => $data['body'],
            'last_activity_at' => $nowUtc,
        ];

        if ($isAdmin) {
            if ($intent === 'published') {
                $payload['status']        = 'published';
                $payload['published_at']  = $nowUtc;
                $payload['scheduled_for'] = null;
            } elseif ($intent === 'scheduled') {
                $payload['status']        = 'scheduled';
                $payload['scheduled_for'] = $scheduledUtc;
                $payload['published_at']  = null;
            } else {
                $payload['status']        = 'draft';
                $payload['scheduled_for'] = null;
                $payload['published_at']  = null;
            }
        } else {
            $payload['status'] = 'pending';
        }

        $thread = Thread::create($payload);

        return $isAdmin
            ? redirect()->route('admin.publish.index')->with('status', 'Thread saved.')
            : redirect()->route('admin.threads.index')->with('status', 'Submitted for review.');
    }
}