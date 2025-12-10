<?php

declare(strict_types=1);

namespace App\Domain\Media\Policies;

use App\Domain\Media\Models\MediaFolder;
use App\Models\User;

class MediaFolderPolicy
{
    /**
     * Determine if user can view any folders
     */
    public function viewAny(User $user): bool
    {
        return $user->is_admin || $user->hasPermission('media.view');
    }

    /**
     * Determine if user can create folders
     */
    public function create(User $user): bool
    {
        return $user->is_admin || $user->hasPermission('media.create');
    }

    /**
     * Determine if user can update a folder
     */
    public function update(User $user, MediaFolder $folder): bool
    {
        return $user->is_admin || $user->hasPermission('media.update');
    }

    /**
     * Determine if user can delete a folder
     */
    public function delete(User $user, MediaFolder $folder): bool
    {
        return $user->is_admin || $user->hasPermission('media.delete');
    }
}
