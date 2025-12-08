<?php

declare(strict_types=1);

namespace App\Domain\Content\Models;

use App\Domain\Businesses\Models\Business;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Content Model (Skeleton)
 *
 * Full implementation will be in Sprint 1
 */
class Content extends Model
{
    protected $fillable = [
        'business_id',
        'type',
        'slug',
        'title',
        'body_json',
        'meta',
        'status',
        'published_at',
        'created_by',
    ];

    protected $casts = [
        'body_json' => 'array',
        'meta' => 'array',
        'published_at' => 'datetime',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(ContentRevision::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
}
