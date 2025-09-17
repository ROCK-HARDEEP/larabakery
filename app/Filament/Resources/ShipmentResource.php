<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShipmentResource\Pages;
use App\Models\Shipment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;

class ShipmentResource extends Resource
{
    protected static ?string $model = Shipment::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    
    protected static ?string $navigationGroup = 'Payments & Shipments';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $navigationLabel = 'Shipments';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Information')
                    ->schema([
                        Forms\Components\Select::make('order_id')
                            ->label('Order')
                            ->relationship('order', 'id')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn ($record) => "Order #{$record->id} - {$record->user->name}")
                            ->disabled(fn ($context) => $context === 'edit')
                            ->columnSpan('full'),
                    ]),
                
                Forms\Components\Section::make('Shipment Details')
                    ->schema([
                        Forms\Components\TextInput::make('tracking_number')
                            ->label('Tracking Number')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Enter tracking number'),
                        
                        Forms\Components\TextInput::make('carrier')
                            ->label('Carrier/Courier Service')
                            ->maxLength(255)
                            ->placeholder('e.g., BlueDart, DTDC, Delhivery, In-house'),
                        
                        Forms\Components\Select::make('status')
                            ->label('Shipment Status')
                            ->options(Shipment::getStatuses())
                            ->required()
                            ->default('pending')
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state === 'shipped' && !$set->getState('shipped_at')) {
                                    $set('shipped_at', now());
                                }
                                if ($state === 'delivered' && !$set->getState('delivered_at')) {
                                    $set('delivered_at', now());
                                }
                            }),
                        
                        DateTimePicker::make('estimated_delivery')
                            ->label('Estimated Delivery')
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false),
                        
                        DateTimePicker::make('shipped_at')
                            ->label('Shipped At')
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false),
                        
                        DateTimePicker::make('delivered_at')
                            ->label('Delivered At')
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false)
                            ->visible(fn (Forms\Get $get) => in_array($get('status'), ['delivered', 'failed', 'returned'])),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Delivery Information')
                    ->schema([
                        Forms\Components\TextInput::make('delivery_person')
                            ->label('Delivery Person Name')
                            ->maxLength(255)
                            ->placeholder('Name of delivery person'),
                        
                        Forms\Components\TextInput::make('delivery_person_contact')
                            ->label('Delivery Person Contact')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('Contact number'),
                        
                        Forms\Components\FileUpload::make('delivery_proof')
                            ->label('Proof of Delivery')
                            ->image()
                            ->optimizeToWebP()
                            ->imageEditor()
                            ->directory('shipments/delivery-proofs')
                            ->visible(fn (Forms\Get $get) => $get('status') === 'delivered'),
                        
                        Forms\Components\Textarea::make('notes')
                            ->label('Shipment Notes')
                            ->rows(3)
                            ->columnSpan('full'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Shipping Address')
                    ->schema([
                        Forms\Components\KeyValue::make('shipping_address')
                            ->label('Address Details')
                            ->addActionLabel('Add Field')
                            ->keyLabel('Field')
                            ->valueLabel('Value')
                            ->default([
                                'name' => '',
                                'phone' => '',
                                'address_line_1' => '',
                                'address_line_2' => '',
                                'city' => '',
                                'state' => '',
                                'pincode' => '',
                                'landmark' => '',
                            ])
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
                
                TextColumn::make('tracking_number')
                    ->label('Tracking #')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Tracking number copied!')
                    ->default('—'),
                
                TextColumn::make('carrier')
                    ->label('Carrier')
                    ->searchable()
                    ->toggleable()
                    ->default('—'),
                
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'gray' => 'pending',
                        'info' => 'processing',
                        'primary' => 'shipped',
                        'warning' => fn ($state) => in_array($state, ['in_transit', 'out_for_delivery']),
                        'success' => 'delivered',
                        'danger' => fn ($state) => in_array($state, ['failed', 'returned']),
                    ]),
                
                TextColumn::make('estimated_delivery')
                    ->label('Est. Delivery')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('shipped_at')
                    ->label('Shipped')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->default('—'),
                
                TextColumn::make('delivered_at')
                    ->label('Delivered')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->default('—'),
                
                TextColumn::make('delivery_person')
                    ->label('Delivery By')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->default('—'),
                
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(Shipment::getStatuses())
                    ->multiple(),
                
                SelectFilter::make('carrier')
                    ->label('Carrier')
                    ->options(fn () => Shipment::distinct()->whereNotNull('carrier')->pluck('carrier', 'carrier')->toArray())
                    ->searchable()
                    ->multiple(),
                
                Tables\Filters\Filter::make('delivery_date')
                    ->form([
                        Forms\Components\DatePicker::make('delivered_from')
                            ->label('Delivered From'),
                        Forms\Components\DatePicker::make('delivered_until')
                            ->label('Delivered Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['delivered_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('delivered_at', '>=', $date),
                            )
                            ->when(
                                $data['delivered_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('delivered_at', '<=', $date),
                            );
                    }),
                
                Tables\Filters\TernaryFilter::make('delivery_status')
                    ->label('Delivery Status')
                    ->placeholder('All shipments')
                    ->trueLabel('Delivered')
                    ->falseLabel('Not Delivered')
                    ->queries(
                        true: fn (Builder $query) => $query->where('status', 'delivered'),
                        false: fn (Builder $query) => $query->where('status', '!=', 'delivered'),
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('updateStatus')
                    ->label('Update Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('New Status')
                            ->options(Shipment::getStatuses())
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(2),
                    ])
                    ->action(function (Shipment $record, array $data): void {
                        $record->update([
                            'status' => $data['status'],
                            'notes' => $data['notes'] ? $record->notes . "\n" . now()->format('d/m/Y H:i') . ": " . $data['notes'] : $record->notes,
                        ]);
                        
                        if ($data['status'] === 'shipped' && !$record->shipped_at) {
                            $record->update(['shipped_at' => now()]);
                        }
                        
                        if ($data['status'] === 'delivered' && !$record->delivered_at) {
                            $record->update(['delivered_at' => now()]);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('updateBulkStatus')
                        ->label('Update Status')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('New Status')
                                ->options(Shipment::getStatuses())
                                ->required(),
                        ])
                        ->action(function ($records, array $data): void {
                            foreach ($records as $record) {
                                $record->update(['status' => $data['status']]);
                                
                                if ($data['status'] === 'shipped' && !$record->shipped_at) {
                                    $record->update(['shipped_at' => now()]);
                                }
                                
                                if ($data['status'] === 'delivered' && !$record->delivered_at) {
                                    $record->update(['delivered_at' => now()]);
                                }
                            }
                        }),
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
            'index' => Pages\ListShipments::route('/'),
            'create' => Pages\CreateShipment::route('/create'),
            'edit' => Pages\EditShipment::route('/{record}/edit'),
            'view' => Pages\ViewShipment::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::whereIn('status', ['pending', 'processing', 'shipped', 'in_transit', 'out_for_delivery'])->count();
        return $count ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }
}