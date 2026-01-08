<?php

declare(strict_types=1);

namespace App\Domain\Api\Models;

use App\Domain\Businesses\Models\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;

class ApiKey extends Model
{
    protected $fillable = [
        'business_id',
        'name',
        'key',
        'secret',
        'scopes',
        'last_used_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'scopes' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'secret',
    ];

    /**
     * Get the business that owns this API key
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Check if API key is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if API key has a specific scope
     */
    public function hasScope(string $scope): bool
    {
        $scopes = $this->scopes ?? [];

        return in_array($scope, $scopes) || in_array('*', $scopes);
    }

    /**
     * Hash the secret before saving
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($apiKey) {
            if (! empty($apiKey->secret) && ! Hash::isHashed($apiKey->secret)) {
                $apiKey->secret = Hash::make($apiKey->secret);
            }
        });

        static::updating(function ($apiKey) {
            if ($apiKey->isDirty('secret') && ! Hash::isHashed($apiKey->secret)) {
                $apiKey->secret = Hash::make($apiKey->secret);
            }
        });
    }
}
