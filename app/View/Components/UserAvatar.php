<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UserAvatar extends Component
{
    public User $user;
    public int $size;

    public function __construct(User $user, int $size = 80)
    {
        $this->user = $user;
        $this->size = $size;
    }

    public function render(): View|string
    {
        return view('components.user-avatar');
    }
}