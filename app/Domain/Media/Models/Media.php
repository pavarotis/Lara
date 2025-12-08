<?php

declare(strict_types=1);

namespace App\Domain\Media\Models;

use App\Domain\Businesses\Models\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Media Model (Skeleton)
 *
 * Full implementation will be in Sprint 2
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
}
