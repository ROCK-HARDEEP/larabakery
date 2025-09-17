<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Order;

class RecentOrdersWidgetFixed extends BaseWidget
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
                        ->with(['user'])
                        ->latest()
                        ->limit(5)
                )
                ->columns([
                    Tables\Columns\TextColumn::make('id')
                        ->label('Order #')
                        ->formatStateUsing(fn ($state) => '#' . str_pad($state, 5, '0', STR_PAD_LEFT))
                        ->searchable()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('user.name')
                        ->label('Customer')
                        ->default('Guest')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('total')
                        ->label('Amount')
                        ->formatStateUsing(fn ($state) => '₹' . number_format($state ?? 0, 0))
                        ->sortable(),

                    Tables\Columns\BadgeColumn::make('status')
                        ->colors([
                            'warning' => 'pending',
                            'info' => 'processing',
                            'success' => 'completed',
                            'danger' => 'cancelled',
                        ])
                        ->formatStateUsing(fn ($state) => ucfirst($state ?? 'pending')),

                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Date')
                        ->dateTime('M j, Y g:i A')
                        ->sortable(),
                ])
                ->actions([
                    Tables\Actions\Action::make('view')
                        ->label('View')
                        ->icon('heroicon-m-eye')
                        ->color('primary')
                        ->url(fn (Order $record): string => '/admin/resources/orders/' . $record->id),
                ])
                ->paginated(false)
                ->emptyStateHeading('No Recent Orders')
                ->emptyStateDescription('Orders will appear here when customers start purchasing.')
                ->emptyStateIcon('heroicon-o-shopping-bag');

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