<x-public-layout>
    <x-slot name="title">Order Confirmed - {{ config('app.name') }}</x-slot>

    <section class="py-16 bg-surface min-h-[60vh]">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <!-- Success Icon -->
            <div class="w-20 h-20 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-2">Order Confirmed!</h1>
            <p class="text-gray-600 mb-8">Thank you for your order. We've received it and will start preparing it shortly.</p>

            <!-- Order Details Card -->
            <div class="bg-white rounded-2xl shadow-sm p-6 text-left mb-8">
                <div class="flex items-center justify-between border-b pb-4 mb-4">
                    <div>
                        <p class="text-sm text-gray-500">Order Number</p>
                        <p class="text-xl font-bold text-primary">{{ $order->order_number }}</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                        @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                        @elseif($order->status === 'preparing') bg-purple-100 text-purple-800
                        @elseif($order->status === 'ready') bg-green-100 text-green-800
                        @elseif($order->status === 'completed') bg-gray-100 text-gray-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>

                <!-- Order Info -->
                <div class="grid sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-500">Order Type</p>
                        <p class="font-medium text-gray-900 flex items-center gap-2">
                            @if($order->type === 'pickup')
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                Pickup
                            @else
                                <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                </svg>
                                Delivery
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Placed At</p>
                        <p class="font-medium text-gray-900">{{ $order->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>

                @if($order->type === 'delivery' && $order->delivery_address)
                    <div class="mb-6">
                        <p class="text-sm text-gray-500">Delivery Address</p>
                        <p class="font-medium text-gray-900">{{ $order->delivery_address }}</p>
                    </div>
                @endif

                <!-- Order Items -->
                <div class="border-t pt-4">
                    <p class="text-sm font-medium text-gray-700 mb-3">Order Items</p>
                    <div class="space-y-2">
                        @foreach($order->items as $item)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">{{ $item->product_name }} × {{ $item->quantity }}</span>
                                <span class="font-medium">€{{ number_format($item->subtotal, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Totals -->
                <div class="border-t mt-4 pt-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">€{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">VAT (24%)</span>
                        <span class="font-medium">€{{ number_format($order->tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-base font-bold">
                        <span class="text-gray-900">Total</span>
                        <span class="text-primary">€{{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url('/menu') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-primary text-white font-medium rounded-lg hover:bg-primary-600 transition-colors">
                    Continue Shopping
                </a>
                <a href="{{ url('/') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Back to Home
                </a>
            </div>

            <!-- Contact Info -->
            <p class="mt-8 text-sm text-gray-500">
                Questions about your order? Contact us at 
                <a href="tel:+302101234567" class="text-primary hover:underline">+30 210 1234567</a>
            </p>
        </div>
    </section>
</x-public-layout>

