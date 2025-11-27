<x-public-layout>
    <x-slot name="title">Cart - {{ config('app.name') }}</x-slot>

    <section class="py-12 bg-surface min-h-[60vh]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Your Cart</h1>
                <a href="{{ url('/menu') }}" class="text-primary hover:text-primary-700 text-sm font-medium flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                    </svg>
                    Continue Shopping
                </a>
            </div>

            @if(empty($cartItems))
                <!-- Empty Cart -->
                <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
                    <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Your cart is empty</h2>
                    <p class="text-gray-500 mb-6">Looks like you haven't added any items yet.</p>
                    <a href="{{ url('/menu') }}" class="inline-flex items-center px-6 py-3 bg-primary text-white font-medium rounded-lg hover:bg-primary-600 transition-colors">
                        Browse Menu
                    </a>
                </div>
            @else
                <div class="grid lg:grid-cols-3 gap-8">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2 space-y-4">
                        @foreach($cartItems as $productId => $item)
                            <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-4" data-product-id="{{ $productId }}">
                                <!-- Product Image Placeholder -->
                                <div class="w-20 h-20 bg-gray-100 rounded-lg flex-shrink-0 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>

                                <!-- Product Info -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-medium text-gray-900">{{ $item['name'] }}</h3>
                                    <p class="text-primary font-semibold">€{{ number_format($item['price'], 2) }}</p>
                                </div>

                                <!-- Quantity Controls -->
                                <div class="flex items-center gap-2">
                                    <button type="button" 
                                            class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors quantity-btn"
                                            data-action="decrease"
                                            data-product-id="{{ $productId }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <span class="w-8 text-center font-medium quantity-display">{{ $item['quantity'] }}</span>
                                    <button type="button" 
                                            class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors quantity-btn"
                                            data-action="increase"
                                            data-product-id="{{ $productId }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Item Total -->
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900 item-total">€{{ number_format($item['price'] * $item['quantity'], 2) }}</p>
                                </div>

                                <!-- Remove Button -->
                                <button type="button" 
                                        class="p-2 text-gray-400 hover:text-red-500 transition-colors remove-btn"
                                        data-product-id="{{ $productId }}"
                                        title="Remove">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-xl shadow-sm p-6 sticky top-24">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
                            
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="font-medium" id="subtotal">€{{ number_format($totals['subtotal'], 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">VAT (24%)</span>
                                    <span class="font-medium" id="tax">€{{ number_format($totals['tax'], 2) }}</span>
                                </div>
                                <div class="border-t pt-3 flex justify-between text-base">
                                    <span class="font-semibold text-gray-900">Total</span>
                                    <span class="font-bold text-primary text-lg" id="total">€{{ number_format($totals['total'], 2) }}</span>
                                </div>
                            </div>

                            <a href="{{ url('/checkout') }}" 
                               class="mt-6 w-full inline-flex items-center justify-center px-6 py-3 bg-primary text-white font-medium rounded-lg hover:bg-primary-600 transition-colors">
                                Proceed to Checkout
                                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>

                            <button type="button" id="clear-cart" class="mt-3 w-full text-center text-sm text-gray-500 hover:text-red-500 transition-colors">
                                Clear Cart
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Quantity buttons
            document.querySelectorAll('.quantity-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const productId = this.dataset.productId;
                    const action = this.dataset.action;
                    const container = document.querySelector(`[data-product-id="${productId}"]`);
                    const display = container.querySelector('.quantity-display');
                    let quantity = parseInt(display.textContent);

                    if (action === 'increase') quantity++;
                    else if (action === 'decrease') quantity = Math.max(0, quantity - 1);

                    updateCart(productId, quantity);
                });
            });

            // Remove buttons
            document.querySelectorAll('.remove-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const productId = this.dataset.productId;
                    removeFromCart(productId);
                });
            });

            // Clear cart
            document.getElementById('clear-cart')?.addEventListener('click', function() {
                if (confirm('Are you sure you want to clear your cart?')) {
                    fetch('/cart/clear', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        }
                    }).then(() => location.reload());
                }
            });

            function updateCart(productId, quantity) {
                fetch('/cart/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ product_id: productId, quantity: quantity })
                })
                .then(res => res.json())
                .then(data => {
                    if (quantity === 0) {
                        location.reload();
                    } else {
                        location.reload(); // Simple reload for now
                    }
                });
            }

            function removeFromCart(productId) {
                fetch('/cart/remove', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ product_id: productId })
                }).then(() => location.reload());
            }
        });
    </script>
    @endpush
</x-public-layout>

