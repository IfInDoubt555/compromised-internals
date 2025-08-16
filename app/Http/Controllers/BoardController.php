<?php

namespace App\Http\Controllers;

use App\Models\Board;

class BoardController extends Controller
{
    public function index()
    {
        $boards = Board::withCount('threads')->orderBy('position')->get();
        return view('boards.index', compact('boards'));
    }

    public function show(Board $board)
    {
        $threads = $board->threads()
            ->latest('last_activity_at')
            ->latest()
            ->paginate(20);

        return view('boards.show', compact('board','threads'));
    }
}