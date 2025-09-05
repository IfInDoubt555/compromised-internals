<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\RallyEvent;
use Illuminate\Contracts\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'userCount'  => User::count(),
            'postCount'  => Post::count(),
            'eventCount' => RallyEvent::count(), // if model exists
            'imageCount' => 89, // placeholder
        ]);
    }
}