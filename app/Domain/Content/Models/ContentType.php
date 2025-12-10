<?php

declare(strict_types=1);

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ContentType Model
 *
 * Represents content type definitions (page, article, block).
 */
class ContentType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'field_definitions',
    ];

    protected $casts = [
        'field_definitions' => 'array',
    ];

    public function contents(): HasMany
    {
        return $this->hasMany(Content::class, 'type', 'slug');
    }

    /**
     * Get field definitions as array
     */
    public function getFieldDefinitions(): array
    {
        return $this->field_definitions ?? [];
    }
}
