<x-public-layout>
    <x-slot name="title">Checkout - {{ config('app.name') }}</x-slot>

    <section class="py-12 bg-surface min-h-[60vh]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <nav class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                    <a href="{{ url('/cart') }}" class="hover:text-primary">Cart</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-gray-900">Checkout</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900">Checkout</h1>
            </div>

            <!-- Error Messages -->
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ url('/checkout') }}" method="POST">
                @csrf
                
                <div class="grid lg:grid-cols-3 gap-8">
                    <!-- Checkout Form -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Contact Information -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h2>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                                    <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" required
                                           class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
                                           placeholder="John Doe">
                                </div>

                                <div class="grid sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                                        <input type="tel" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}" required
                                               class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
                                               placeholder="+30 69x xxx xxxx">
                                    </div>
                                    <div>
                                        <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                        <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email') }}"
                                               class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
                                               placeholder="john@example.com">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Type -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Type</h2>
                            
                            <div class="grid sm:grid-cols-2 gap-4">
                                <label class="relative flex items-center p-4 border-2 rounded-xl cursor-pointer transition-colors hover:border-primary-200 has-[:checked]:border-primary has-[:checked]:bg-primary-50">
                                    <input type="radio" name="type" value="pickup" class="sr-only" {{ old('type', 'pickup') === 'pickup' ? 'checked' : '' }}>
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">Pickup</p>
                                            <p class="text-sm text-gray-500">Pick up at store</p>
                                        </div>
                                    </div>
                                </label>

                                <label class="relative flex items-center p-4 border-2 rounded-xl cursor-pointer transition-colors hover:border-primary-200 has-[:checked]:border-primary has-[:checked]:bg-primary-50">
                                    <input type="radio" name="type" value="delivery" class="sr-only" {{ old('type') === 'delivery' ? 'checked' : '' }}>
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-accent-100 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">Delivery</p>
                                            <p class="text-sm text-gray-500">Deliver to address</p>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Delivery Address (shown when delivery selected) -->
                            <div id="delivery-address" class="mt-4 hidden">
                                <label for="delivery_address" class="block text-sm font-medium text-gray-700 mb-1">Delivery Address *</label>
                                <textarea name="delivery_address" id="delivery_address" rows="2"
                                          class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
                                          placeholder="Street, Number, City, Postal Code">{{ old('delivery_address') }}</textarea>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Notes</h2>
                            <textarea name="notes" id="notes" rows="3"
                                      class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
                                      placeholder="Special instructions, allergies, etc.">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-xl shadow-sm p-6 sticky top-24">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
                            
                            <!-- Items -->
                            <div class="space-y-3 mb-4 max-h-48 overflow-y-auto">
                                @foreach($cartItems as $item)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">{{ $item['name'] }} × {{ $item['quantity'] }}</span>
                                        <span class="font-medium">€{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="border-t pt-4 space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="font-medium">€{{ number_format($totals['subtotal'], 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">VAT (24%)</span>
                                    <span class="font-medium">€{{ number_format($totals['tax'], 2) }}</span>
                                </div>
                                <div class="border-t pt-3 flex justify-between text-base">
                                    <span class="font-semibold text-gray-900">Total</span>
                                    <span class="font-bold text-primary text-lg">€{{ number_format($totals['total'], 2) }}</span>
                                </div>
                            </div>

                            <button type="submit" 
                                    class="mt-6 w-full inline-flex items-center justify-center px-6 py-3 bg-primary text-white font-medium rounded-lg hover:bg-primary-600 transition-colors">
                                Place Order
                                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>

                            <p class="mt-4 text-xs text-gray-500 text-center">
                                By placing your order, you agree to our terms and conditions.
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeInputs = document.querySelectorAll('input[name="type"]');
            const deliveryAddress = document.getElementById('delivery-address');

            function toggleDeliveryAddress() {
                const selected = document.querySelector('input[name="type"]:checked');
                if (selected && selected.value === 'delivery') {
                    deliveryAddress.classList.remove('hidden');
                } else {
                    deliveryAddress.classList.add('hidden');
                }
            }

            typeInputs.forEach(input => {
                input.addEventListener('change', toggleDeliveryAddress);
            });

            toggleDeliveryAddress();
        });
    </script>
    @endpush
</x-public-layout>

