<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Post;
use Illuminate\View\View;

class BoardController extends Controller
{
    public function index(): View
    {
        $boards = Board::withCount('threads')
            ->orderBy('position')
            ->get();

        return view('boards.index', compact('boards'));
    }

    public function show(Board $board): View
    {
        // Threads in this board (public: only published)
        $threads = $board->threads()
            ->published()
            ->latest('published_at')
            ->with(['user'])        // eager load author
            ->withCount('replies')  // reply counts
            ->paginate(20);

        // Recent blog posts linked to this board (public: only published)
        $posts = Post::query()
            ->published()
            ->where('board_id', $board->id)
            ->latest('published_at')
            ->take(6)
            ->get();

        return view('boards.show', compact('board', 'threads', 'posts'));
    }
}