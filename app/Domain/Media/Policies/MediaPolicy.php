<?php

declare(strict_types=1);

namespace App\Domain\Media\Policies;

use App\Domain\Media\Models\Media;
use App\Models\User;

class MediaPolicy
{
    /**
     * Determine if user can view any media
     */
    public function viewAny(User $user): bool
    {
        return $user->is_admin || $user->hasPermission('media.view');
    }

    /**
     * Determine if user can view a media
     */
    public function view(User $user, Media $media): bool
    {
        return $user->is_admin || $user->hasPermission('media.view');
    }

    /**
     * Determine if user can create media
     */
    public function create(User $user): bool
    {
        return $user->is_admin || $user->hasPermission('media.create');
    }

    /**
     * Determine if user can update a media
     */
    public function update(User $user, Media $media): bool
    {
        return $user->is_admin || $user->hasPermission('media.update');
    }

    /**
     * Determine if user can delete a media
     */
    public function delete(User $user, Media $media): bool
    {
        return $user->is_admin || $user->hasPermission('media.delete');
    }
}
