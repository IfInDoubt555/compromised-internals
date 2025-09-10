<?php

namespace App\Policies;

use App\Models\Board;
use App\Models\User;

class BoardPolicy
{
    public function viewAny(?User $user): bool { return true; }
    public function view(?User $user, Board $board): bool { return true; }

    // Board CRUD is admin-only in most forums
    public function create(User $user): bool { return $user->isAdmin(); }
    public function update(User $user, Board $board): bool { return $user->isAdmin(); }
    public function delete(User $user, Board $board): bool { return $user->isAdmin(); }
    public function restore(User $user, Board $board): bool { return $user->isAdmin(); }
    public function forceDelete(User $user, Board $board): bool { return $user->isAdmin(); }
}