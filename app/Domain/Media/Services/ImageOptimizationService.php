<?php

declare(strict_types=1);

namespace App\Domain\Media\Services;

use Illuminate\Support\Facades\Storage;

/**
 * Image Optimization Service
 *
 * Generates optimized image variants (WebP, AVIF, responsive sizes).
 *
 * NOTE: This service requires the intervention/image package.
 * Install it with: composer require intervention/image
 *
 * For now, this is a placeholder implementation. Once intervention/image
 * is installed, uncomment the ImageManager usage below.
 */
class ImageOptimizationService
{
    // TODO: Uncomment when intervention/image is installed
    // private ImageManager $imageManager;
    //
    // public function __construct()
    // {
    //     // Use GD driver (can be changed to Imagick if available)
    //     $this->imageManager = new ImageManager(new Driver);
    // }

    public function __construct()
    {
        // Placeholder - requires intervention/image package
    }

    /**
     * Generate optimized variants (WebP, AVIF, responsive sizes)
     *
     * @param  string  $path  Original image path (relative to storage)
     * @return array{webp: string|null, avif: string|null, sizes: array}
     */
    public function generateVariants(string $path): array
    {
        // TODO: Implement when intervention/image is installed
        // For now, return empty variants
        return [
            'webp' => null,
            'avif' => null,
            'sizes' => [],
        ];

        // Uncomment when intervention/image is installed:
        /*
        $fullPath = Storage::path($path);

        if (! file_exists($fullPath)) {
            throw new \RuntimeException("Image not found: {$path}");
        }

        $pathInfo = pathinfo($path);
        $directory = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];
        $extension = $pathInfo['extension'] ?? 'jpg';

        // Load original image
        $image = $this->imageManager->read($fullPath);

        // Generate WebP
        $webpPath = $this->generateWebP($image, $directory, $filename);

        // Generate AVIF (if supported)
        $avifPath = $this->generateAVIF($image, $directory, $filename);

        // Generate responsive sizes
        $sizes = $this->generateResponsiveSizes($image, $directory, $filename, $extension);

        return [
            'webp' => $webpPath,
            'avif' => $avifPath,
            'sizes' => $sizes,
        ];
        */
    }

    /**
     * Generate WebP version
     *
     * @param  mixed  $image  ImageInterface from intervention/image
     */
    private function generateWebP($image, string $directory, string $filename): ?string
    {
        try {
            $webpPath = "{$directory}/{$filename}.webp";
            $webpFullPath = Storage::path($webpPath);

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
    private function generateAVIF($image, string $directory, string $filename): ?string
    {
        // AVIF support requires PHP 8.1+ and imagick extension
        if (! function_exists('imageavif')) {
            return null;
        }

        try {
            $avifPath = "{$directory}/{$filename}.avif";
            $avifFullPath = Storage::path($avifPath);

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
    private function generateResponsiveSizes($image, string $directory, string $filename, string $extension): array
    {
        $sizes = [];
        $widths = [320, 640, 768, 1024, 1280, 1920]; // Common responsive breakpoints

        $originalWidth = $image->width();
        $originalHeight = $image->height();

        foreach ($widths as $width) {
            // Don't upscale
            if ($width > $originalWidth) {
                continue;
            }

            try {
                $height = (int) (($width / $originalWidth) * $originalHeight);
                $sizePath = "{$directory}/{$filename}-{$width}w.{$extension}";
                $sizeFullPath = Storage::path($sizePath);

                // Ensure directory exists
                $sizeDir = dirname($sizeFullPath);
                if (! is_dir($sizeDir)) {
                    mkdir($sizeDir, 0755, true);
                }

                // Resize and save
                $resized = $image->scale($width, $height);
                $resized->save($sizeFullPath);

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
