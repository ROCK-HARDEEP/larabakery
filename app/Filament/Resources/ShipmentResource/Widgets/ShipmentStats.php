<?php

namespace App\Filament\Resources\ShipmentResource\Widgets;

use App\Models\Shipment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ShipmentStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Pending Shipments', Shipment::where('status', 'pending')->count())
                ->description('Awaiting processing')
                ->color('gray')
                ->icon('heroicon-o-clock'),
            
            Stat::make('In Transit', Shipment::whereIn('status', ['shipped', 'in_transit', 'out_for_delivery'])->count())
                ->description('Currently being delivered')
                ->color('warning')
                ->icon('heroicon-o-truck'),
            
            Stat::make('Delivered Today', Shipment::where('status', 'delivered')
                ->whereDate('delivered_at', today())
                ->count())
                ->description('Successfully delivered')
                ->color('success')
                ->icon('heroicon-o-check-circle'),
            
            Stat::make('Failed/Returned', Shipment::whereIn('status', ['failed', 'returned'])->count())
                ->description('Require attention')
                ->color('danger')
                ->icon('heroicon-o-exclamation-triangle'),
        ];
    }
}