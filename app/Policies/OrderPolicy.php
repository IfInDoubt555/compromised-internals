<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    // Admin can see all; user can see their own orders
    public function viewAny(User $user): bool { return $user->isAdmin(); }

    public function view(User $user, Order $order): bool
    {
        return $user->isAdmin() || $order->user_id === $user->id;
    }

    // Users never create orders directly via policy; controllers/checkout logic governs that
    public function create(User $user): bool { return $user->isAdmin(); }

    public function update(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }
}