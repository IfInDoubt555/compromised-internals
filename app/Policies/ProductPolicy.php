<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(?User $user): bool { return true; }
    public function view(?User $user, Product $product): bool { return true; }

    // Manage products: admin only
    public function create(User $user): bool { return $user->isAdmin(); }
    public function update(User $user, Product $product): bool { return $user->isAdmin(); }
    public function delete(User $user, Product $product): bool { return $user->isAdmin(); }
    public function restore(User $user, Product $product): bool { return $user->isAdmin(); }
    public function forceDelete(User $user, Product $product): bool { return $user->isAdmin(); }
}