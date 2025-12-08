<?php

declare(strict_types=1);

namespace App\Domain\Settings\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Setting Model
 *
 * Global and per-business settings
 */
class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    // No cast for value - it's stored as string/JSON and casted in GetSettingsService based on type

    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    public function scopeKey($query, string $key)
    {
        return $query->where('key', $key);
    }
}
