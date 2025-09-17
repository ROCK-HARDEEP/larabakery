<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Split;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
                ->requiresConfirmation(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('User Profile')
                    ->schema([
                        Split::make([
                            ImageEntry::make('avatar_url')
                                ->label('')
                                ->circular()
                                ->size(120)
                                ->defaultImageUrl(url('https://ui-avatars.com/api/?name=User')),
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('name')
                                        ->label('Full Name')
                                        ->weight('bold')
                                        ->size('lg'),
                                    TextEntry::make('email')
                                        ->label('Email Address')
                                        ->icon('heroicon-m-envelope')
                                        ->copyable(),
                                    TextEntry::make('phone')
                                        ->label('Phone Number')
                                        ->icon('heroicon-m-phone')
                                        ->copyable()
                                        ->default('Not provided'),
                                    TextEntry::make('username')
                                        ->label('Username')
                                        ->badge()
                                        ->default('Not set'),
                                ]),
                        ])->from('md'),
                    ]),
                
                Section::make('Account Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('id')
                                    ->label('User ID')
                                    ->badge(),
                                TextEntry::make('provider')
                                    ->label('Login Method')
                                    ->badge()
                                    ->color(fn (string $state): string => match($state) {
                                        'google' => 'info',
                                        'facebook' => 'warning',
                                        default => 'primary',
                                    }),
                                IconEntry::make('email_verified_at')
                                    ->label('Email Verified')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-badge')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),
                                IconEntry::make('phone_verified_at')
                                    ->label('Phone Verified')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-badge')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),
                                TextEntry::make('last_login_at')
                                    ->label('Last Login')
                                    ->dateTime('d/m/Y H:i')
                                    ->since()
                                    ->default('Never'),
                                TextEntry::make('created_at')
                                    ->label('Registered On')
                                    ->dateTime('d/m/Y H:i'),
                            ]),
                    ]),
                
                Section::make('Contact & Address')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('address')
                                    ->label('Address')
                                    ->columnSpan(2)
                                    ->default('No address provided'),
                                TextEntry::make('pincode')
                                    ->label('Pincode')
                                    ->default('Not provided'),
                                TextEntry::make('gstin')
                                    ->label('GSTIN')
                                    ->default('Not provided'),
                            ]),
                    ])
                    ->collapsible(),
                
                Section::make('Notification Preferences')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                IconEntry::make('notify_email')
                                    ->label('Email')
                                    ->boolean(),
                                IconEntry::make('notify_sms')
                                    ->label('SMS')
                                    ->boolean(),
                                IconEntry::make('notify_whatsapp')
                                    ->label('WhatsApp')
                                    ->boolean(),
                                IconEntry::make('notify_push')
                                    ->label('Push')
                                    ->boolean(),
                            ]),
                    ])
                    ->collapsible(),
                
                Section::make('Order Statistics')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('orders_count')
                                    ->label('Total Orders')
                                    ->badge()
                                    ->color('success')
                                    ->default(0),
                                TextEntry::make('total_spent')
                                    ->label('Total Spent')
                                    ->money('INR')
                                    ->default(0)
                                    ->getStateUsing(fn ($record) => $record->orders()->sum('total')),
                                TextEntry::make('average_order')
                                    ->label('Average Order')
                                    ->money('INR')
                                    ->default(0)
                                    ->getStateUsing(fn ($record) => $record->orders()->avg('total') ?? 0),
                                TextEntry::make('last_order')
                                    ->label('Last Order')
                                    ->dateTime('d/m/Y')
                                    ->default('No orders')
                                    ->getStateUsing(fn ($record) => $record->orders()->latest()->first()?->created_at),
                            ]),
                    ]),
                
                Section::make('Tags')
                    ->schema([
                        TextEntry::make('tags')
                            ->label('')
                            ->badge()
                            ->separator(',')
                            ->default(['No tags']),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}