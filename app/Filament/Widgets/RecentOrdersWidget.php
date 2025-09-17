<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;

class RecentOrdersWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 3,
    ];
    
    protected static ?string $heading = 'Recent Orders';
    
    public function table(Table $table): Table
    {
        try {
            return $table
            ->query(
                Order::query()
                    ->with(['user:id,name']) // Eager load user relationship
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('formatted_order_id')
                    ->label('Order ID')
                    ->searchable(query: function ($query, $search) {
                        // Allow searching by the formatted ID
                        return $query->where('id', 'like', "%{$search}%");
                    })
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('id', $direction);
                    })
                    ->copyable()
                    ->copyMessage('Order ID copied')
                    ->copyMessageDuration(1500)
                    ->weight('bold')
                    ->badge()
                    ->color('primary'),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->default('Guest'),
                    
                Tables\Columns\TextColumn::make('total')
                    ->label('Amount')
                    ->money('INR')
                    ->sortable(),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'processing',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),
                    
                Tables\Columns\TextColumn::make('payment_mode')
                    ->label('Payment')
                    ->badge()
                    ->colors([
                        'success' => 'cod',
                        'primary' => 'online',
                    ]),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (Order $record): string => route('filament.admin.resources.orders.edit', $record))
                    ->icon('heroicon-m-eye')
                    ->color('primary'),
            ])
            ->paginated([5, 10]);

        } catch (\Exception $e) {
            // Return empty table with error message if something goes wrong
            return $table
                ->query(Order::query()->whereRaw('1 = 0')) // Empty query
                ->columns([
                    Tables\Columns\TextColumn::make('message')
                        ->label('Status')
                        ->default('Unable to load recent orders')
                ])
                ->paginated(false)
                ->emptyStateHeading('Order Data Unavailable')
                ->emptyStateDescription('Unable to load recent orders at this time.');
        }
    }
}