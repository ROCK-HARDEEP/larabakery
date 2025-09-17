<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Product;

class TopSellingProductsWidgetFixed extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 2,
    ];

    protected static ?string $heading = 'Top Selling Products';

    public function table(Table $table): Table
    {
        try {
            return $table
                ->query(
                    Product::query()
                        ->withCount('orderItems')
                        ->where('is_active', true)
                        ->orderBy('order_items_count', 'desc')
                        ->limit(5)
                )
                ->columns([
                    Tables\Columns\TextColumn::make('name')
                        ->label('Product')
                        ->searchable()
                        ->limit(25)
                        ->tooltip(function ($record) {
                            return $record->name ?? 'N/A';
                        }),

                    Tables\Columns\TextColumn::make('base_price')
                        ->label('Price')
                        ->formatStateUsing(fn ($state) => '₹' . number_format($state ?? 0, 0)),

                    Tables\Columns\TextColumn::make('order_items_count')
                        ->label('Sales')
                        ->badge()
                        ->color('success')
                        ->formatStateUsing(fn ($state) => $state . ' sold'),

                    Tables\Columns\TextColumn::make('stock')
                        ->label('Stock')
                        ->badge()
                        ->color(fn (?int $state): string => match (true) {
                            $state === null => 'gray',
                            $state <= 0 => 'danger',
                            $state <= 10 => 'warning',
                            default => 'success',
                        }),
                ])
                ->actions([
                    Tables\Actions\Action::make('view')
                        ->label('Edit')
                        ->icon('heroicon-m-pencil')
                        ->color('primary')
                        ->url(fn (Product $record): string => '/admin/resources/products/' . $record->id . '/edit'),
                ])
                ->paginated(false)
                ->emptyStateHeading('No Sales Data')
                ->emptyStateDescription('Top selling products will appear here once you have orders.')
                ->emptyStateIcon('heroicon-o-chart-bar-square');

        } catch (\Exception $e) {
            // Return empty table with error message if something goes wrong
            return $table
                ->query(Product::query()->whereRaw('1 = 0')) // Empty query
                ->columns([
                    Tables\Columns\TextColumn::make('message')
                        ->label('Status')
                        ->default('Unable to load sales data')
                ])
                ->paginated(false)
                ->emptyStateHeading('Sales Data Unavailable')
                ->emptyStateDescription('Unable to load top selling products at this time.');
        }
    }
}