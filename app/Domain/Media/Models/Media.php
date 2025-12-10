<?php

declare(strict_types=1);

namespace App\Domain\Media\Models;

use App\Domain\Businesses\Models\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Media Model
 *
 * Represents media files (images, videos, documents) in the media library.
 */
class Media extends Model
{
    protected $fillable = [
        'business_id',
        'folder_id',
        'name',
        'path',
        'type',
        'mime',
        'size',
        'metadata',
        'created_by',
    ];

    protected $casts = [
        'metadata' => 'array',
        'size' => 'integer',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Scope: Filter by business
     */
    public function scopeOfBusiness($query, int $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    /**
     * Scope: Filter by folder
     */
    public function scopeInFolder($query, ?int $folderId)
    {
        if ($folderId === null) {
            return $query->whereNull('folder_id');
        }

        return $query->where('folder_id', $folderId);
    }

    /**
     * Scope: Filter by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Search by name
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    /**
     * Get the public URL for the media file
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/'.$this->path);
    }

    /**
     * Get the thumbnail URL (if exists)
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        $variants = $this->metadata['variants'] ?? [];
        $thumbPath = $variants['thumb'] ?? null;

        if ($thumbPath) {
            return asset('storage/'.$thumbPath);
        }

        // Fallback to original if no thumbnail
        if ($this->type === 'image') {
            return $this->url;
        }

        return null;
    }
}
