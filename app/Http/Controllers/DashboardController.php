<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Thread; // ⬅️ add this

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
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

        // Orders (unchanged)
        $orders = $user->orders()->with('items')->latest()->get();

        return view('dashboard', compact(
            'user',
            'posts', 'postCount',
            'threads', 'threadCount',
            'orders'
        ));
    }

    public function show()
    {
        return view('dashboard.show');
    }

    public function edit()
    {
        return view('dashboard.edit');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body'  => 'required|string',
        ]);

        // TODO: implement dashboard update action if you actually need it
        // e.g. Auth::user()->update($validated);
        return back()->with('status', 'Updated.');
    }

    public function orders()
    {
        $orders = Auth::user()->orders()->with('items')->latest()->get();
        return view('profile.orders', compact('orders'));
    }
}