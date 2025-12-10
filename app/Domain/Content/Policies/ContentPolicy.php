<?php

declare(strict_types=1);

namespace App\Domain\Content\Policies;

use App\Domain\Content\Models\Content;
use App\Models\User;

class ContentPolicy
{
    /**
     * Determine if user can view any content
     */
    public function viewAny(User $user): bool
    {
        return $user->is_admin || $user->hasPermission('content.view');
    }

    /**
     * Determine if user can view a content
     */
    public function view(User $user, Content $content): bool
    {
        return $user->is_admin || $user->hasPermission('content.view');
    }

    /**
     * Determine if user can create content
     */
    public function create(User $user): bool
    {
        return $user->is_admin || $user->hasPermission('content.create');
    }

    /**
     * Determine if user can update a content
     */
    public function update(User $user, Content $content): bool
    {
        return $user->is_admin || $user->hasPermission('content.update');
    }

    /**
     * Determine if user can delete a content
     */
    public function delete(User $user, Content $content): bool
    {
        return $user->is_admin || $user->hasPermission('content.delete');
    }
}
