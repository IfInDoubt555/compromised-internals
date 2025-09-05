<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Thread;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $user = Auth::user();

        // Posts
        $posts = Post::where('user_id', $user->id)
            ->latest()->take(10)->get();
        $postCount = Post::where('user_id', $user->id)->count();

        // Threads
        $threads = Thread::where('user_id', $user->id)
            ->latest()->take(10)->get();
        $threadCount = Thread::where('user_id', $user->id)->count();

        // Orders
        $orders = $user->orders()->with('items')->latest()->get();

        return view(
            /** @var view-string $view */
            $view = 'dashboard',
            compact('user', 'posts', 'postCount', 'threads', 'threadCount', 'orders')
        );
    }

    public function show(): View
    {
        return view(
            /** @var view-string $view */
            $view = 'dashboard.show'
        );
    }

    public function edit(): View
    {
        return view(
            /** @var view-string $view */
            $view = 'dashboard.edit'
        );
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body'  => 'required|string',
        ]);

        // TODO: implement dashboard update action if needed
        // e.g. Auth::user()->update($validated);

        return back()->with('status', 'Updated.');
    }

    public function orders(): View
    {
        $orders = Auth::user()->orders()->with('items')->latest()->get();

        return view(
            /** @var view-string $view */
            $view = 'profile.orders',
            compact('orders')
        );
    }
}