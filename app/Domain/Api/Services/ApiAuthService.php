<?php

declare(strict_types=1);

namespace App\Domain\Api\Services;

use App\Domain\Api\Models\ApiKey;
use Illuminate\Support\Facades\Hash;

class ApiAuthService
{
    /**
     * Authenticate API key and secret
     */
    public function authenticate(string $key, string $secret): ?ApiKey
    {
        $apiKey = ApiKey::where('key', $key)
            ->where('is_active', true)
            ->first();

        if (! $apiKey) {
            return null;
        }

        // Verify secret
        if (! Hash::check($secret, $apiKey->secret)) {
            return null;
        }

        // Check expiration
        if ($apiKey->isExpired()) {
            return null;
        }

        // Update last used
        $apiKey->update(['last_used_at' => now()]);

        return $apiKey;
    }

    /**
     * Check if API key has a specific scope
     */
    public function hasScope(ApiKey $apiKey, string $scope): bool
    {
        return $apiKey->hasScope($scope);
    }
}
