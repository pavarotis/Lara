<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Businesses\Models\Business;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Services\CreateOrderService;
use App\Domain\Orders\Services\ValidateOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private CreateOrderService $createOrderService,
        private ValidateOrderService $validateOrderService
    ) {}

    /**
     * Show checkout form
     */
    public function show(): View|RedirectResponse
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('menu')
                ->with('error', 'Your cart is empty');
        }

        $business = Business::active()->first();
        $totals = $this->calculateTotals($cart);

        return view('checkout.index', [
            'cartItems' => $cart,
            'totals' => $totals,
            'business' => $business,
        ]);
    }

    /**
     * Process checkout and create order
     */
    public function store(Request $request): RedirectResponse
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('menu')
                ->with('error', 'Your cart is empty');
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'type' => 'required|in:pickup,delivery',
            'delivery_address' => 'required_if:type,delivery|nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $business = Business::active()->first();

        // Validate order rules
        $validation = $this->validateOrderService->execute($business, $validated, array_values($cart));

        if (! $validation['valid']) {
            return back()->withErrors(['order' => $validation['message']])->withInput();
        }

        // Prepare order data
        $orderData = [
            'business_id' => $business->id,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'] ?? null,
            'customer_phone' => $validated['customer_phone'],
            'type' => $validated['type'],
            'delivery_address' => $validated['delivery_address'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ];

        // Prepare items
        $items = array_values($cart);

        // Create order
        $order = $this->createOrderService->execute($orderData, $items);

        // Clear cart
        session()->forget('cart');

        return redirect()->route('checkout.success', $order->order_number);
    }

    /**
     * Show order confirmation
     */
    public function success(string $orderNumber): View
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['items', 'customer', 'business'])
            ->firstOrFail();

        return view('checkout.success', [
            'order' => $order,
        ]);
    }

    private function calculateTotals(array $items): array
    {
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $tax = $subtotal * 0.24;
        $total = $subtotal + $tax;

        return [
            'subtotal' => round($subtotal, 2),
            'tax' => round($tax, 2),
            'total' => round($total, 2),
        ];
    }
}
