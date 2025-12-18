<?php

declare(strict_types=1);

namespace App\Domain\Modules\Models;

use App\Domain\Content\Models\Content;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ContentModuleAssignment Model
 *
 * Junction table linking content to module instances in specific regions.
 */
class ContentModuleAssignment extends Model
{
    protected $fillable = [
        'content_id',
        'module_instance_id',
        'region',
        'sort_order',
    ];

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }

    public function moduleInstance(): BelongsTo
    {
        return $this->belongsTo(ModuleInstance::class);
    }

    public function scopeForContent($query, int $contentId)
    {
        return $query->where('content_id', $contentId);
    }

    public function scopeForRegion($query, string $region)
    {
        return $query->where('region', $region);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
