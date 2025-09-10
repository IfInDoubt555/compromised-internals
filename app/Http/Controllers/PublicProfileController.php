<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;

class PublicProfileController extends Controller
{
    public function show(User $user): View
    {
        return view('profile.public', compact('user'));
    }
}