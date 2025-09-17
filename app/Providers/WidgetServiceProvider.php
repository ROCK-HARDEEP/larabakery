<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class WidgetServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Manually register Filament widgets with Livewire to avoid discovery issues
        try {
            Livewire::component('app.filament.widgets.stats-overview', \App\Filament\Widgets\StatsOverview::class);
            Livewire::component('app.filament.widgets.sales-analytics-widget', \App\Filament\Widgets\SalesAnalyticsWidget::class);
            Livewire::component('app.filament.widgets.recent-orders-widget', \App\Filament\Widgets\RecentOrdersWidget::class);
            Livewire::component('app.filament.widgets.top-selling-products-widget', \App\Filament\Widgets\TopSellingProductsWidget::class);
            Livewire::component('app.filament.widgets.low-stock-alerts-widget', \App\Filament\Widgets\LowStockAlertsWidget::class);
        } catch (\Exception $e) {
            // Silently fail if there are issues - this prevents the entire app from breaking
            \Log::warning('Widget registration failed: ' . $e->getMessage());
        }
    }

    public function register()
    {
        //
    }
}