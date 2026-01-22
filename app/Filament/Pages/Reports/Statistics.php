<?php

namespace App\Filament\Pages\Reports;

use App\Domain\Catalog\Models\Product;
use App\Domain\Customers\Models\Customer;
use App\Domain\Orders\Models\Order;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class Statistics extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.reports.statistics';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?string $navigationLabel = 'Statistics';

    public function getTitle(): string
    {
        return 'Statistics';
    }

    public function getSalesStatistics(): array
    {
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();
        $thisWeek = now()->startOfWeek();
        $lastWeek = now()->subWeek()->startOfWeek();
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();
        $thisYear = now()->startOfYear();

        return [
            'today' => [
                'orders' => Order::where('created_at', '>=', $today)->count(),
                'revenue' => Order::where('status', '!=', 'cancelled')
                    ->where('created_at', '>=', $today)->sum('total'),
            ],
            'yesterday' => [
                'orders' => Order::whereBetween('created_at', [$yesterday, $today])->count(),
                'revenue' => Order::where('status', '!=', 'cancelled')
                    ->whereBetween('created_at', [$yesterday, $today])->sum('total'),
            ],
            'this_week' => [
                'orders' => Order::where('created_at', '>=', $thisWeek)->count(),
                'revenue' => Order::where('status', '!=', 'cancelled')
                    ->where('created_at', '>=', $thisWeek)->sum('total'),
            ],
            'last_week' => [
                'orders' => Order::whereBetween('created_at', [$lastWeek, $thisWeek])->count(),
                'revenue' => Order::where('status', '!=', 'cancelled')
                    ->whereBetween('created_at', [$lastWeek, $thisWeek])->sum('total'),
            ],
            'this_month' => [
                'orders' => Order::where('created_at', '>=', $thisMonth)->count(),
                'revenue' => Order::where('status', '!=', 'cancelled')
                    ->where('created_at', '>=', $thisMonth)->sum('total'),
            ],
            'last_month' => [
                'orders' => Order::whereBetween('created_at', [$lastMonth, $thisMonth])->count(),
                'revenue' => Order::where('status', '!=', 'cancelled')
                    ->whereBetween('created_at', [$lastMonth, $thisMonth])->sum('total'),
            ],
            'this_year' => [
                'orders' => Order::where('created_at', '>=', $thisYear)->count(),
                'revenue' => Order::where('status', '!=', 'cancelled')
                    ->where('created_at', '>=', $thisYear)->sum('total'),
            ],
        ];
    }

    public function getDailySalesData(): array
    {
        $startDate = now()->subDays(30)->startOfDay();
        $endDate = now()->endOfDay();

        return Order::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'orders' => (int) $item->orders,
                    'revenue' => (float) $item->revenue,
                ];
            })
            ->toArray();
    }

    public function getOrdersByStatus(): array
    {
        return Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    public function getTopProducts(): array
    {
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get()
            ->toArray();
    }

    public function getCustomerStatistics(): array
    {
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        return [
            'total' => Customer::count(),
            'this_month' => Customer::where('created_at', '>=', $thisMonth)->count(),
            'last_month' => Customer::whereBetween('created_at', [$lastMonth, $thisMonth])->count(),
            'with_orders' => Customer::has('orders')->count(),
            'new_today' => Customer::whereDate('created_at', today())->count(),
        ];
    }

    public function getProductStatistics(): array
    {
        return [
            'total' => Product::count(),
            'available' => Product::where('is_available', true)->count(),
            'featured' => Product::where('is_featured', true)->count(),
            'out_of_stock' => Product::where('is_available', false)->count(),
        ];
    }
}
