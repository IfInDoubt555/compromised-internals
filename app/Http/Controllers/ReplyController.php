<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\Thread;
use App\Http\Requests\StoreReplyRequest;
use App\Http\Requests\UpdateReplyRequest;
use Illuminate\Http\RedirectResponse;

class ReplyController extends Controller
{
    public function store(StoreReplyRequest $request, Thread $thread): RedirectResponse
    {
        $user = $request->user();

        if (! $user->hasVerifiedEmail()) {
            return back()->withErrors(['You must verify your email address to reply.']);
        }

        $data = $request->validated();

        $thread->replies()->create([
            'user_id' => $user->id,
            'body'    => $data['body'], // HTML already stripped; markdown allowed
        ]);

        // keep “Hot Right Now” fresh
        $thread->update(['last_activity_at' => now()]);

        return back()->with('success', 'Reply posted!');
    }

    public function update(UpdateReplyRequest $request, Reply $reply): RedirectResponse
    {
        // authorize() already checked ownership in UpdateReplyRequest
        $data = $request->validated();

        $reply->update(['body' => $data['body']]);
        $reply->thread()->update(['last_activity_at' => now()]);

        return back()->with('success', 'Reply updated!');
    }

    public function destroy(\Illuminate\Http\Request $request, Reply $reply): RedirectResponse
    {
        // Keep current ownership guard (no ReplyPolicy yet)
        if ($request->user()->id !== (int) $reply->user_id) {
            abort(403);
        }

        $reply->delete();

        return back()->with('success', 'Reply deleted.');
    }
}