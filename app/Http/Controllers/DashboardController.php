<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Thread;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $user = Auth::user();
        /** @var \App\Models\User $user */ // auth middleware ensures non-null

        // Posts
        $posts = Post::where('user_id', $user->getKey())
            ->latest()->take(10)->get();
        $postCount = Post::where('user_id', $user->getKey())->count();

        // Threads
        $threads = Thread::where('user_id', $user->getKey())
            ->latest()->take(10)->get();
        $threadCount = Thread::where('user_id', $user->getKey())->count();

        // Orders
        $orders = $user->orders()->with('items')->latest()->get();

        /** @var view-string $view */
        $view = 'dashboard.index';
        return view($view, compact('user', 'posts', 'postCount', 'threads', 'threadCount', 'orders'));
    }

    public function show(): View
    {
        /** @var view-string $view */
        $view = 'dashboard.show';
        return view($view);
    }

    public function edit(): View
    {
        /** @var view-string $view */
        $view = 'dashboard.edit';
        return view($view);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body'  => 'required|string',
        ]);

        // TODO: implement dashboard update action if/when needed
        // Auth::user()?->update($validated);

        return back()->with('status', 'Updated.');
    }

    public function orders(): View
    {
        $user = Auth::user();
        /** @var \App\Models\User $user */

        $orders = $user->orders()->with('items')->latest()->get();

        /** @var view-string $view */
        $view = 'profile.orders';
        return view($view, compact('orders'));
    }
}