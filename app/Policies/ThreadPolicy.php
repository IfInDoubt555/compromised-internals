<?php

namespace App\Policies;

use App\Models\Thread;
use App\Models\User;

class ThreadPolicy
{
    // Anyone logged in can create a thread (you can tighten this later)
    public function create(User $user): bool
    {
        return $user !== null;
    }

    // Owner or admin can update
    public function update(User $user, Thread $thread): bool
    {
        return $thread->user_id === $user->id || $user->isAdmin();
    }

    // Owner or admin can delete
    public function delete(User $user, Thread $thread): bool
    {
        return $thread->user_id === $user->id || $user->isAdmin();
    }

    // Optional (only if you use them):
    public function viewAny(?User $user): bool { return true; }
    public function view(?User $user, Thread $thread): bool { return true; }
    public function restore(User $user, Thread $thread): bool { return $user->isAdmin(); }
    public function forceDelete(User $user, Thread $thread): bool { return $user->isAdmin(); }
}