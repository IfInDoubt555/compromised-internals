<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Thread;
use App\Models\User;

class ThreadPolicy
{
    /** Anyone authenticated can create (tighten later if needed). */
    public function create(User $user): bool
    {
        return true;
    }

    /** Owner or admin can update. */
    public function update(User $user, Thread $thread): bool
    {
        return $thread->user_id === $user->id || $user->isAdmin();
    }

    /** Owner or admin can delete. */
    public function delete(User $user, Thread $thread): bool
    {
        return $thread->user_id === $user->id || $user->isAdmin();
    }

    /** Allow guests to view. Keep nullable for guest access. */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /** Allow guests to view. */
    public function view(?User $user, Thread $thread): bool
    {
        return true;
    }

    public function restore(User $user, Thread $thread): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Thread $thread): bool
    {
        return $user->isAdmin();
    }
}