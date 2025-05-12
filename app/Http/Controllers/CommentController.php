<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class CommentController extends Controller
{
    use AuthorizesRequests;
    
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $post->comments()->create([
            'user_id' => auth()->id(),
            'body' => $request->body,
        ]);

        return back()->with('success', 'Comment added!');
    }
    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $request->validate(['body' => 'required|string|max:1000']);
        $comment->update(['body' => $request->body]);

        return back()->with('success', 'Comment updated!');
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();

        return back()->with('success', 'Comment deleted.');
    }
}