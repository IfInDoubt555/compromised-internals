<?php

namespace App\Policies;

use App\Models\Reply;
use App\Models\User;

class ReplyPolicy
{
    public function viewAny(?User $user): bool { return true; }
    public function view(?User $user, Reply $reply): bool { return true; }

    public function create(User $user): bool
    {
        return (bool) $user;
    }

    public function update(User $user, Reply $reply): bool
    {
        return $reply->user_id === $user->id || $user->isAdmin();
    }

    public function delete(User $user, Reply $reply): bool
    {
        return $reply->user_id === $user->id || $user->isAdmin();
    }

    public function restore(User $user, Reply $reply): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Reply $reply): bool
    {
        return $user->isAdmin();
    }
}