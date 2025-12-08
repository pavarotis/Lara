<?php

declare(strict_types=1);

namespace App\Domain\Orders\Services;

class CalculateOrderTotalService
{
    private float $taxRate = 0.24; // 24% ΦΠΑ

    /**
     * Calculate totals from cart items
     *
     * @param  array  $items  [['product_id' => x, 'price' => x, 'quantity' => x], ...]
     */
    public function execute(array $items): array
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $subtotal += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
        }

        $tax = round($subtotal * $this->taxRate, 2);
        $total = round($subtotal + $tax, 2);

        return [
            'subtotal' => round($subtotal, 2),
            'tax' => $tax,
            'total' => $total,
        ];
    }

    public function setTaxRate(float $rate): self
    {
        $this->taxRate = $rate;

        return $this;
    }
}
