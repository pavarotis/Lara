<?php

declare(strict_types=1);

namespace App\Domain\Content\Models;

use App\Domain\Businesses\Models\Business;
use App\Domain\Layouts\Models\Layout;
use App\Models\User;
use App\Support\ContentStatusHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Content Model
 *
 * Represents content entries (pages, articles, blocks) with block-based structure.
 */
class Content extends Model
{
    protected $fillable = [
        'business_id',
        'type',
        'layout_id',
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

    public function contentType(): BelongsTo
    {
        return $this->belongsTo(ContentType::class, 'type', 'slug');
    }

    public function layout(): BelongsTo
    {
        return $this->belongsTo(Layout::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', ContentStatusHelper::published())
            ->whereNotNull('published_at');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', ContentStatusHelper::draft());
    }

    public function scopeForBusiness($query, int $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', ContentStatusHelper::archived());
    }

    /**
     * Check if content is published
     */
    public function isPublished(): bool
    {
        return $this->status === ContentStatusHelper::published() && $this->published_at !== null;
    }

    /**
     * Check if content is draft
     */
    public function isDraft(): bool
    {
        return $this->status === ContentStatusHelper::draft();
    }

    /**
     * Publish the content
     */
    public function publish(): void
    {
        $this->update([
            'status' => ContentStatusHelper::published(),
            'published_at' => now(),
        ]);
    }

    /**
     * Archive the content
     */
    public function archive(): void
    {
        $this->update([
            'status' => ContentStatusHelper::archived(),
        ]);
    }
}
