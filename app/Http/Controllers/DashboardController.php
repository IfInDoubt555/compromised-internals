<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Post;

class DashboardController extends Controller
{
    public function __construct()
{
    $this->middleware('auth');
}
public function index()
{
    $posts = Post::where('user_id', auth()->id())->latest()->take(5)->get();
    $postCount = Post::where('user_id', auth()->id())->count();

    $orders = auth()->check() 
        ? auth()->user()->orders()->with('items')->latest()->get()
        : collect(); // Empty if not logged in (safety fallback)

    return view('dashboard', [
        'posts' => $posts,
        'postCount' => $postCount,
        'orders' => $orders,
    ]);
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

    // Example of updating a post or user profile goes here.
}
public function orders()
{
    $orders = auth()->user()->orders()->with('items')->latest()->get();

    return view('profile.orders', compact('orders'));
}

}