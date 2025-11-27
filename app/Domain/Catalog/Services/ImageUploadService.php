<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    private string $disk = 'public';

    /**
     * Upload an image
     */
    public function upload(UploadedFile $file, string $folder = 'images'): string
    {
        $filename = $this->generateFilename($file);
        $path = $file->storeAs($folder, $filename, $this->disk);

        return $path;
    }

    /**
     * Upload product image
     */
    public function uploadProductImage(UploadedFile $file): string
    {
        return $this->upload($file, 'products');
    }

    /**
     * Upload category image
     */
    public function uploadCategoryImage(UploadedFile $file): string
    {
        return $this->upload($file, 'categories');
    }

    /**
     * Upload business logo
     */
    public function uploadBusinessLogo(UploadedFile $file): string
    {
        return $this->upload($file, 'logos');
    }

    /**
     * Delete an image
     */
    public function delete(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        return Storage::disk($this->disk)->delete($path);
    }

    /**
     * Replace an image (delete old, upload new)
     */
    public function replace(?string $oldPath, UploadedFile $newFile, string $folder = 'images'): string
    {
        $this->delete($oldPath);
        return $this->upload($newFile, $folder);
    }

    /**
     * Get the public URL for an image
     */
    public function getUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        return Storage::disk($this->disk)->url($path);
    }

    /**
     * Check if image exists
     */
    public function exists(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        return Storage::disk($this->disk)->exists($path);
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
     * Get allowed image extensions
     */
    public static function getAllowedExtensions(): array
    {
        return ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    }

    /**
     * Get validation rules for image upload
     */
    public static function getValidationRules(bool $required = false): array
    {
        $rules = [
            'image',
            'mimes:' . implode(',', self::getAllowedExtensions()),
            'max:2048', // 2MB max
        ];

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }
}

