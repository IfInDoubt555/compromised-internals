<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Post;
use Illuminate\View\View;
use Illuminate\Database\Eloquent\Builder;


class BoardController extends Controller
{
    public function index(): View
    {
        $boards = Board::query()
            ->withCount([
                // count only threads that are visible on list pages
                'threads as threads_count' => function ($q): void {
                    /** @var Builder<\App\Models\Thread> $q */
                    $q->visibleForList(); // scope on Thread; docblock tells PHPStan the Builder model
                },            
            ])
            ->orderBy('position')
            ->get();

        return view('boards.index', compact('boards'));
    }

    public function show(Board $board): View
    {
        // Threads visible for list pages, scoped to this board
        $threadsQuery = $board->threads();
        /** @var Builder<\App\Models\Thread> $threadsQuery */
        $threads = $threadsQuery
            ->visibleForList()
            // Load user and just the profile fields we need for display_name
            ->with([
                'user:id,name',
                'user.profile:id,user_id,display_name',
            ])
            ->with(['user:id,name'])
            ->withCount('replies')
            ->orderByRaw('COALESCE(last_activity_at, published_at, created_at) DESC')
            ->paginate(20)
            ->withQueryString();

        // Recent published blog posts linked to this board
        $posts = Post::query()
            ->published()
            ->whereHas('board', fn ($q) => $q->whereKey($board->getKey()))
            // alternatively: ->where('board_id', $board->getKey())
            ->latest('published_at')
            ->limit(6)
            ->get();

        return view('boards.show', compact('board', 'threads', 'posts'));
    }
}