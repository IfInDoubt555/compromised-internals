<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function store(StoreCommentRequest $request, Post $post)
    {
        $user = $request->user();

        if (! $user->hasVerifiedEmail()) {
            return back()->withErrors(['You must verify your email address to comment.']);
        }

        $data = $request->validated(); // body sanitized in StoreCommentRequest

        $post->comments()->create([
            'user_id' => $user->id,
            'body'    => $data['body'],
        ]);

        return back()->with('success', 'Comment added!');
    }

    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $data = $request->validated(); // body sanitized in UpdateCommentRequest

        $comment->update(['body' => $data['body']]);

        return back()->with('success', 'Comment updated!');
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return back()->with('success', 'Comment deleted.');
    }
}