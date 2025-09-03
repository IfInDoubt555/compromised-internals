<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BoardController extends Controller
{
    public function index(): View
    {
        $boards = Board::query()
            ->withCount([
                // ✅ count only what we actually show
                'threads as threads_count' => fn ($q) => $q->visibleForList(),
            ])
            ->orderBy('position')
            ->get();

        return view('boards.index', compact('boards'));
    }

    public function show(Board $board): View
    {
        // ✅ list using the same visibility scope as index
        $threads = $board->threads()
            ->visibleForList()
            ->with(['user:id,name,display_name'])
            ->withCount('replies')
            ->orderByDesc(DB::raw('COALESCE(last_activity_at, published_at, created_at)'))
            ->paginate(20)
            ->withQueryString();

        // Recent blog posts linked to this board (assuming many-to-many posts<->boards)
        $posts = Post::query()
            ->published()
            ->whereHas('boards', fn ($q) => $q->whereKey($board->id))
            ->latest('published_at')
            ->take(6)
            ->get();

        return view('boards.show', compact('board', 'threads', 'posts'));
    }
}