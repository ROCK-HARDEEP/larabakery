<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class StatsOverviewFixed extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        try {
            $today = Carbon::today();
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();

            // Get revenue data with fallback
            try {
                $todayRevenue = Order::whereDate('created_at', $today)
                    ->where('status', '!=', 'cancelled')
                    ->sum('total') ?? 0;

                $monthRevenue = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->where('status', '!=', 'cancelled')
                    ->sum('total') ?? 0;

                $todayOrders = Order::whereDate('created_at', $today)->count();
                $totalOrders = Order::count();
                $pendingOrders = Order::where('status', 'pending')->count();
            } catch (\Exception $e) {
                $todayRevenue = 0;
                $monthRevenue = 0;
                $todayOrders = 0;
                $totalOrders = 0;
                $pendingOrders = 0;
            }

            // Get product data with fallback
            try {
                $totalProducts = Product::where('is_active', true)->count();
                $lowStockProducts = Product::where('is_active', true)
                    ->where('stock', '<=', 10)
                    ->where('stock', '>', 0)
                    ->count();
            } catch (\Exception $e) {
                $totalProducts = 0;
                $lowStockProducts = 0;
            }

            // Get customer data with fallback
            try {
                $totalCustomers = User::whereHas('orders')->count();
                $newCustomersThisMonth = User::whereHas('orders')
                    ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->count();
            } catch (\Exception $e) {
                $totalCustomers = 0;
                $newCustomersThisMonth = 0;
            }

            return [
                Stat::make('Today\'s Revenue', '₹' . number_format($todayRevenue, 0))
                    ->description($todayOrders . ' orders today')
                    ->descriptionIcon('heroicon-m-shopping-bag')
                    ->chart([7, 3, 4, 5, 6, 3, 5])
                    ->color('success'),

                Stat::make('Monthly Revenue', '₹' . number_format($monthRevenue, 0))
                    ->description('This month\'s total')
                    ->descriptionIcon('heroicon-m-arrow-trending-up')
                    ->chart([3, 4, 6, 7, 8, 9, 11])
                    ->color('success'),

                Stat::make('Total Orders', number_format($totalOrders))
                    ->description($pendingOrders . ' pending')
                    ->descriptionIcon('heroicon-m-clock')
                    ->chart([3, 5, 2, 7, 4, 6, 8])
                    ->color('primary'),

                Stat::make('Active Products', number_format($totalProducts))
                    ->description($lowStockProducts . ' low stock')
                    ->descriptionIcon($lowStockProducts > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                    ->chart([2, 3, 4, 5, 6, 7, 8])
                    ->color($lowStockProducts > 0 ? 'warning' : 'success'),
            ];

        } catch (\Exception $e) {
            // Return fallback stats if everything fails
            return [
                Stat::make('System Status', 'Loading...')
                    ->description('Dashboard data loading')
                    ->descriptionIcon('heroicon-m-information-circle')
                    ->color('gray'),

                Stat::make('Revenue', '₹0')
                    ->description('Data unavailable')
                    ->descriptionIcon('heroicon-m-currency-rupee')
                    ->color('gray'),

                Stat::make('Orders', '0')
                    ->description('Data unavailable')
                    ->descriptionIcon('heroicon-m-shopping-bag')
                    ->color('gray'),

                Stat::make('Products', '0')
                    ->description('Data unavailable')
                    ->descriptionIcon('heroicon-m-cube')
                    ->color('gray'),
            ];
        }
    }
}