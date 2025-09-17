<?php

namespace App\Filament\Resources\ShipmentResource\Pages;

use App\Filament\Resources\ShipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;

class ViewShipment extends ViewRecord
{
    protected static string $resource = ShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Order Information')
                    ->schema([
                        TextEntry::make('order.id')
                            ->label('Order ID')
                            ->badge(),
                        TextEntry::make('order.user.name')
                            ->label('Customer'),
                        TextEntry::make('order.total')
                            ->label('Order Total')
                            ->money('INR'),
                    ])
                    ->columns(3),
                
                Section::make('Shipment Details')
                    ->schema([
                        TextEntry::make('tracking_number')
                            ->label('Tracking Number')
                            ->copyable()
                            ->default('Not Available'),
                        TextEntry::make('carrier')
                            ->label('Carrier')
                            ->default('Not Specified'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match($state) {
                                'pending' => 'gray',
                                'processing' => 'info',
                                'shipped' => 'primary',
                                'in_transit', 'out_for_delivery' => 'warning',
                                'delivered' => 'success',
                                'failed', 'returned' => 'danger',
                                default => 'gray'
                            }),
                        TextEntry::make('estimated_delivery')
                            ->label('Estimated Delivery')
                            ->dateTime('d/m/Y H:i')
                            ->default('Not Set'),
                        TextEntry::make('shipped_at')
                            ->label('Shipped At')
                            ->dateTime('d/m/Y H:i')
                            ->default('Not Shipped'),
                        TextEntry::make('delivered_at')
                            ->label('Delivered At')
                            ->dateTime('d/m/Y H:i')
                            ->default('Not Delivered'),
                    ])
                    ->columns(3),
                
                Section::make('Delivery Information')
                    ->schema([
                        TextEntry::make('delivery_person')
                            ->label('Delivery Person')
                            ->default('Not Assigned'),
                        TextEntry::make('delivery_person_contact')
                            ->label('Contact Number')
                            ->default('Not Available'),
                        ImageEntry::make('delivery_proof')
                            ->label('Proof of Delivery')
                            ->visible(fn ($record) => $record->delivery_proof),
                        TextEntry::make('notes')
                            ->label('Notes')
                            ->columnSpanFull()
                            ->default('No notes'),
                    ])
                    ->columns(2),
                
                Section::make('Shipping Address')
                    ->schema([
                        KeyValueEntry::make('shipping_address')
                            ->label('Address Details'),
                    ])
                    ->collapsible(),
            ]);
    }
}