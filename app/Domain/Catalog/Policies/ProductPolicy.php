<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Policies;

use App\Domain\Catalog\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Determine if user can view any products
     */
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine if user can view a product
     */
    public function view(User $user, Product $product): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine if user can create products
     */
    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine if user can update a product
     */
    public function update(User $user, Product $product): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine if user can delete a product
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->is_admin;
    }
}

