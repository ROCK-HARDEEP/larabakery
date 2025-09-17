<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    
    protected static ?string $navigationGroup = 'Payments & Shipments';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $navigationLabel = 'Payments';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Information')
                    ->schema([
                        Forms\Components\Select::make('order_id')
                            ->label('Order')
                            ->relationship('order', 'id')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn ($record) => "Order #{$record->id} - {$record->user->name}")
                            ->disabled(fn ($context) => $context === 'edit'),
                        
                        Forms\Components\TextInput::make('provider')
                            ->label('Payment Method')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., UPI, Credit Card, Debit Card, COD, Net Banking'),
                        
                        Forms\Components\TextInput::make('txn_id')
                            ->label('Transaction ID')
                            ->maxLength(255)
                            ->placeholder('Transaction reference number'),
                        
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->prefix('₹')
                            ->required()
                            ->minValue(0),
                        
                        Forms\Components\Select::make('status')
                            ->label('Payment Status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'success' => 'Success',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                                'partial_refund' => 'Partial Refund',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('pending'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Additional Details')
                    ->schema([
                        Forms\Components\KeyValue::make('payload_json')
                            ->label('Payment Gateway Response')
                            ->addActionLabel('Add Field')
                            ->keyLabel('Field')
                            ->valueLabel('Value')
                            ->reorderable()
                            ->columnSpan('full'),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('order.id')
                    ->label('Order #')
                    ->sortable()
                    ->searchable()
                    ->url(fn ($record) => $record->order ? OrderResource::getUrl('view', ['record' => $record->order]) : null),
                
                TextColumn::make('order.user.name')
                    ->label('Customer')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                
                TextColumn::make('provider')
                    ->label('Payment Method')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'upi' => 'success',
                        'cod' => 'warning',
                        'credit card', 'debit card' => 'info',
                        'net banking' => 'primary',
                        default => 'gray',
                    }),
                
                TextColumn::make('txn_id')
                    ->label('Transaction ID')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Transaction ID copied!')
                    ->toggleable(),
                
                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('INR')
                    ->sortable(),
                
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'gray' => 'pending',
                        'warning' => 'processing',
                        'success' => 'success',
                        'danger' => fn ($state) => in_array($state, ['failed', 'cancelled']),
                        'info' => fn ($state) => in_array($state, ['refunded', 'partial_refund']),
                    ]),
                
                TextColumn::make('created_at')
                    ->label('Payment Date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'success' => 'Success',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                        'partial_refund' => 'Partial Refund',
                        'cancelled' => 'Cancelled',
                    ])
                    ->multiple(),
                
                SelectFilter::make('provider')
                    ->label('Payment Method')
                    ->options(fn () => Payment::distinct()->pluck('provider', 'provider')->toArray())
                    ->searchable()
                    ->multiple(),
                
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('From'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
            'view' => Pages\ViewPayment::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}