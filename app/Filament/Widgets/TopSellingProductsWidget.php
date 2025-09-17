<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class TopSellingProductsWidget extends BaseWidget
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
                    ->select('products.id', 'products.name', 'products.images_path', DB::raw('MIN(product_variants.price) as min_price'), DB::raw('SUM(product_variants.stock_quantity) as total_stock'), DB::raw('COUNT(order_items.id) as sales_count'))
                    ->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
                    ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                    ->groupBy('products.id', 'products.name', 'products.images_path')
                    ->orderBy('sales_count', 'desc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('images_path')
                    ->label('')
                    ->circular()
                    ->size(40)
                    ->getStateUsing(fn ($record) => $record->image),

                Tables\Columns\TextColumn::make('name')
                    ->label('Product')
                    ->searchable()
                    ->limit(20)
                    ->tooltip(function ($record) {
                        return $record->name;
                    }),

                Tables\Columns\TextColumn::make('min_price')
                    ->label('Price')
                    ->money('INR')
                    ->getStateUsing(fn ($record) => $record->min_price ?? 0),

                Tables\Columns\TextColumn::make('sales_count')
                    ->label('Sales')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('total_stock')
                    ->label('Stock')
                    ->badge()
                    ->getStateUsing(fn ($record) => $record->total_stock ?? 0)
                    ->color(fn ($state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    }),
            ])
            ->paginated(false);

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