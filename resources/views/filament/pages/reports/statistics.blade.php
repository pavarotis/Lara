<x-filament-panels::page>
    @push('styles')
        @vite('resources/css/reports.css')
    @endpush

    @php
        $salesStats = $this->getSalesStatistics();
        $dailySales = $this->getDailySalesData();
        $ordersByStatus = $this->getOrdersByStatus();
        $topProducts = $this->getTopProducts();
        $customerStats = $this->getCustomerStatistics();
        $productStats = $this->getProductStatistics();
    @endphp

    <div class="space-y-4">
        <!-- Top Row: Sales Statistics & Comparison -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Sales Statistics - Takes 2 columns -->
            <div class="lg:col-span-2 rounded-lg shadow p-5 bg-white dark:bg-dark-bg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Sales Statistics</h3>
                    <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between mb-1.5">
                            <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Today</p>
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-xl font-bold text-gray-900 dark:text-white mb-0.5
                                @if(isset($salesStats['today']['orders']) && $salesStats['today']['orders'] === null)
                                    text-red-500
                                @endif
                        ">
                            {{ isset($salesStats['today']['orders']) ? $salesStats['today']['orders'] : 'Error' }}
                        </p>
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-300
                                @if(isset($salesStats['today']['revenue']) && $salesStats['today']['revenue'] === null)
                                    text-red-500
                                @endif
                        ">
                            €{{ isset($salesStats['today']['revenue']) ? number_format($salesStats['today']['revenue'], 2) : 'Error' }}
                        </p>
                    </div>
                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between mb-1.5">
                            <p class="text-xs font-medium text-gray-700 dark:text-gray-300">This Week</p>
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <p class="text-xl font-bold text-gray-900 dark:text-white mb-0.5
                                @if(isset($salesStats['this_week']['orders']) && $salesStats['this_week']['orders'] === null)
                                    text-red-500
                                @endif
                        ">
                            {{ isset($salesStats['this_week']['orders']) ? $salesStats['this_week']['orders'] : 'Error' }}
                        </p>
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-300
                                @if(isset($salesStats['this_week']['revenue']) && $salesStats['this_week']['revenue'] === null)
                                    text-red-500
                                @endif
                        ">
                            €{{ isset($salesStats['this_week']['revenue']) ? number_format($salesStats['this_week']['revenue'], 2) : 'Error' }}
                        </p>
                    </div>
                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between mb-1.5">
                            <p class="text-xs font-medium text-gray-700 dark:text-gray-300">This Month</p>
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <p class="text-xl font-bold text-gray-900 dark:text-white mb-0.5
                                @if(isset($salesStats['this_month']['orders']) && $salesStats['this_month']['orders'] === null)
                                    text-red-500
                                @endif
                        ">
                            {{ isset($salesStats['this_month']['orders']) ? $salesStats['this_month']['orders'] : 'Error' }}
                        </p>
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-300
                                @if(isset($salesStats['this_month']['revenue']) && $salesStats['this_month']['revenue'] === null)
                                    text-red-500
                                @endif
                        ">
                            €{{ isset($salesStats['this_month']['revenue']) ? number_format($salesStats['this_month']['revenue'], 2) : 'Error' }}
                        </p>
                    </div>
                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between mb-1.5">
                            <p class="text-xs font-medium text-gray-700 dark:text-gray-300">This Year</p>
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-xl font-bold text-gray-900 dark:text-white mb-0.5
                                @if(isset($salesStats['this_year']['orders']) && $salesStats['this_year']['orders'] === null)
                                    text-red-500
                                @endif
                        ">
                            {{ isset($salesStats['this_year']['orders']) ? $salesStats['this_year']['orders'] : 'Error' }}
                        </p>
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-300
                                @if(isset($salesStats['this_year']['revenue']) && $salesStats['this_year']['revenue'] === null)
                                    text-red-500
                                @endif
                        ">
                            €{{ isset($salesStats['this_year']['revenue']) ? number_format($salesStats['this_year']['revenue'], 2) : 'Error' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Comparison Cards - Takes 1 column -->
            <div class="space-y-3">
                <div class="bg-white dark:bg-dark-bg rounded-lg shadow p-4 border-l-4 border-gray-300 dark:border-gray-600">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Week Comparison</p>
                        <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </div>
                    <div class="flex items-baseline mb-1">
                        <p class="text-xl font-bold text-gray-900 dark:text-white
                            @if(!isset($salesStats['this_week']['orders']) || !isset($salesStats['last_week']['orders']))
                                text-red-500
                            @endif">
                            {{ isset($salesStats['this_week']['orders']) ? $salesStats['this_week']['orders'] : 'Error' }}
                        </p>
                        <p class="ml-1.5 text-xs text-gray-700 dark:text-gray-300">this week</p>
                    </div>
                    <p class="text-xs text-gray-700 dark:text-gray-300 mb-2
                        @if(!isset($salesStats['last_week']['orders']))
                            text-red-500
                        @endif
                    ">
                        {{ isset($salesStats['last_week']['orders']) ? $salesStats['last_week']['orders'] : 'Error' }} last week
                    </p>
                    @php
                        if (isset($salesStats['last_week']['orders']) && $salesStats['last_week']['orders'] > 0) {
                            $weekChange = (($salesStats['this_week']['orders'] - $salesStats['last_week']['orders']) / $salesStats['last_week']['orders']) * 100;
                        } else {
                            $weekChange = 0;
                        }
                    @endphp
                    <div class="flex items-center">
                        @if($weekChange >= 0)
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                            </svg>
                        @endif
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                            {{ $weekChange >= 0 ? '+' : '' }}{{ number_format($weekChange, 1) }}%
                        </p>
                    </div>
                </div>

                <div class="bg-white dark:bg-dark-bg rounded-lg shadow p-4 border-l-4 border-gray-300 dark:border-gray-600">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Month Comparison</p>
                        <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </div>
                    <div class="flex items-baseline mb-1">
                        <p class="text-xl font-bold text-gray-900 dark:text-white
                            @if(!isset($salesStats['this_month']['orders']) || !isset($salesStats['last_month']['orders']))
                                text-red-500
                            @endif">
                            {{ isset($salesStats['this_month']['orders']) ? $salesStats['this_month']['orders'] : 'Error' }}
                        </p>
                        <p class="ml-1.5 text-xs text-gray-700 dark:text-gray-300">this month</p>
                    </div>
                    <p class="text-xs text-gray-700 dark:text-gray-300 mb-2
                        @if(!isset($salesStats['last_month']['orders']))
                            text-red-500
                        @endif
                    ">
                        {{ isset($salesStats['last_month']['orders']) ? $salesStats['last_month']['orders'] : 'Error' }} last month
                    </p>
                    @php
                        if (isset($salesStats['last_month']['orders']) && $salesStats['last_month']['orders'] > 0) {
                            $monthChange = (($salesStats['this_month']['orders'] - $salesStats['last_month']['orders']) / $salesStats['last_month']['orders']) * 100;
                        } else {
                            $monthChange = 0;
                        }
                    @endphp
                    <div class="flex items-center">
                        @if($monthChange >= 0)
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                            </svg>
                        @endif
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                            {{ $monthChange >= 0 ? '+' : '' }}{{ number_format($monthChange, 1) }}%
                        </p>
                    </div>
                </div>

                <div class="bg-white dark:bg-dark-bg rounded-lg shadow p-4 border-l-4 border-gray-300 dark:border-gray-600">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Avg Order Value</p>
                        <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    @php
                        if (isset($salesStats['this_month']['orders']) && $salesStats['this_month']['orders'] > 0) {
                            $avgOrderValue = $salesStats['this_month']['revenue'] / $salesStats['this_month']['orders'];
                        } else {
                            $avgOrderValue = null;
                        }
                    @endphp
                    <p class="text-xl font-bold text-gray-900 dark:text-white mb-0.5 @if($avgOrderValue === null) text-red-500 @endif">
                        @if($avgOrderValue !== null)
                            €{{ number_format($avgOrderValue, 2) }}
                        @else
                            Error
                        @endif
                    </p>
                    <p class="text-xs text-gray-700 dark:text-gray-300">this month</p>
                </div>
            </div>
        </div>

        <!-- Second Row: Orders by Status & Customer/Product Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Orders by Status -->
            <div class="lg:col-span-1 bg-white dark:bg-dark-bg rounded-lg shadow p-5">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Orders by Status</h3>
                <div class="space-y-2">
                    @php
                        $totalOrders = array_sum($ordersByStatus);
                    @endphp
                    @foreach($ordersByStatus as $status => $count)
                        @php
                            $percentage = $totalOrders > 0 ? ($count / $totalOrders) * 100 : 0;
                            $statusColors = [
                                'pending' => ['bg' => 'bg-yellow-50 dark:bg-yellow-900/20', 'text' => 'text-yellow-700 dark:text-yellow-300', 'border' => 'border-yellow-200 dark:border-yellow-800', 'bar' => 'bg-yellow-500'],
                                'delivered' => ['bg' => 'bg-green-50 dark:bg-green-900/20', 'text' => 'text-green-700 dark:text-green-300', 'border' => 'border-green-200 dark:border-green-800', 'bar' => 'bg-green-500'],
                                'cancelled' => ['bg' => 'bg-red-50 dark:bg-red-900/20', 'text' => 'text-red-700 dark:text-red-300', 'border' => 'border-red-200 dark:border-red-800', 'bar' => 'bg-red-500'],
                                'confirmed' => ['bg' => 'bg-gray-50 dark:bg-gray-700', 'text' => 'text-gray-700 dark:text-gray-300', 'border' => 'border-gray-200 dark:border-gray-600', 'bar' => 'bg-gray-500'],
                                'preparing' => ['bg' => 'bg-gray-50 dark:bg-gray-700', 'text' => 'text-gray-700 dark:text-gray-300', 'border' => 'border-gray-200 dark:border-gray-600', 'bar' => 'bg-gray-500'],
                                'ready' => ['bg' => 'bg-gray-50 dark:bg-gray-700', 'text' => 'text-gray-700 dark:text-gray-300', 'border' => 'border-gray-200 dark:border-gray-600', 'bar' => 'bg-gray-500'],
                            ];
                            $colors = $statusColors[$status] ?? ['bg' => 'bg-gray-50 dark:bg-gray-700', 'text' => 'text-gray-700 dark:text-gray-300', 'border' => 'border-gray-200 dark:border-gray-600', 'bar' => 'bg-gray-500'];
                            $widthPercent = number_format($percentage, 2);
                        @endphp
                        <div class="p-3 {{ $colors['bg'] }} rounded-lg border {{ $colors['border'] }} @if($count === null) border-red-500 @endif">
                            <div class="flex items-center justify-between mb-1.5">
                                <p class="text-xs font-medium {{ $colors['text'] }} capitalize">{{ $status }}</p>
                                <p class="text-xs font-semibold {{ $colors['text'] }} @if($count === null) text-red-500 @endif">{{ $count !== null ? number_format($percentage, 1) . '%' : 'Error' }}</p>
                            </div>
                            <p class="text-lg font-bold {{ $colors['text'] }} mb-1.5 @if($count === null) text-red-500 @endif">{{ $count !== null ? $count : 'Error' }}</p>
                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-1.5">
                                <div class="{{ $colors['bar'] }} h-1.5 rounded-full" style="width: {{ $widthPercent }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Customer & Product Statistics -->
            <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white dark:bg-dark-bg rounded-lg shadow p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Customer Statistics</h3>
                        <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center p-2.5 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-700 dark:text-gray-300 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span class="text-xs text-gray-600 dark:text-gray-400 font-medium">Total Customers</span>
                            </div>
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300 @if(!isset($customerStats['total'])) text-red-500 @endif">
                                {{ isset($customerStats['total']) ? number_format($customerStats['total']) : 'Error' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-xs text-gray-600 dark:text-gray-400 font-medium">This Month</span>
                            </div>
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300 @if(!isset($customerStats['this_month'])) text-red-500 @endif">
                                {{ isset($customerStats['this_month']) ? number_format($customerStats['this_month']) : 'Error' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                                <span class="text-xs text-gray-600 dark:text-gray-400 font-medium">With Orders</span>
                            </div>
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300 @if(!isset($customerStats['with_orders'])) text-red-500 @endif">
                                {{ isset($customerStats['with_orders']) ? number_format($customerStats['with_orders']) : 'Error' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-xs text-gray-600 dark:text-gray-400 font-medium">New Today</span>
                            </div>
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300 @if(!isset($customerStats['new_today'])) text-red-500 @endif">
                                {{ isset($customerStats['new_today']) ? number_format($customerStats['new_today']) : 'Error' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-dark-bg rounded-lg shadow p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Product Statistics</h3>
                        <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center p-2.5 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-700 dark:text-gray-300 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <span class="text-xs text-gray-600 dark:text-gray-400 font-medium">Total Products</span>
                            </div>
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300 @if(!isset($productStats['total'])) text-red-500 @endif">
                                {{ isset($productStats['total']) ? number_format($productStats['total']) : 'Error' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-xs text-gray-600 dark:text-gray-400 font-medium">Available</span>
                            </div>
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300 @if(!isset($productStats['available'])) text-red-500 @endif">
                                {{ isset($productStats['available']) ? number_format($productStats['available']) : 'Error' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                                <span class="text-xs text-gray-600 dark:text-gray-400 font-medium">Featured</span>
                            </div>
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300 @if(!isset($productStats['featured'])) text-red-500 @endif">
                                {{ isset($productStats['featured']) ? number_format($productStats['featured']) : 'Error' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-red-500 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <span class="text-xs text-gray-600 dark:text-gray-400 font-medium">Out of Stock</span>
                            </div>
                            <span class="text-sm font-bold text-red-700 dark:text-red-400 @if(!isset($productStats['out_of_stock'])) text-red-500 @endif">
                                {{ isset($productStats['out_of_stock']) ? number_format($productStats['out_of_stock']) : 'Error' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Third Row: Top Products & Daily Sales -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Top Products -->
            <div class="bg-white dark:bg-dark-bg rounded-lg shadow p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Top 10 Products by Sales</h3>
                    <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">#</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Product</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300 text-right">Quantity Sold</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300 text-right">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($topProducts as $index => $product)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition
                                        @if(!isset($product->name) || !isset($product->total_quantity) || !isset($product->total_revenue)) bg-red-100 dark:bg-red-900/20 @endif
                                ">
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-bold">
                                            {{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white @if(!isset($product->name)) text-red-500 @endif">
                                        {{ isset($product->name) ? $product->name : 'Error' }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 @if(!isset($product->total_quantity)) text-red-500 @endif">
                                            {{ isset($product->total_quantity) ? number_format($product->total_quantity) : 'Error' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white @if(!isset($product->total_revenue)) text-red-500 @endif">
                                        {{ isset($product->total_revenue) ? '€' . number_format($product->total_revenue, 2) : 'Error' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-700 dark:text-gray-300">No product sales data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Daily Sales -->
            <div class="bg-white dark:bg-dark-bg rounded-lg shadow p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Daily Sales (Last 10 Days)</h3>
                    <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Date</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300 text-right">Orders</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300 text-right">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @php
                                $maxRevenue = count($dailySales) > 0 ? max(array_column(array_slice($dailySales, -10), 'revenue')) : 1;
                            @endphp
                            @forelse(array_slice($dailySales, -10) as $day)
                                @php
                                    $barWidth = (isset($day['revenue']) && $maxRevenue > 0) ? ($day['revenue'] / $maxRevenue) * 100 : 0;
                                    $barWidthPercent = number_format($barWidth, 2);
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition
                                    @if(!isset($day['date']) || !isset($day['orders']) || !isset($day['revenue'])) bg-red-100 dark:bg-red-900/20 @endif
                                ">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="font-medium text-gray-900 dark:text-white @if(!isset($day['date'])) text-red-500 @endif">
                                                {{ isset($day['date']) ? \Carbon\Carbon::parse($day['date'])->format('M d, Y') : 'Error' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 @if(!isset($day['orders'])) text-red-500 @endif">
                                            {{ isset($day['orders']) ? $day['orders'] : 'Error' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end">
                                            <div class="w-24 bg-gray-200 dark:bg-gray-600 rounded-full h-2 mr-3">
                                                <div class="bg-gray-400 dark:bg-gray-500 h-2 rounded-full"
                                                    style="width: {{ $barWidthPercent }}%"></div>
                                            </div>
                                            <span class="font-semibold text-gray-900 dark:text-white min-w-[80px] text-right @if(!isset($day['revenue'])) text-red-500 @endif">
                                                {{ isset($day['revenue']) ? '€' . number_format($day['revenue'], 2) : 'Error' }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-700 dark:text-gray-300">No sales data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
