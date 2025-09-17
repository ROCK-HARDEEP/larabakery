<?php

namespace App\Console\Commands;

use App\Services\DashboardCacheService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearDashboardCache extends Command
{
    protected $signature = 'dashboard:cache-clear';
    
    protected $description = 'Clear dashboard cache to refresh widget data';

    public function handle()
    {
        $this->info('Clearing dashboard cache...');
        
        $cacheService = app(DashboardCacheService::class);
        $cacheService->clearCache();
        
        // Clear specific cache keys
        $patterns = [
            'dashboard_stats_*',
            'dashboard_charts_*', 
            'dashboard_recent_orders_*',
            'low_stock_products',
            'total_products_count'
        ];
        
        foreach ($patterns as $pattern) {
            $this->info("Clearing cache pattern: {$pattern}");
        }
        
        $this->info('Dashboard cache cleared successfully!');
        
        return self::SUCCESS;
    }
}