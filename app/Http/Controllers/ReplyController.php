<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\Thread;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    public function store(Request $request, Thread $thread)
    {
        $user = $request->user();

        if (!$user->hasVerifiedEmail()) {
            return back()->withErrors(['You must verify your email address to reply.']);
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000', new \App\Rules\NoBannedWords],
        ]);

        $thread->replies()->create([
            'user_id' => $user->id,
            'body'    => $validated['body'],
        ]);

        // keep “Hot Right Now” fresh
        $thread->update(['last_activity_at' => now()]);

        return back()->with('success', 'Reply posted!');
    }

    public function update(Request $request, Reply $reply)
    {
        if ($request->user()->id !== $reply->user_id) {
            abort(403);
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000', new \App\Rules\NoBannedWords],
        ]);

        $reply->update(['body' => $validated['body']]);
        $reply->thread()->update(['last_activity_at' => now()]);

        return back()->with('success', 'Reply updated!');
    }

    public function destroy(Request $request, Reply $reply)
    {
        if ($request->user()->id !== $reply->user_id) {
            abort(403);
        }

        $reply->delete();

        return back()->with('success', 'Reply deleted.');
    }
}