<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Policies;

use App\Domain\Catalog\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * Determine if user can view any categories
     */
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine if user can view a category
     */
    public function view(User $user, Category $category): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine if user can create categories
     */
    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine if user can update a category
     */
    public function update(User $user, Category $category): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine if user can delete a category
     */
    public function delete(User $user, Category $category): bool
    {
        return $user->is_admin;
    }
}
