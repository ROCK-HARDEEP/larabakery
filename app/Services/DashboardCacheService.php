<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardCacheService
{
    protected int $cacheMinutes = 5; // Cache for 5 minutes
    
    public function getStats(string $timeFilter, ?string $customStartDate = null, ?string $customEndDate = null): array
    {
        $cacheKey = "dashboard_stats_{$timeFilter}_{$customStartDate}_{$customEndDate}";
        
        return Cache::remember($cacheKey, now()->addMinutes($this->cacheMinutes), function() use ($timeFilter, $customStartDate, $customEndDate) {
            $dateRange = $this->getDateRange($timeFilter, $customStartDate, $customEndDate);
            
            // Single query to get all order stats with database-agnostic date functions
            $dateColumn = $this->getDateColumn('created_at');
            $currentDateFunction = $this->getCurrentDateFunction();
            
            $orderStats = Order::selectRaw("
                COUNT(*) as total_orders,
                SUM(total) as total_revenue,
                COUNT(CASE WHEN {$dateColumn} = {$currentDateFunction} THEN 1 END) as orders_today,
                SUM(CASE WHEN {$dateColumn} = {$currentDateFunction} THEN total ELSE 0 END) as revenue_today
            ")
            ->whereBetween('created_at', $dateRange)
            ->first();

            // Get customer count efficiently
            $customerCount = User::whereBetween('created_at', $dateRange)
                ->whereDoesntHave('roles', function($query) {
                    $query->whereIn('name', ['admin', 'ops', 'csr', 'content']);
                })
                ->count();

            // Get product count (cached separately as it changes less frequently)
            $productCount = Cache::remember('total_products_count', now()->addHours(1), function() {
                return Product::where('is_active', true)->count();
            });

            return [
                'total_orders' => $orderStats->total_orders ?? 0,
                'total_revenue' => $orderStats->total_revenue ?? 0,
                'orders_today' => $orderStats->orders_today ?? 0,
                'revenue_today' => $orderStats->revenue_today ?? 0,
                'total_customers' => $customerCount,
                'total_products' => $productCount,
            ];
        });
    }
    
    public function getChartData(string $timeFilter, ?string $customStartDate = null, ?string $customEndDate = null): array
    {
        $cacheKey = "dashboard_charts_{$timeFilter}_{$customStartDate}_{$customEndDate}";
        
        return Cache::remember($cacheKey, now()->addMinutes($this->cacheMinutes), function() use ($timeFilter, $customStartDate, $customEndDate) {
            $dateRange = $this->getDateRange($timeFilter, $customStartDate, $customEndDate);
            
            $start = Carbon::parse($dateRange[0]);
            $end = Carbon::parse($dateRange[1]);
            $diffInDays = $start->diffInDays($end);
            
            if ($diffInDays <= 7) {
                return $this->getDailyChartData($start, $end);
            } elseif ($diffInDays <= 90) {
                return $this->getWeeklyChartData($start, $end);
            } else {
                return $this->getMonthlyChartData($start, $end);
            }
        });
    }
    
    public function getRecentOrders(string $timeFilter, ?string $customStartDate = null, ?string $customEndDate = null): \Illuminate\Database\Eloquent\Collection
    {
        $cacheKey = "dashboard_recent_orders_{$timeFilter}_{$customStartDate}_{$customEndDate}";
        
        return Cache::remember($cacheKey, now()->addMinutes($this->cacheMinutes), function() use ($timeFilter, $customStartDate, $customEndDate) {
            $dateRange = $this->getDateRange($timeFilter, $customStartDate, $customEndDate);
            
            return Order::with(['user:id,name'])
                ->select(['id', 'user_id', 'total', 'status', 'created_at'])
                ->whereBetween('created_at', $dateRange)
                ->latest()
                ->limit(10)
                ->get();
        });
    }
    
    public function getLowStockProducts(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('low_stock_products', now()->addMinutes(10), function() {
            return Product::select(['id', 'name', 'stock', 'price', 'is_active'])
                ->where('stock', '<', 10)
                ->where('is_active', true)
                ->orderBy('stock')
                ->limit(5)
                ->get();
        });
    }
    
    private function getDailyChartData(Carbon $start, Carbon $end): array
    {
        // Use database-agnostic date function
        $dateColumn = $this->getDateColumn('created_at');
        
        $results = Order::selectRaw("
            {$dateColumn} as date,
            COUNT(*) as orders_count,
            SUM(total) as revenue_sum
        ")
        ->whereBetween('created_at', [$start, $end])
        ->groupBy(DB::raw($dateColumn))
        ->orderBy('date')
        ->get()
        ->keyBy('date');
        
        $labels = [];
        $orders = [];
        $revenues = [];
        
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $labels[] = $date->format('M d');
            $orders[] = $results->get($dateStr)->orders_count ?? 0;
            $revenues[] = $results->get($dateStr)->revenue_sum ?? 0;
        }
        
        return compact('labels', 'orders', 'revenues');
    }
    
    private function getWeeklyChartData(Carbon $start, Carbon $end): array
    {
        // Use database-agnostic approach for weekly grouping
        $dateColumn = $this->getDateColumn('created_at');
        $weekColumn = $this->getWeekColumn('created_at');
        
        $results = Order::selectRaw("
            {$weekColumn} as week,
            MIN({$dateColumn}) as week_start,
            COUNT(*) as orders_count,
            SUM(total) as revenue_sum
        ")
        ->whereBetween('created_at', [$start, $end])
        ->groupBy(DB::raw($weekColumn))
        ->orderBy(DB::raw($weekColumn))
        ->get();
        
        $labels = [];
        $orders = [];
        $revenues = [];
        
        foreach ($results as $result) {
            $labels[] = Carbon::parse($result->week_start)->format('M d');
            $orders[] = $result->orders_count;
            $revenues[] = $result->revenue_sum;
        }
        
        return compact('labels', 'orders', 'revenues');
    }
    
    private function getMonthlyChartData(Carbon $start, Carbon $end): array
    {
        // Use database-agnostic approach for monthly grouping
        $yearColumn = $this->getYearColumn('created_at');
        $monthColumn = $this->getMonthColumn('created_at');
        
        $results = Order::selectRaw("
            {$yearColumn} as year,
            {$monthColumn} as month,
            COUNT(*) as orders_count,
            SUM(total) as revenue_sum
        ")
        ->whereBetween('created_at', [$start, $end])
        ->groupBy(DB::raw("{$yearColumn}, {$monthColumn}"))
        ->orderBy(DB::raw("{$yearColumn}, {$monthColumn}"))
        ->get();
        
        $labels = [];
        $orders = [];
        $revenues = [];
        
        foreach ($results as $result) {
            $labels[] = Carbon::create($result->year, $result->month, 1)->format('M Y');
            $orders[] = $result->orders_count;
            $revenues[] = $result->revenue_sum;
        }
        
        return compact('labels', 'orders', 'revenues');
    }
    
    private function getDateRange(string $timeFilter, ?string $customStartDate = null, ?string $customEndDate = null): array
    {
        $now = Carbon::now();
        
        return match($timeFilter) {
            'daily' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'weekly' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'monthly' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'yearly' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            'last7days' => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay()],
            'last30days' => [$now->copy()->subDays(29)->startOfDay(), $now->copy()->endOfDay()],
            'last90days' => [$now->copy()->subDays(89)->startOfDay(), $now->copy()->endOfDay()],
            'last6months' => [$now->copy()->subMonths(6)->startOfDay(), $now->copy()->endOfDay()],
            'lastyear' => [$now->copy()->subYear()->startOfDay(), $now->copy()->endOfDay()],
            'all' => [Carbon::create(2020, 1, 1)->startOfDay(), $now->copy()->endOfDay()],
            'custom' => [
                $customStartDate ? Carbon::parse($customStartDate)->startOfDay() : $now->copy()->subDays(30)->startOfDay(),
                $customEndDate ? Carbon::parse($customEndDate)->endOfDay() : $now->copy()->endOfDay()
            ],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };
    }
    
    public function clearCache(string $pattern = 'dashboard_*'): void
    {
        if (method_exists(Cache::getStore(), 'tags')) {
            Cache::tags(['dashboard'])->flush();
        }
        // For drivers that don't support tags, we can't easily clear specific patterns
    }
    
    private function getDateColumn(string $column): string
    {
        $driver = config('database.default');
        $connection = config("database.connections.{$driver}.driver");
        
        return match($connection) {
            'sqlite' => "date({$column})",
            'mysql', 'mariadb' => "DATE({$column})",
            'pgsql' => "DATE({$column})",
            default => "date({$column})"
        };
    }
    
    private function getWeekColumn(string $column): string
    {
        $driver = config('database.default');
        $connection = config("database.connections.{$driver}.driver");
        
        return match($connection) {
            'sqlite' => "strftime('%Y%W', {$column})",
            'mysql', 'mariadb' => "YEARWEEK({$column}, 1)",
            'pgsql' => "EXTRACT(year FROM {$column}) || LPAD(EXTRACT(week FROM {$column})::text, 2, '0')",
            default => "strftime('%Y%W', {$column})"
        };
    }
    
    private function getYearColumn(string $column): string
    {
        $driver = config('database.default');
        $connection = config("database.connections.{$driver}.driver");
        
        return match($connection) {
            'sqlite' => "strftime('%Y', {$column})",
            'mysql', 'mariadb' => "YEAR({$column})",
            'pgsql' => "EXTRACT(year FROM {$column})",
            default => "strftime('%Y', {$column})"
        };
    }
    
    private function getMonthColumn(string $column): string
    {
        $driver = config('database.default');
        $connection = config("database.connections.{$driver}.driver");
        
        return match($connection) {
            'sqlite' => "strftime('%m', {$column})",
            'mysql', 'mariadb' => "MONTH({$column})",
            'pgsql' => "EXTRACT(month FROM {$column})",
            default => "strftime('%m', {$column})"
        };
    }
    
    private function getCurrentDateFunction(): string
    {
        $driver = config('database.default');
        $connection = config("database.connections.{$driver}.driver");
        
        return match($connection) {
            'sqlite' => "date('now')",
            'mysql', 'mariadb' => "CURDATE()",
            'pgsql' => "CURRENT_DATE",
            default => "date('now')"
        };
    }
}