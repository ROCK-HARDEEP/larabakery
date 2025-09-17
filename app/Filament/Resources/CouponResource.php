<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Get;
use Filament\Forms\Set;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Shop Management';
    protected static ?string $navigationLabel = 'Coupons';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Coupon Details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('code')
                                    ->label('Coupon Code')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('e.g., SUMMER2024')
                                    ->suffixAction(
                                        Forms\Components\Actions\Action::make('generate')
                                            ->icon('heroicon-o-sparkles')
                                            ->action(function (Set $set) {
                                                $set('code', Coupon::generateCode());
                                            })
                                    ),
                                
                                Forms\Components\Select::make('type')
                                    ->label('Coupon Type')
                                    ->options([
                                        'first_time_user' => 'First Time User',
                                        'order_above' => 'Order Above Amount',
                                        'product_specific' => 'Product Specific',
                                    ])
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        // Clear fields when type changes
                                        if ($state !== 'order_above') {
                                            $set('minimum_order_amount', null);
                                        }
                                        if ($state !== 'product_specific') {
                                            $set('product_ids', null);
                                        }
                                    }),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->placeholder('e.g., Get 20% off on your first order')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Discount Configuration')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('discount_type')
                                    ->label('Discount Type')
                                    ->options([
                                        'percentage' => 'Percentage',
                                        'fixed' => 'Fixed Amount',
                                    ])
                                    ->required()
                                    ->reactive(),

                                Forms\Components\TextInput::make('discount_value')
                                    ->label(fn (Get $get) => $get('discount_type') === 'percentage' ? 'Discount Percentage' : 'Discount Amount')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->maxValue(fn (Get $get) => $get('discount_type') === 'percentage' ? 100 : 999999)
                                    ->suffix(fn (Get $get) => $get('discount_type') === 'percentage' ? '%' : '₹')
                                    ->helperText(fn (Get $get) => $get('discount_type') === 'percentage' 
                                        ? 'Enter percentage between 0-100' 
                                        : 'Enter fixed discount amount'),
                            ]),

                        // Conditional field for "Order Above" type
                        Forms\Components\TextInput::make('minimum_order_amount')
                            ->label('Minimum Order Amount')
                            ->numeric()
                            ->prefix('₹')
                            ->minValue(0)
                            ->visible(fn (Get $get) => $get('type') === 'order_above')
                            ->required(fn (Get $get) => $get('type') === 'order_above')
                            ->helperText('Minimum cart value required to use this coupon'),

                        // Conditional field for "Product Specific" type
                        Forms\Components\Select::make('product_ids')
                            ->label('Select Products')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(Product::where('is_active', true)->pluck('name', 'id'))
                            ->visible(fn (Get $get) => $get('type') === 'product_specific')
                            ->required(fn (Get $get) => $get('type') === 'product_specific')
                            ->helperText('Select products that this coupon applies to')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Usage Limits & Expiry')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DateTimePicker::make('expires_at')
                                    ->label('Expiry Date & Time')
                                    ->minDate(now())
                                    ->displayFormat('M d, Y H:i')
                                    ->helperText('Leave empty for no expiry'),

                                Forms\Components\TextInput::make('usage_limit')
                                    ->label('Total Usage Limit')
                                    ->numeric()
                                    ->minValue(1)
                                    ->helperText('Maximum times this coupon can be used (leave empty for unlimited)'),

                                Forms\Components\TextInput::make('usage_limit_per_customer')
                                    ->label('Usage Limit Per Customer')
                                    ->numeric()
                                    ->minValue(1)
                                    ->helperText('Maximum times a single customer can use this coupon'),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true)
                                    ->helperText('Inactive coupons cannot be used'),
                            ]),
                    ]),

                Forms\Components\Section::make('Usage Statistics')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Placeholder::make('usage_count')
                                    ->label('Times Used')
                                    ->content(fn ($record) => $record ? $record->usage_count : 0),

                                Forms\Components\Placeholder::make('remaining_uses')
                                    ->label('Remaining Uses')
                                    ->content(fn ($record) => $record ? ($record->getRemainingUses() ?? 'Unlimited') : 'N/A'),

                                Forms\Components\Placeholder::make('status')
                                    ->label('Status')
                                    ->content(function ($record) {
                                        if (!$record) return 'New';
                                        if (!$record->is_active) return 'Inactive';
                                        if ($record->isExpired()) return 'Expired';
                                        if ($record->usage_limit && $record->usage_count >= $record->usage_limit) return 'Limit Reached';
                                        return 'Active';
                                    }),
                            ]),
                    ])
                    ->visible(fn ($record) => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Coupon code copied!')
                    ->weight('bold'),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'first_time_user' => 'First Time',
                        'order_above' => 'Min Order',
                        'product_specific' => 'Product',
                        default => $state
                    })
                    ->colors([
                        'primary' => 'first_time_user',
                        'warning' => 'order_above',
                        'success' => 'product_specific',
                    ]),

                Tables\Columns\TextColumn::make('discount')
                    ->label('Discount')
                    ->getStateUsing(fn ($record) => $record->getDiscountLabel())
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('minimum_order_amount')
                    ->label('Min Order')
                    ->money('INR')
                    ->toggleable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->color(fn ($record) => $record->isExpired() ? 'danger' : null)
                    ->placeholder('No expiry'),

                Tables\Columns\TextColumn::make('usage')
                    ->label('Usage')
                    ->getStateUsing(function ($record) {
                        if ($record->usage_limit) {
                            return $record->usage_count . '/' . $record->usage_limit;
                        }
                        return $record->usage_count . '/∞';
                    })
                    ->badge()
                    ->color(function ($record) {
                        if (!$record->usage_limit) return 'info';
                        $percentage = ($record->usage_count / $record->usage_limit) * 100;
                        if ($percentage >= 100) return 'danger';
                        if ($percentage >= 75) return 'warning';
                        return 'success';
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'first_time_user' => 'First Time User',
                        'order_above' => 'Order Above Amount',
                        'product_specific' => 'Product Specific',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),

                Tables\Filters\Filter::make('expired')
                    ->query(fn ($query) => $query->where('expires_at', '<', now()))
                    ->label('Expired'),

                Tables\Filters\Filter::make('valid')
                    ->query(fn ($query) => $query->active())
                    ->label('Currently Valid'),
            ])
            ->actions([
                Tables\Actions\Action::make('duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->action(function ($record) {
                        $newCoupon = $record->replicate();
                        $newCoupon->code = Coupon::generateCode();
                        $newCoupon->usage_count = 0;
                        $newCoupon->save();
                    })
                    ->successNotificationTitle('Coupon duplicated successfully'),

                Tables\Actions\EditAction::make(),
                
                Tables\Actions\DeleteAction::make()
                    ->before(function ($record) {
                        // Check if coupon has been used
                        if ($record->usage_count > 0) {
                            throw new \Exception('Cannot delete a coupon that has been used. Consider deactivating it instead.');
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            // Check if any coupon has been used
                            if ($records->where('usage_count', '>', 0)->count() > 0) {
                                throw new \Exception('Cannot delete coupons that have been used.');
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
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::active()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}