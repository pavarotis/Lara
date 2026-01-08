<?php

declare(strict_types=1);

namespace App\Domain\Themes\Models;

use App\Domain\Businesses\Models\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThemeToken extends Model
{
    protected $fillable = [
        'business_id',
        'preset_slug',
        'token_overrides',
        'header_variant',
        'footer_variant',
    ];

    protected $casts = [
        'token_overrides' => 'array',
    ];

    /**
     * Get the business that owns this theme token
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the theme preset for this token
     */
    public function preset(): BelongsTo
    {
        return $this->belongsTo(ThemePreset::class, 'preset_slug', 'slug');
    }
}
