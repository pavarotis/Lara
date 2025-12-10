<?php

declare(strict_types=1);

namespace App\Domain\Content\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ContentRevision Model
 *
 * Represents version history for content entries.
 */
class ContentRevision extends Model
{
    protected $fillable = [
        'content_id',
        'body_json',
        'meta',
        'user_id',
    ];

    protected $casts = [
        'body_json' => 'array',
        'meta' => 'array',
    ];

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Restore content to this revision state
     */
    public function restore(): bool
    {
        $content = $this->content;

        if (! $content) {
            return false;
        }

        return $content->update([
            'body_json' => $this->body_json,
            'meta' => $this->meta,
        ]);
    }
}
