<?php

namespace App\Http\Controllers;

use App\Models\Thread;

class ThreadController extends Controller
{
    public function show(Thread $thread)
    {
        $thread->load(['board','user','replies.user']);
        return view('threads.show', compact('thread'));
    }
}