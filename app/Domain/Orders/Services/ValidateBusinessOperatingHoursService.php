<?php

declare(strict_types=1);

namespace App\Domain\Orders\Services;

use App\Domain\Businesses\Models\Business;

class ValidateBusinessOperatingHoursService
{
    /**
     * Check if business is currently open for orders
     */
    public function execute(Business $business): bool
    {
        $settings = $business->settings ?? [];

        // If no operating hours defined, assume always open
        if (empty($settings['operating_hours'])) {
            return true;
        }

        $now = now();
        $dayOfWeek = strtolower($now->format('l')); // monday, tuesday, etc.
        $currentTime = $now->format('H:i');

        $hours = $settings['operating_hours'][$dayOfWeek] ?? null;

        // Closed this day
        if (! $hours || ($hours['closed'] ?? false)) {
            return false;
        }

        $open = $hours['open'] ?? '00:00';
        $close = $hours['close'] ?? '23:59';

        return $currentTime >= $open && $currentTime <= $close;
    }

    /**
     * Get operating hours message
     */
    public function getMessage(Business $business): string
    {
        if ($this->execute($business)) {
            return 'Δεχόμαστε παραγγελίες';
        }

        return 'Αυτή τη στιγμή δεν δεχόμαστε παραγγελίες';
    }
}
