<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Thread;
use Illuminate\Contracts\View\View;

class AdminScheduledController extends Controller
{
    public function index(): View
    {
        return view('admin.scheduled', [
            'posts'   => Post::scheduled()->orderBy('published_at')->get(),
            'threads' => Thread::scheduled()->orderBy('published_at')->get(),
        ]);
    }
}