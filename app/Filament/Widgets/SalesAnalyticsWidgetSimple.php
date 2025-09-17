<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class SalesAnalyticsWidgetSimple extends ChartWidget
{
    protected static ?string $heading = 'Sales Analytics';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Revenue (₹)',
                    'data' => [100, 200, 150, 300, 250, 400, 350],
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'borderColor' => 'rgb(245, 158, 11)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}