<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class UserStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalUsers = User::count();
        $verifiedUsers = User::whereNotNull('email_verified_at')->count();
        $newUsersToday = User::whereDate('created_at', today())->count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year)
                                  ->count();
        $activeUsers = User::where('last_login_at', '>=', now()->subDays(30))->count();
        $usersWithOrders = User::has('orders')->count();
        
        // Calculate growth percentage for this month
        $lastMonthUsers = User::whereMonth('created_at', now()->subMonth()->month)
                              ->whereYear('created_at', now()->subMonth()->year)
                              ->count();
        $growthPercentage = $lastMonthUsers > 0 
            ? round((($newUsersThisMonth - $lastMonthUsers) / $lastMonthUsers) * 100, 1)
            : 100;
        
        return [
            Stat::make('Total Users', $totalUsers)
                ->description($newUsersToday . ' new today')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->icon('heroicon-o-user-group'),
            
            Stat::make('Verified Users', $verifiedUsers)
                ->description(round(($verifiedUsers / max($totalUsers, 1)) * 100, 1) . '% verified')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->icon('heroicon-o-shield-check'),
            
            Stat::make('Active Users (30 days)', $activeUsers)
                ->description(round(($activeUsers / max($totalUsers, 1)) * 100, 1) . '% active')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info')
                ->icon('heroicon-o-bolt'),
            
            Stat::make('This Month', $newUsersThisMonth)
                ->description($growthPercentage > 0 ? '+' . $growthPercentage . '% growth' : $growthPercentage . '% growth')
                ->descriptionIcon($growthPercentage > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($growthPercentage > 0 ? 'success' : 'danger')
                ->icon('heroicon-o-calendar-date-range'),
            
            Stat::make('Customers', $usersWithOrders)
                ->description(round(($usersWithOrders / max($totalUsers, 1)) * 100, 1) . '% have ordered')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('warning')
                ->icon('heroicon-o-shopping-cart'),
            
            Stat::make('Login Methods', '')
                ->description($this->getLoginMethodsDescription())
                ->color('gray')
                ->icon('heroicon-o-key'),
        ];
    }
    
    protected function getLoginMethodsDescription(): string
    {
        $methods = User::select('provider', DB::raw('count(*) as count'))
                       ->groupBy('provider')
                       ->pluck('count', 'provider')
                       ->toArray();
        
        $descriptions = [];
        foreach ($methods as $method => $count) {
            $method = $method ?: 'Email';
            $descriptions[] = ucfirst($method) . ': ' . $count;
        }
        
        return implode(' | ', $descriptions) ?: 'No users';
    }
    
    protected static ?string $pollingInterval = '30s';
}