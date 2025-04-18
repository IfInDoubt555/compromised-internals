<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class UserAvatar extends Component
{
    public function __construct(
        public string $size = 'w-10 h-10'
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.user-avatar', [
            'user' => Auth::user(),
            'size' => $this->size,
        ]);
    }
}
