<?php

declare(strict_types=1);

namespace App\Domain\Orders\Services;

use App\Domain\Businesses\Models\Business;

class ValidateOrderService
{
    public function __construct(
        private ValidateBusinessOperatingHoursService $operatingHours
    ) {}

    /**
     * Validate if order can be placed
     * @return array ['valid' => bool, 'errors' => []]
     */
    public function execute(Business $business, array $data, array $items): array
    {
        $errors = [];
        $settings = $business->settings ?? [];

        // Check if business is open
        if (!$this->operatingHours->execute($business)) {
            $errors[] = 'Η επιχείρηση είναι κλειστή αυτή τη στιγμή.';
        }

        // Check if delivery is enabled (if delivery type selected)
        if (($data['type'] ?? 'pickup') === 'delivery') {
            if (!($settings['delivery_enabled'] ?? false)) {
                $errors[] = 'Η παράδοση δεν είναι διαθέσιμη.';
            }

            if (empty($data['delivery_address'])) {
                $errors[] = 'Η διεύθυνση παράδοσης είναι υποχρεωτική.';
            }
        }

        // Check minimum order amount
        $minAmount = $settings['minimum_order'] ?? 0;
        if ($minAmount > 0) {
            $total = array_sum(array_map(fn($item) => 
                ($item['price'] ?? 0) * ($item['quantity'] ?? 1), $items
            ));

            if ($total < $minAmount) {
                $errors[] = "Η ελάχιστη παραγγελία είναι €{$minAmount}.";
            }
        }

        // Check if cart has items
        if (empty($items)) {
            $errors[] = 'Το καλάθι είναι άδειο.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}

