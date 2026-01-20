<?php

declare(strict_types=1);

namespace App\Domain\Media\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;

/**
 * Image Optimization Service
 *
 * Generates optimized image variants (WebP, AVIF, responsive sizes).
 */
class ImageOptimizationService
{
    private ImageManager $imageManager;

    public function __construct()
    {
        // Use GD driver by default (Imagick can be swapped if available)
        $this->imageManager = ImageManager::gd();
    }

    /**
     * Generate optimized variants (WebP, AVIF, responsive sizes)
     *
     * @param  string  $path  Original image path (relative to storage)
     * @return array{webp: string|null, avif: string|null, sizes: array}
     */
    public function generateVariants(string $path): array
    {
        $disk = Storage::disk('public');
        $fullPath = $disk->path($path);

        if (! file_exists($fullPath)) {
            return [
                'webp' => null,
                'avif' => null,
                'sizes' => [],
            ];
        }

        $pathInfo = pathinfo($path);
        $directory = $pathInfo['dirname'] === '.' ? '' : $pathInfo['dirname'];
        $filename = $pathInfo['filename'];
        $extension = $pathInfo['extension'] ?? 'jpg';
        $variantsDir = trim($directory.'/variants', '/');

        // Load original image
        $image = $this->imageManager->read($fullPath);

        // Generate WebP
        $webpPath = $this->generateWebP($image, $variantsDir, $filename);

        // Generate AVIF (if supported)
        $avifPath = $this->generateAVIF($image, $variantsDir, $filename);

        // Generate responsive sizes
        $sizes = $this->generateResponsiveSizes($fullPath, $variantsDir, $filename, $extension);

        return [
            'webp' => $webpPath,
            'avif' => $avifPath,
            'sizes' => $sizes,
        ];
    }

    /**
     * Generate WebP version
     *
     * @param  mixed  $image  ImageInterface from intervention/image
     */
    private function generateWebP(ImageInterface $image, string $directory, string $filename): ?string
    {
        try {
            $webpPath = "{$directory}/{$filename}.webp";
            $disk = Storage::disk('public');
            $webpFullPath = $disk->path($webpPath);

            // Ensure directory exists
            $webpDir = dirname($webpFullPath);
            if (! is_dir($webpDir)) {
                mkdir($webpDir, 0755, true);
            }

            // Encode as WebP with quality 85
            $image->toWebp(85)->save($webpFullPath);

            return $webpPath;
        } catch (\Exception $e) {
            // WebP generation failed, return null
            return null;
        }
    }

    /**
     * Generate AVIF version (if supported)
     *
     * @param  mixed  $image  ImageInterface from intervention/image
     */
    private function generateAVIF(ImageInterface $image, string $directory, string $filename): ?string
    {
        try {
            $avifPath = "{$directory}/{$filename}.avif";
            $disk = Storage::disk('public');
            $avifFullPath = $disk->path($avifPath);

            // Ensure directory exists
            $avifDir = dirname($avifFullPath);
            if (! is_dir($avifDir)) {
                mkdir($avifDir, 0755, true);
            }

            // Encode as AVIF with quality 80
            $image->toAvif(80)->save($avifFullPath);

            return $avifPath;
        } catch (\Exception $e) {
            // AVIF generation failed, return null
            return null;
        }
    }

    /**
     * Generate responsive sizes (srcset)
     *
     * @param  mixed  $image  ImageInterface from intervention/image
     * @return array<string, string> Array of [width => path]
     */
    private function generateResponsiveSizes(
        string $fullPath,
        string $directory,
        string $filename,
        string $extension
    ): array {
        $sizes = [];
        $widths = [320, 640, 768, 1024, 1280, 1920]; // Common responsive breakpoints
        $disk = Storage::disk('public');

        foreach ($widths as $width) {
            try {
                $image = $this->imageManager->read($fullPath);
                $originalWidth = $image->width();

                // Don't upscale
                if ($width > $originalWidth) {
                    continue;
                }

                $sizePath = "{$directory}/{$filename}-{$width}w.{$extension}";
                $sizeFullPath = $disk->path($sizePath);

                // Ensure directory exists
                $sizeDir = dirname($sizeFullPath);
                if (! is_dir($sizeDir)) {
                    mkdir($sizeDir, 0755, true);
                }

                // Resize and save (no upscaling)
                $image->scaleDown($width)->save($sizeFullPath);

                $sizes[$width] = $sizePath;
            } catch (\Exception $e) {
                // Skip this size if generation fails
                continue;
            }
        }

        return $sizes;
    }

    /**
     * Generate responsive srcset string
     *
     * @param  string  $path  Original image path
     * @param  array  $variants  Variants array from generateVariants()
     * @return string Srcset string
     */
    public function generateSrcset(string $path, array $variants): string
    {
        $sizes = $variants['sizes'] ?? [];
        $srcset = [];

        foreach ($sizes as $width => $sizePath) {
            $url = Storage::url($sizePath);
            $srcset[] = "{$url} {$width}w";
        }

        return implode(', ', $srcset);
    }
}
