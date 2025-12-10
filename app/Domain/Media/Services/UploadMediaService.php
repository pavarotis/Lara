<?php

declare(strict_types=1);

namespace App\Domain\Media\Services;

use App\Domain\Businesses\Models\Business;
use App\Domain\Media\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadMediaService
{
    public function __construct(
        private GenerateVariantsService $generateVariantsService
    ) {}

    /**
     * Upload a media file
     */
    public function execute(
        Business $business,
        UploadedFile $file,
        ?int $folderId = null
    ): Media {
        return DB::transaction(function () use ($business, $file, $folderId) {
            // Generate unique filename
            $filename = $this->generateFilename($file);

            // Determine storage path
            $folderPath = $this->getFolderPath($business->id, $folderId);
            $storagePath = "media/{$business->id}/{$folderPath}/{$filename}";

            // Store file
            $path = $file->storeAs("media/{$business->id}/{$folderPath}", $filename, 'public');

            // Determine file type
            $type = $this->determineType($file->getMimeType());

            // Get file metadata
            $metadata = $this->getFileMetadata($file, $path);

            // Create Media record
            $media = Media::create([
                'business_id' => $business->id,
                'folder_id' => $folderId,
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'type' => $type,
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
                'metadata' => $metadata,
                'created_by' => auth()->id(),
            ]);

            // Generate variants for images
            if ($type === 'image') {
                $this->generateVariantsService->execute($media);
            }

            return $media->fresh();
        });
    }

    /**
     * Generate unique filename
     */
    private function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('Ymd_His');
        $random = Str::random(8);

        return "{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Get folder path for storage
     */
    private function getFolderPath(int $businessId, ?int $folderId): string
    {
        if (! $folderId) {
            return now()->format('Y/m');
        }

        // Get folder path from MediaFolder
        $folder = \App\Domain\Media\Models\MediaFolder::find($folderId);

        if ($folder && $folder->path) {
            return $folder->path;
        }

        return now()->format('Y/m');
    }

    /**
     * Determine file type from MIME type
     */
    private function determineType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        if (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        }

        return 'document';
    }

    /**
     * Get file metadata
     */
    private function getFileMetadata(UploadedFile $file, string $path): array
    {
        $metadata = [];

        // For images, get dimensions
        if (str_starts_with($file->getMimeType(), 'image/')) {
            $imageInfo = getimagesize($file->getRealPath());
            if ($imageInfo) {
                $metadata['width'] = $imageInfo[0];
                $metadata['height'] = $imageInfo[1];
            }
        }

        return $metadata;
    }
}
