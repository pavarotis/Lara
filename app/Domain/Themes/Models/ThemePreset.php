<?php

declare(strict_types=1);

namespace App\Domain\Themes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThemePreset extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'tokens',
        'default_modules',
        'default_header_variant',
        'default_footer_variant',
        'is_active',
    ];

    protected $casts = [
        'tokens' => 'array',
        'default_modules' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get businesses using this preset (through theme_tokens)
     */
    public function businesses(): HasMany
    {
        return $this->hasMany(ThemeToken::class, 'preset_slug', 'slug');
    }
}
