<?php

declare(strict_types=1);

namespace App\Domain\Marketing\Models;

use App\Domain\Businesses\Models\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'business_id',
        'code',
        'name',
        'description',
        'type',
        'discount',
        'minimum_amount',
        'uses_total',
        'uses_per_customer',
        'uses_count',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'discount' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'uses_total' => 'integer',
        'uses_per_customer' => 'integer',
        'uses_count' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isExpired(): bool
    {
        return $this->end_date && $this->end_date->isPast();
    }

    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->isExpired()) {
            return false;
        }

        if ($this->start_date && $this->start_date->isFuture()) {
            return false;
        }

        if ($this->uses_total && $this->uses_count >= $this->uses_total) {
            return false;
        }

        return true;
    }
}
