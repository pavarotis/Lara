<?php

declare(strict_types=1);

namespace App\Domain\Orders\Services;

use App\Domain\Customers\Models\Customer;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateOrderService
{
    public function __construct(
        private CalculateOrderTotalService $calculateTotal
    ) {}

    /**
     * Create a new order
     *
     * @param  array  $data  Order data (business_id, customer data, type, notes, delivery_address)
     * @param  array  $items  Cart items [['product_id' => x, 'name' => x, 'price' => x, 'quantity' => x], ...]
     */
    public function execute(array $data, array $items): Order
    {
        return DB::transaction(function () use ($data, $items) {
            // Create or find customer
            $customer = $this->getOrCreateCustomer($data);

            // Calculate totals
            $totals = $this->calculateTotal->execute($items);

            // Create order
            $order = Order::create([
                'business_id' => $data['business_id'],
                'customer_id' => $customer->id,
                'order_number' => $this->generateOrderNumber(),
                'status' => 'pending',
                'type' => $data['type'] ?? 'pickup',
                'subtotal' => $totals['subtotal'],
                'tax' => $totals['tax'],
                'total' => $totals['total'],
                'notes' => $data['notes'] ?? null,
                'delivery_address' => $data['delivery_address'] ?? null,
            ]);

            // Create order items
            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'product_price' => $item['price'],
                    'quantity' => $item['quantity'] ?? 1,
                    'subtotal' => $item['price'] * ($item['quantity'] ?? 1),
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            return $order->load('items', 'customer');
        });
    }

    private function getOrCreateCustomer(array $data): Customer
    {
        if (! empty($data['customer_id'])) {
            return Customer::findOrFail($data['customer_id']);
        }

        return Customer::create([
            'user_id' => $data['user_id'] ?? null,
            'name' => $data['customer_name'],
            'email' => $data['customer_email'] ?? null,
            'phone' => $data['customer_phone'] ?? null,
            'address' => $data['customer_address'] ?? null,
        ]);
    }

    private function generateOrderNumber(): string
    {
        $prefix = date('Ymd');
        $random = strtoupper(Str::random(6));

        return "{$prefix}-{$random}";
    }
}
