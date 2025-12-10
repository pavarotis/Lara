<?php

declare(strict_types=1);

namespace App\Domain\Media\Models;

use App\Domain\Businesses\Models\Business;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * MediaFolder Model
 *
 * Represents folders in the media library with nested structure support.
 */
class MediaFolder extends Model
{
    protected $fillable = [
        'business_id',
        'parent_id',
        'name',
        'path',
        'created_by',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MediaFolder::class, 'parent_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(Media::class, 'folder_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope: Filter by business
     */
    public function scopeOfBusiness($query, int $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    /**
     * Scope: Root folders (no parent)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get the full folder path
     */
    public function getPath(): string
    {
        if ($this->path) {
            return $this->path;
        }

        // Build path from parent chain
        $path = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }

        return implode('/', $path);
    }
}
