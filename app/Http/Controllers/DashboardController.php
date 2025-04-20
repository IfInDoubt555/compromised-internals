<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        $posts = Post::where('user_id', $user->id)->latest()->take(5)->get();
        $postCount = Post::where('user_id', $user->id)->count();

        $orders = $user->orders()->with('items')->latest()->get();

        return view('dashboard', compact('posts', 'postCount', 'orders', 'user'));
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
            'body' => 'required|string',
        ]);

        // Example logic placeholder: update user profile or content here
        // e.g., Auth::user()->update($validated);
    }

    public function orders()
    {
        $orders = Auth::user()->orders()->with('items')->latest()->get();
        return view('profile.orders', compact('orders'));
    }
}
