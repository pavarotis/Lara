<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ProductExtra Model
 *
 * Custom extras/fields per product (e.g., size, color, addons).
 * Can be accessed in product templates.
 */
class ProductExtra extends Model
{
    protected $fillable = [
        'product_id',
        'key',
        'value',
        'type',
        'label',
        'sort_order',
    ];

    protected $casts = [
        'value' => 'string', // Will be cast based on type
        'sort_order' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('key');
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

    /**
     * Get display label (falls back to key if label is empty)
     */
    public function getDisplayLabel(): string
    {
        return $this->label ?: ucfirst(str_replace('_', ' ', $this->key));
    }
}
