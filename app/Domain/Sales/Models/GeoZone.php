<?php

declare(strict_types=1);

namespace App\Domain\Sales\Models;

use App\Domain\Businesses\Models\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeoZone extends Model
{
    protected $fillable = [
        'business_id',
        'name',
        'description',
        'zones',
        'is_active',
    ];

    protected $casts = [
        'zones' => 'array',
        'is_active' => 'boolean',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(Tax::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
