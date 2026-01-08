<?php

declare(strict_types=1);

namespace App\Domain\Layouts\Models;

use App\Domain\Businesses\Models\Business;
use App\Domain\Content\Models\Content;
use App\Domain\Modules\Models\ModuleInstance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Layout Model
 *
 * Represents page layouts with regions (OpenCart-like system).
 */
class Layout extends Model
{
    protected $fillable = [
        'business_id',
        'name',
        'type',
        'regions',
        'is_default',
        'compiled_html',
        'assets_manifest',
        'critical_css',
        'compiled_at',
    ];

    protected $casts = [
        'regions' => 'array',
        'assets_manifest' => 'array',
        'is_default' => 'boolean',
        'compiled_at' => 'datetime',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function contents(): HasMany
    {
        return $this->hasMany(Content::class);
    }

    public function defaultModules(): HasMany
    {
        return $this->hasMany(ModuleInstance::class, 'business_id', 'business_id')
            ->whereNull('name'); // ModuleInstance where name is null (default modules)
    }

    public function scopeForBusiness($query, int $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Check if layout has a specific region
     */
    public function hasRegion(string $region): bool
    {
        return in_array($region, $this->getRegions(), true);
    }

    /**
     * Get regions array
     */
    public function getRegions(): array
    {
        return $this->regions ?? [];
    }
}
