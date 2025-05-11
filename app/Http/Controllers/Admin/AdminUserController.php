<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\User;


class AdminUserController extends Controller
{
    public function ban(User $user)
    {
        $user->update(['banned_at' => Carbon::now()]);
        return redirect()->route('admin.users.index')->with('success', 'User has been banned.');
    }

    public function unban(User $user)
    {
        $user->update(['banned_at' => null]);
        return redirect()->route('admin.users.index')->with('success', 'User has been unbanned.');
    }
    public function show(User $user)
    {
        $posts = $user->posts()->latest()->paginate(5);
        $orders = $user->orders()->latest()->get();

        return view('admin.users.show', compact('user', 'posts', 'orders'));
    }
    public function index(Request $request)
    {
        $query = User::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.users.index', compact('users'));
    }
}
