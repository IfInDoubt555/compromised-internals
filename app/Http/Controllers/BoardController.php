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
        // Threads in this board
        $threads = $board->threads()
            ->with(['user'])           // eager load author
            ->withCount('replies')     // show reply counts efficiently
            ->latest('last_activity_at')
            ->paginate(20);

        // Recent blog posts linked to this board
        $posts = Post::query()
            ->where('status', 'approved')
            ->where('board_id', $board->id)
            ->latest()
            ->take(6)
            ->get();

        return view('boards.show', compact('board', 'threads', 'posts'));
    }
}