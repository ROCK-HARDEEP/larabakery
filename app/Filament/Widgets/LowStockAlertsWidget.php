<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Product;

class LowStockAlertsWidget extends BaseWidget
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
                        ->with(['category:id,name', 'variants']) // Eager load category and variants
                        ->where('is_active', true)
                        ->whereHas('variants', function($q) {
                            $q->where('stock_quantity', '<=', 10)
                              ->where('is_active', true);
                        })
                        ->withSum('variants as total_stock', 'stock_quantity')
                        ->orderBy('total_stock', 'asc')
                        ->limit(5)
                )
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('')
                    ->circular()
                    ->size(40),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Product')
                    ->searchable()
                    ->limit(20)
                    ->tooltip(function ($record) {
                        return $record->name;
                    }),
                    
                Tables\Columns\TextColumn::make('variants.0.sku')
                    ->label('SKU')
                    ->searchable()
                    ->getStateUsing(fn ($record) => $record->variants->first()?->sku ?? 'N/A'),

                Tables\Columns\TextColumn::make('total_stock')
                    ->label('Stock')
                    ->badge()
                    ->getStateUsing(fn ($record) => $record->total_stock ?? 0)
                    ->color(fn ($state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 5 => 'warning',
                        default => 'info',
                    }),
                    
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->badge(),
            ])
            ->actions([
                Tables\Actions\Action::make('restock')
                    ->label('Restock')
                    ->icon('heroicon-m-plus-circle')
                    ->color('success')
                    ->url(fn (Product $record): string => route('filament.admin.resources.products.edit', $record)),
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