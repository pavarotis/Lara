<?php

namespace App\Filament\Pages\Reports;

use App\Domain\Catalog\Models\Product;
use App\Domain\Customers\Models\Customer;
use App\Domain\Orders\Models\Order;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class Reports extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.reports.reports';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Reports';

    public function getTitle(): string
    {
        return 'Reports';
    }

    public function getStats(): array
    {
        $today = now()->startOfDay();
        $thisMonth = now()->startOfMonth();
        $thisYear = now()->startOfYear();

        return [
            'total_orders' => Order::count(),
            'today_orders' => Order::where('created_at', '>=', $today)->count(),
            'month_orders' => Order::where('created_at', '>=', $thisMonth)->count(),
            'total_revenue' => Order::where('status', '!=', 'cancelled')->sum('total'),
            'today_revenue' => Order::where('status', '!=', 'cancelled')
                ->where('created_at', '>=', $today)->sum('total'),
            'month_revenue' => Order::where('status', '!=', 'cancelled')
                ->where('created_at', '>=', $thisMonth)->sum('total'),
            'total_customers' => Customer::count(),
            'new_customers_today' => Customer::where('created_at', '>=', $today)->count(),
            'new_customers_month' => Customer::where('created_at', '>=', $thisMonth)->count(),
            'total_products' => Product::count(),
            'available_products' => Product::where('is_available', true)->count(),
            'featured_products' => Product::where('is_featured', true)->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
        ];
    }

    public function getRecentOrders(): \Illuminate\Database\Eloquent\Collection
    {
        return Order::with(['customer', 'business'])
            ->latest()
            ->limit(10)
            ->get();
    }

    public function getOrdersByStatus(): array
    {
        return Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }
}
