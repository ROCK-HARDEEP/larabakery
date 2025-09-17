<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ViewAction;

class CustomerResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Customers';

    protected static ?string $modelLabel = 'Customer';

    protected static ?string $pluralModelLabel = 'Customers';

    public static function getEloquentQuery(): Builder
    {
        // Show only users without admin roles
        return parent::getEloquentQuery()
            ->with(['roles', 'orders']) // Eager load relationships
            ->whereDoesntHave('roles', function (Builder $query) {
                $query->whereIn('name', ['admin', 'superadmin']);
            });
    }

    public static function canViewAny(): bool
    {
        // Both admin and superadmin can view customers
        return true; // Allow for all authenticated admin users
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Show for both admin and superadmin
        $user = auth()->user();
        if (!$user) return false;

        // Load roles relationship if not loaded
        if (!$user->relationLoaded('roles')) {
            $user->load('roles');
        }

        return $user->hasRole(['admin', 'superadmin']);
    }

    public static function canCreate(): bool
    {
        // Nobody can create customers through admin panel
        return false;
    }

    public static function canEdit($record): bool
    {
        // Nobody can edit customers through admin panel
        return false;
    }

    public static function canDelete($record): bool
    {
        // Nobody can delete customers through admin panel
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function canView($record): bool
    {
        // Both admin and superadmin can view individual customers
        $user = auth()->user();
        if (!$user) return false;

        return $user->hasRole(['admin', 'superadmin']) ||
               $user->can('view_customers');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(url('https://ui-avatars.com/api/?name=User'))
                    ->size(40),

                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable()
                    ->weight('medium'),

                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email copied!')
                    ->icon('heroicon-m-envelope'),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Phone copied!')
                    ->icon('heroicon-m-phone'),

                TextColumn::make('address')
                    ->label('Address')
                    ->limit(50)
                    ->tooltip(function ($record) {
                        return $record->address;
                    })
                    ->searchable(),

                TextColumn::make('pincode')
                    ->label('Pincode')
                    ->searchable(),

                IconColumn::make('email_verified_at')
                    ->label('Email Verified')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                BadgeColumn::make('provider')
                    ->label('Login Method')
                    ->colors([
                        'primary' => 'email',
                        'info' => 'google',
                        'warning' => 'facebook',
                    ]),

                TextColumn::make('orders_count')
                    ->label('Orders')
                    ->counts('orders')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                TextColumn::make('last_login_at')
                    ->label('Last Login')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->since(),

                TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('provider')
                    ->label('Login Method')
                    ->options([
                        'email' => 'Email',
                        'google' => 'Google',
                        'facebook' => 'Facebook',
                    ])
                    ->multiple(),

                Tables\Filters\Filter::make('has_orders')
                    ->label('Has Orders')
                    ->query(fn (Builder $query): Builder => $query->has('orders')),

                Tables\Filters\Filter::make('no_orders')
                    ->label('No Orders')
                    ->query(fn (Builder $query): Builder => $query->doesntHave('orders')),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'view' => Pages\ViewCustomer::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }
}