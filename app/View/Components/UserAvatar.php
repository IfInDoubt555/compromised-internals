<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class UserAvatar extends Component
{
    public $user;
    public $size;

    public function __construct($user = null, $size = 'w-20 h-20')
    {
        $this->user = $user ?? Auth::user();
        $this->size = $size;
    }

    public function render(): View|Closure|string
    {
        return view('components.user-avatar');
    }
}