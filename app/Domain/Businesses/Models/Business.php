<?php

declare(strict_types=1);

namespace App\Domain\Businesses\Models;

use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'logo',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get a specific setting value
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }

    /**
     * Check if delivery is enabled
     */
    public function isDeliveryEnabled(): bool
    {
        return $this->getSetting('delivery_enabled', false);
    }

    /**
     * Get the color theme
     */
    public function getTheme(): string
    {
        return $this->getSetting('color_theme', 'default');
    }

    /**
     * Get currency
     */
    public function getCurrency(): string
    {
        return $this->getSetting('currency', 'EUR');
    }

    /**
     * Get minimum order amount
     */
    public function getMinimumOrder(): float
    {
        return (float) $this->getSetting('minimum_order', 0);
    }
}

