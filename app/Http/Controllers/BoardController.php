<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

final class BoardController extends Controller
{
    public function index(): View
    {
        $boards = Board::query()
            ->withCount([
                // Count only threads that are visible on list pages
                'threads as threads_count' => function (Builder $q): void {
                    /** @var Builder<\App\Models\Thread> $q */
                    $q->visibleForList();
                },
            ])
            ->orderBy('position')
            ->get();

        /** @var view-string $view */
        $view = 'boards.index';
        return view($view, compact('boards'));
    }

    public function show(Board $board): View
    {
        /** @var HasMany<Thread> $threads */
        $threads = $board->threads()->latest();
    
        $threads = $threads
            ->visibleForList()
            ->with([
                // Load user and just the profile fields we need for display_name
                'user:id,name',
                'user.profile:id,user_id,display_name',
            ])
            ->withCount('replies')
            ->orderByRaw('COALESCE(last_activity_at, published_at, created_at) DESC')
            ->paginate(20)
            ->withQueryString();
            
        // Recent published blog posts linked to this board
        $posts = Post::query()
            ->published()
            ->whereHas('board', fn (Builder $q) => $q->whereKey($board->getKey()))
            // alternatively: ->where('board_id', $board->getKey())
            ->latest('published_at')
            ->limit(6)
            ->get();
            
        /** @var view-string $view */
        $view = 'boards.show';
        return view($view, compact('board', 'threads', 'posts'));
    }
}