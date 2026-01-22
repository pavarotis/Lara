<?php

declare(strict_types=1);

namespace App\Domain\Seo\Models;

use App\Domain\Businesses\Models\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Redirect extends Model
{
    protected $fillable = [
        'business_id',
        'from_url',
        'to_url',
        'type',
        'is_active',
        'hits',
        'last_hit_at',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'hits' => 'integer',
        'last_hit_at' => 'datetime',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForBusiness($query, ?int $businessId)
    {
        return $query->where(function ($q) use ($businessId) {
            if ($businessId) {
                $q->where('business_id', $businessId)
                    ->orWhereNull('business_id');
            } else {
                $q->whereNull('business_id');
            }
        });
    }

    public function incrementHits(): void
    {
        $this->increment('hits');
        $this->update(['last_hit_at' => now()]);
    }
}
