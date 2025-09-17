<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Product;

class LowStockAlertsWidgetFixed extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 2,
    ];

    protected static ?string $heading = 'Low Stock Alerts';

    public function table(Table $table): Table
    {
        try {
            return $table
                ->query(
                    Product::query()
                        ->where('is_active', true)
                        ->where('stock', '<=', 10)
                        ->orderBy('stock', 'asc')
                        ->limit(5)
                )
                ->columns([
                    Tables\Columns\TextColumn::make('name')
                        ->label('Product')
                        ->searchable()
                        ->limit(20)
                        ->tooltip(function ($record) {
                            return $record->name ?? 'N/A';
                        }),

                    Tables\Columns\TextColumn::make('stock')
                        ->label('Stock')
                        ->badge()
                        ->color(fn (?int $state): string => match (true) {
                            $state === null => 'gray',
                            $state <= 0 => 'danger',
                            $state <= 5 => 'warning',
                            default => 'info',
                        }),

                    Tables\Columns\TextColumn::make('category.name')
                        ->label('Category')
                        ->badge()
                        ->default('N/A'),
                ])
                ->actions([
                    Tables\Actions\Action::make('restock')
                        ->label('Restock')
                        ->icon('heroicon-m-plus-circle')
                        ->color('success')
                        ->url(fn (Product $record): string => '/admin/resources/products/' . $record->id . '/edit'),
                ])
                ->paginated(false)
                ->emptyStateHeading('No Low Stock Items')
                ->emptyStateDescription('All products have sufficient stock.')
                ->emptyStateIcon('heroicon-o-check-circle');

        } catch (\Exception $e) {
            // Return empty table with error message if something goes wrong
            return $table
                ->query(Product::query()->whereRaw('1 = 0')) // Empty query
                ->columns([
                    Tables\Columns\TextColumn::make('message')
                        ->label('Status')
                        ->default('Unable to load stock data')
                ])
                ->paginated(false)
                ->emptyStateHeading('Stock Data Unavailable')
                ->emptyStateDescription('Unable to load low stock alerts at this time.');
        }
    }
}