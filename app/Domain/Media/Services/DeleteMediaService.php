<?php

declare(strict_types=1);

namespace App\Domain\Media\Services;

use App\Domain\Media\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteMediaService
{
    /**
     * Delete media file and all variants
     */
    public function execute(Media $media): bool
    {
        return DB::transaction(function () use ($media) {
            // Delete original file
            if (Storage::disk('public')->exists($media->path)) {
                Storage::disk('public')->delete($media->path);
            }

            // Delete variants
            $variants = $media->metadata['variants'] ?? [];
            foreach ($variants as $variantPath) {
                if (Storage::disk('public')->exists($variantPath)) {
                    Storage::disk('public')->delete($variantPath);
                }
            }

            // Delete Media record
            $deleted = $media->delete();

            // Handle folder cleanup (if empty)
            if ($media->folder_id) {
                $this->cleanupEmptyFolder($media->folder_id);
            }

            return $deleted;
        });
    }

    /**
     * Cleanup empty folder
     */
    private function cleanupEmptyFolder(int $folderId): void
    {
        $folder = \App\Domain\Media\Models\MediaFolder::find($folderId);

        if (! $folder) {
            return;
        }

        // Check if folder is empty
        $hasMedia = $folder->files()->exists();
        $hasChildren = $folder->children()->exists();

        if (! $hasMedia && ! $hasChildren) {
            // Folder is empty, delete it
            $folder->delete();
        }
    }
}
