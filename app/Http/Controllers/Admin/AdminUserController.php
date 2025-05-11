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
}
