<x-filament-panels::page>
    @push('styles')
        @vite('resources/css/reports.css')
    @endpush

    @php
        $stats = $this->getStats();
        $recentOrders = $this->getRecentOrders();
        $ordersByStatus = $this->getOrdersByStatus();
    @endphp

    <div class="space-y-6">
        <!-- Summary Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Orders -->
            <div class="bg-white dark:bg-dark-bg rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Orders</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_orders']) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Today: {{ $stats['today_orders'] }} | This Month: {{ $stats['month_orders'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white dark:bg-dark-bg rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">€{{ number_format($stats['total_revenue'], 2) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Today: €{{ number_format($stats['today_revenue'], 2) }} | Month: €{{ number_format($stats['month_revenue'], 2) }}
                        </p>
                    </div>
                    <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Customers -->
            <div class="bg-white dark:bg-dark-bg rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Customers</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_customers']) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            New Today: {{ $stats['new_customers_today'] }} | This Month: {{ $stats['new_customers_month'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Products -->
            <div class="bg-white dark:bg-dark-bg rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Products</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_products']) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Available: {{ $stats['available_products'] }} | Featured: {{ $stats['featured_products'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Status Overview -->
        <div class="bg-white dark:bg-dark-bg rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Status Overview</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['pending_orders'] }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Pending</p>
                </div>
                <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['delivered_orders'] }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Delivered</p>
                </div>
                <div class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['cancelled_orders'] }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Cancelled</p>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <p class="text-2xl font-bold text-gray-700 dark:text-gray-300">{{ $stats['total_orders'] - $stats['pending_orders'] - $stats['delivered_orders'] - $stats['cancelled_orders'] }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Other</p>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white dark:bg-dark-bg rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Orders</h3>
                <a href="{{ route('filament.admin.resources.sales-orders.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                    View All →
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">Order #</th>
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">Customer</th>
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">Status</th>
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">Total</th>
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($recentOrders as $order)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $order->order_number }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $order->customer->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if($order->status === 'pending') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                                        @elseif($order->status === 'delivered') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                                        @elseif($order->status === 'cancelled') bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300
                                        @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">€{{ number_format($order->total, 2) }}</td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $order->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No orders found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="bg-white dark:bg-dark-bg rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Links</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('filament.admin.resources.sales-orders.index') }}" class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition group">
                    <div class="text-center">
                        <svg class="w-6 h-6 mx-auto mb-2 text-gray-600 dark:text-gray-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">Orders</p>
                    </div>
                </a>
                <a href="{{ route('filament.admin.resources.customers.index') }}" class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition group">
                    <div class="text-center">
                        <svg class="w-6 h-6 mx-auto mb-2 text-gray-600 dark:text-gray-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">Customers</p>
                    </div>
                </a>
                <a href="{{ route('filament.admin.resources.catalog-products.index') }}" class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition group">
                    <div class="text-center">
                        <svg class="w-6 h-6 mx-auto mb-2 text-gray-600 dark:text-gray-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">Products</p>
                    </div>
                </a>
                <a href="{{ route('filament.admin.pages.statistics') }}" class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition group">
                    <div class="text-center">
                        <svg class="w-6 h-6 mx-auto mb-2 text-gray-600 dark:text-gray-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">Statistics</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-filament-panels::page>
