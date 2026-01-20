<?php

declare(strict_types=1);

namespace App\Domain\Variables\Models;

use App\Domain\Businesses\Models\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Variable Model
 *
 * Custom variables per business (site-wide settings).
 * Can be accessed in templates via helper.
 */
class Variable extends Model
{
    protected $fillable = [
        'business_id',
        'key',
        'value',
        'type',
        'description',
    ];

    protected $casts = [
        'value' => 'string', // Will be cast based on type
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function scopeForBusiness($query, int $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    /**
     * Get typed value based on type
     */
    public function getTypedValue(): mixed
    {
        return match ($this->type) {
            'number' => is_numeric($this->value) ? (str_contains($this->value, '.') ? (float) $this->value : (int) $this->value) : 0,
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($this->value, true) ?? [],
            default => $this->value,
        };
    }

    /**
     * Set value with type validation
     */
    public function setTypedValue(mixed $value): void
    {
        $this->value = match ($this->type) {
            'number' => (string) $value,
            'boolean' => $value ? '1' : '0',
            'json' => is_string($value) ? $value : json_encode($value),
            default => (string) $value,
        };
    }
}
