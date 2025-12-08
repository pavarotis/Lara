<?php

declare(strict_types=1);

namespace App\Domain\Content\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ContentRevision Model (Skeleton)
 *
 * Full implementation will be in Sprint 1
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
}
