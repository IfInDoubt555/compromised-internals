<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostModerationController extends Controller
{
    public function index()
    {
        $pendingPosts = Post::where('status', 'pending')->with('user')->latest()->get();
        return view('admin.posts.moderation', compact('pendingPosts'));
    }

    public function approve($post)
    {
        $post = Post::findOrFail($post);
        $post->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Post approved.');
    }

    public function reject($post)
    {
        $post = Post::findOrFail($post);
        $post->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Post rejected.');
    }
}
