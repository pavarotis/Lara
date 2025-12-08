<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Businesses\Models\Business;
use App\Domain\Catalog\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Display cart contents
     */
    public function index(): View
    {
        $cart = session('cart', []);
        $business = Business::active()->first();

        $cartItems = $this->getCartItems($cart);
        $totals = $this->calculateTotals($cartItems);

        return view('cart.index', [
            'cartItems' => $cartItems,
            'totals' => $totals,
            'business' => $business,
        ]);
    }

    /**
     * Add item to cart
     */
    public function add(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1|max:99',
        ]);

        $productId = $validated['product_id'];
        $quantity = $validated['quantity'] ?? 1;

        $product = Product::findOrFail($productId);

        if (! $product->is_available) {
            return response()->json(['error' => 'Product not available'], 422);
        }

        $cart = session('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'quantity' => $quantity,
            ];
        }

        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'message' => 'Added to cart',
            'cart_count' => $this->getCartCount($cart),
        ]);
    }

    /**
     * Update item quantity
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:0|max:99',
        ]);

        $productId = $validated['product_id'];
        $quantity = $validated['quantity'];

        $cart = session('cart', []);

        if ($quantity === 0) {
            unset($cart[$productId]);
        } elseif (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
        }

        session(['cart' => $cart]);

        $cartItems = $this->getCartItems($cart);
        $totals = $this->calculateTotals($cartItems);

        return response()->json([
            'success' => true,
            'cart_count' => $this->getCartCount($cart),
            'totals' => $totals,
        ]);
    }

    /**
     * Remove item from cart
     */
    public function remove(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
        ]);

        $cart = session('cart', []);
        unset($cart[$validated['product_id']]);
        session(['cart' => $cart]);

        $cartItems = $this->getCartItems($cart);
        $totals = $this->calculateTotals($cartItems);

        return response()->json([
            'success' => true,
            'cart_count' => $this->getCartCount($cart),
            'totals' => $totals,
        ]);
    }

    /**
     * Clear entire cart
     */
    public function clear(): JsonResponse
    {
        session()->forget('cart');

        return response()->json([
            'success' => true,
            'cart_count' => 0,
        ]);
    }

    /**
     * Get cart data for AJAX
     */
    public function data(): JsonResponse
    {
        $cart = session('cart', []);
        $cartItems = $this->getCartItems($cart);
        $totals = $this->calculateTotals($cartItems);

        return response()->json([
            'items' => array_values($cartItems),
            'totals' => $totals,
            'count' => $this->getCartCount($cart),
        ]);
    }

    private function getCartItems(array $cart): array
    {
        return $cart;
    }

    private function calculateTotals(array $items): array
    {
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $tax = $subtotal * 0.24; // 24% VAT
        $total = $subtotal + $tax;

        return [
            'subtotal' => round($subtotal, 2),
            'tax' => round($tax, 2),
            'total' => round($total, 2),
        ];
    }

    private function getCartCount(array $cart): int
    {
        return array_sum(array_column($cart, 'quantity'));
    }
}
