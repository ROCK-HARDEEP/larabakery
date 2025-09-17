<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected int | string | array $columnSpan = 'full';
    
    protected function getStats(): array
    {
        try {
            return cache()->remember('dashboard_stats', 300, function () {
            $today = Carbon::today();
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
            
            // Use single query with conditional aggregation for better performance
            $revenueData = Order::selectRaw('
                SUM(CASE WHEN DATE(created_at) = ? THEN total ELSE 0 END) as today_revenue,
                SUM(CASE WHEN created_at BETWEEN ? AND ? AND status != ? THEN total ELSE 0 END) as month_revenue,
                SUM(CASE WHEN created_at BETWEEN ? AND ? AND status != ? THEN total ELSE 0 END) as last_month_revenue,
                COUNT(CASE WHEN DATE(created_at) = ? THEN 1 END) as today_orders,
                COUNT(*) as total_orders,
                COUNT(CASE WHEN status = ? THEN 1 END) as pending_orders
            ', [
                $today->toDateString(),
                $startOfMonth, $endOfMonth, 'cancelled',
                $startOfMonth->copy()->subMonth(), $endOfMonth->copy()->subMonth(), 'cancelled',
                $today->toDateString(),
                'pending'
            ])->first();
            
            $todayRevenue = $revenueData->today_revenue ?? 0;
            $monthRevenue = $revenueData->month_revenue ?? 0;
            $lastMonthRevenue = $revenueData->last_month_revenue ?? 0;
            $todayOrders = $revenueData->today_orders ?? 0;
            $totalOrders = $revenueData->total_orders ?? 0;
            $pendingOrders = $revenueData->pending_orders ?? 0;
            
            // Calculate growth percentage
            $revenueGrowth = $lastMonthRevenue > 0 
                ? round((($monthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
                : 100;
            
            // Optimize product and customer queries
            $totalProducts = Product::where('is_active', true)->count();
            $lowStockProducts = Product::where('is_active', true)
                ->whereHas('variants', function($q) {
                    $q->where('stock_quantity', '>', 0)
                      ->where('stock_quantity', '<=', 10)
                      ->where('is_active', true);
                })->count();
            
            $customerData = User::selectRaw('
                COUNT(DISTINCT CASE WHEN EXISTS(SELECT 1 FROM orders WHERE orders.user_id = users.id) THEN users.id END) as total_customers,
                COUNT(DISTINCT CASE WHEN EXISTS(SELECT 1 FROM orders WHERE orders.user_id = users.id) AND users.created_at BETWEEN ? AND ? THEN users.id END) as new_customers
            ', [$startOfMonth, $endOfMonth])->first();
            
            $totalCustomers = $customerData->total_customers ?? 0;
            $newCustomersThisMonth = $customerData->new_customers ?? 0;
        
            return [
                Stat::make('Today\'s Revenue', '₹' . number_format($todayRevenue, 2))
                    ->description($todayOrders . ' orders today')
                    ->descriptionIcon('heroicon-m-shopping-bag')
                    ->chart([7, 3, 4, 5, 6, 3, 5])
                    ->color('success'),
                    
                Stat::make('Monthly Revenue', '₹' . number_format($monthRevenue, 2))
                    ->description($revenueGrowth . '% ' . ($revenueGrowth >= 0 ? 'increase' : 'decrease'))
                    ->descriptionIcon($revenueGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                    ->chart([3, 4, 6, 7, 8, 9, 11])
                    ->color($revenueGrowth >= 0 ? 'success' : 'danger'),
                    
                Stat::make('Total Orders', number_format($totalOrders))
                    ->description($pendingOrders . ' pending')
                    ->descriptionIcon('heroicon-m-clock')
                    ->chart([3, 5, 2, 7, 4, 6, 8])
                    ->color('primary'),
                    
                Stat::make('Total Customers', number_format($totalCustomers))
                    ->description('+' . $newCustomersThisMonth . ' this month')
                    ->descriptionIcon('heroicon-m-user-plus')
                    ->chart([2, 3, 4, 5, 6, 7, 8])
                    ->color('info'),
            ];
        });
        } catch (\Exception $e) {
            // Return fallback stats if there are any errors
            return [
                Stat::make('Revenue Today', '₹0')
                    ->description('Unable to load data')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->chart([1, 1, 1, 1, 1, 1, 1])
                    ->color('warning'),

                Stat::make('Total Orders', '0')
                    ->description('Unable to load data')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->chart([1, 1, 1, 1, 1, 1, 1])
                    ->color('warning'),

                Stat::make('Total Customers', '0')
                    ->description('Unable to load data')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->chart([1, 1, 1, 1, 1, 1, 1])
                    ->color('warning'),
            ];
        }
    }
}