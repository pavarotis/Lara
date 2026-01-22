<?php

declare(strict_types=1);

namespace App\Domain\Vqmod\Services;

use Illuminate\Support\Facades\File;

class VqmodManagerService
{
    protected string $enabledPath;

    protected string $disabledPath;

    public function __construct()
    {
        $this->enabledPath = storage_path('app/vqmod/xml');
        $this->disabledPath = storage_path('app/vqmod/xml/disabled');

        // Create directories if they don't exist
        if (! File::exists($this->enabledPath)) {
            File::makeDirectory($this->enabledPath, 0755, true);
        }

        if (! File::exists($this->disabledPath)) {
            File::makeDirectory($this->disabledPath, 0755, true);
        }
    }

    /**
     * Get all vqmod files (enabled and disabled)
     */
    public function getAllFiles(): array
    {
        $files = [];

        // Get enabled files
        $enabledFiles = File::glob($this->enabledPath.'/*.xml');
        foreach ($enabledFiles as $file) {
            $files[] = [
                'name' => basename($file),
                'path' => $file,
                'enabled' => true,
                'size' => File::size($file),
                'modified' => File::lastModified($file),
            ];
        }

        // Get disabled files
        $disabledFiles = File::glob($this->disabledPath.'/*.xml');
        foreach ($disabledFiles as $file) {
            $files[] = [
                'name' => basename($file),
                'path' => $file,
                'enabled' => false,
                'size' => File::size($file),
                'modified' => File::lastModified($file),
            ];
        }

        // Sort by name
        usort($files, fn ($a, $b) => strcmp($a['name'], $b['name']));

        return $files;
    }

    /**
     * Enable a vqmod file (move from disabled to enabled)
     */
    public function enableFile(string $filename): bool
    {
        $disabledFile = $this->disabledPath.'/'.$filename;
        $enabledFile = $this->enabledPath.'/'.$filename;

        if (! File::exists($disabledFile)) {
            return false;
        }

        return File::move($disabledFile, $enabledFile);
    }

    /**
     * Disable a vqmod file (move from enabled to disabled)
     */
    public function disableFile(string $filename): bool
    {
        $enabledFile = $this->enabledPath.'/'.$filename;
        $disabledFile = $this->disabledPath.'/'.$filename;

        if (! File::exists($enabledFile)) {
            return false;
        }

        return File::move($enabledFile, $disabledFile);
    }

    /**
     * Delete a vqmod file
     */
    public function deleteFile(string $filename): bool
    {
        $enabledFile = $this->enabledPath.'/'.$filename;
        $disabledFile = $this->disabledPath.'/'.$filename;

        if (File::exists($enabledFile)) {
            return File::delete($enabledFile);
        }

        if (File::exists($disabledFile)) {
            return File::delete($disabledFile);
        }

        return false;
    }

    /**
     * Get file content
     */
    public function getFileContent(string $filename): ?string
    {
        $enabledFile = $this->enabledPath.'/'.$filename;
        $disabledFile = $this->disabledPath.'/'.$filename;

        if (File::exists($enabledFile)) {
            return File::get($enabledFile);
        }

        if (File::exists($disabledFile)) {
            return File::get($disabledFile);
        }

        return null;
    }

    /**
     * Save file content
     */
    public function saveFileContent(string $filename, string $content): bool
    {
        $enabledFile = $this->enabledPath.'/'.$filename;
        $disabledFile = $this->disabledPath.'/'.$filename;

        if (File::exists($enabledFile)) {
            return File::put($enabledFile, $content) !== false;
        }

        if (File::exists($disabledFile)) {
            return File::put($disabledFile, $content) !== false;
        }

        return false;
    }

    /**
     * Check if file exists
     */
    public function fileExists(string $filename): bool
    {
        return File::exists($this->enabledPath.'/'.$filename) ||
               File::exists($this->disabledPath.'/'.$filename);
    }
}
