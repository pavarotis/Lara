<?php

declare(strict_types=1);

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ContentType Model (Skeleton)
 *
 * Full implementation will be in Sprint 1
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
}
