<?php

declare(strict_types=1);

namespace App\Domain\Media\Services;

use App\Domain\Media\Models\Media;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateVariantsService
{
    public function __construct(
        private ImageOptimizationService $imageOptimizationService
    ) {}

    /**
     * Generate image variants (thumb, small, medium, large)
     */
    public function execute(Media $media): void
    {
        if ($media->type !== 'image') {
            return;
        }

        $originalPath = Storage::disk('public')->path($media->path);

        if (! file_exists($originalPath)) {
            return;
        }

        $variants = [];
        $variantsDir = dirname($media->path).'/variants';

        // Define variant sizes
        $sizes = [
            'thumb' => [150, 150],
            'small' => [400, 400],
            'medium' => [800, 800],
            'large' => [1200, 1200],
        ];

        foreach ($sizes as $name => [$width, $height]) {
            $variantPath = $this->generateVariant(
                $originalPath,
                $variantsDir,
                $media->name,
                $name,
                $width,
                $height
            );

            if ($variantPath) {
                $variants[$name] = $variantPath;
            }
        }

        // Update media metadata with variant paths
        $metadata = $media->metadata ?? [];
        $metadata['variants'] = $variants;
        $optimizedVariants = $this->imageOptimizationService->generateVariants($media->path);

        $media->update([
            'metadata' => $metadata,
            'variants' => $optimizedVariants,
        ]);
    }

    /**
     * Generate a single variant
     */
    private function generateVariant(
        string $originalPath,
        string $variantsDir,
        string $originalName,
        string $variantName,
        int $width,
        int $height
    ): ?string {
        try {
            // Get image info
            $imageInfo = getimagesize($originalPath);
            if (! $imageInfo) {
                return null;
            }

            [$originalWidth, $originalHeight, $imageType] = $imageInfo;

            // Calculate new dimensions maintaining aspect ratio
            $ratio = min($width / $originalWidth, $height / $originalHeight);
            $newWidth = (int) ($originalWidth * $ratio);
            $newHeight = (int) ($originalHeight * $ratio);

            // Create image resource from original
            $source = match ($imageType) {
                IMAGETYPE_JPEG => imagecreatefromjpeg($originalPath),
                IMAGETYPE_PNG => imagecreatefrompng($originalPath),
                IMAGETYPE_GIF => imagecreatefromgif($originalPath),
                IMAGETYPE_WEBP => imagecreatefromwebp($originalPath),
                default => null,
            };

            if (! $source) {
                return null;
            }

            // Create new image
            $destination = imagecreatetruecolor($newWidth, $newHeight);

            // Preserve transparency for PNG/GIF
            if ($imageType === IMAGETYPE_PNG || $imageType === IMAGETYPE_GIF) {
                imagealphablending($destination, false);
                imagesavealpha($destination, true);
                $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
                imagefilledrectangle($destination, 0, 0, $newWidth, $newHeight, $transparent);
            }

            // Resize image
            imagecopyresampled(
                $destination,
                $source,
                0, 0, 0, 0,
                $newWidth, $newHeight,
                $originalWidth, $originalHeight
            );

            // Generate variant filename
            $pathInfo = pathinfo($originalName);
            $variantFilename = "{$pathInfo['filename']}-{$variantName}.{$pathInfo['extension']}";
            $variantPath = "{$variantsDir}/{$variantFilename}";

            // Ensure directory exists
            Storage::disk('public')->makeDirectory($variantsDir);

            // Save variant
            $fullPath = Storage::disk('public')->path($variantPath);

            match ($imageType) {
                IMAGETYPE_JPEG => imagejpeg($destination, $fullPath, 90),
                IMAGETYPE_PNG => imagepng($destination, $fullPath, 9),
                IMAGETYPE_GIF => imagegif($destination, $fullPath),
                IMAGETYPE_WEBP => imagewebp($destination, $fullPath, 90),
                default => false,
            };

            // Free memory
            imagedestroy($source);
            imagedestroy($destination);

            return $variantPath;
        } catch (\Exception $e) {
            // Log error but don't fail the upload
            Log::warning("Failed to generate variant {$variantName} for media: ".$e->getMessage());

            return null;
        }
    }
}
