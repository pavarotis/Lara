<?php

declare(strict_types=1);

namespace App\Domain\Modules\Models;

use App\Domain\Businesses\Models\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ModuleInstance Model
 *
 * Represents a module instance (reusable or page-specific).
 */
class ModuleInstance extends Model
{
    protected $fillable = [
        'business_id',
        'type',
        'name',
        'settings',
        'style',
        'width_mode',
        'enabled',
    ];

    protected $casts = [
        'settings' => 'array',
        'style' => 'array',
        'enabled' => 'boolean',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ContentModuleAssignment::class);
    }

    public function scopeForBusiness($query, int $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    public function scopeReusable($query)
    {
        return $query->whereNotNull('name');
    }

    /**
     * Check if module is reusable
     */
    public function isReusable(): bool
    {
        return $this->name !== null;
    }

    /**
     * Get a setting value
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }
}
