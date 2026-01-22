<?php

declare(strict_types=1);

namespace App\Domain\Sales\Models;

use App\Domain\Businesses\Models\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tax extends Model
{
    protected $fillable = [
        'business_id',
        'name',
        'type',
        'rate',
        'geo_zone_id',
        'priority',
        'is_active',
        'description',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'priority' => 'integer',
        'is_active' => 'boolean',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function geoZone(): BelongsTo
    {
        return $this->belongsTo(GeoZone::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
