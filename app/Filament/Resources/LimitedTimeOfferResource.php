<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LimitedTimeOfferResource\Pages;
use App\Models\LimitedTimeOffer;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Get;
use Filament\Forms\Set;

class LimitedTimeOfferResource extends Resource
{
    protected static ?string $model = LimitedTimeOffer::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Shop Management';
    protected static ?string $navigationLabel = 'Limited Time Offers';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Offer Details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Offer Name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => 
                                        $set('slug', Str::slug($state))
                                    ),
                                
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('image_path')
                            ->label('Offer Banner Image')
                            ->image()
                            ->disk('public')
                            ->directory('offers')
                            ->visibility('public')
                            ->imagePreviewHeight('200')
                            ->optimizeToWebP()
                            ->helperText('Upload an attractive banner for this offer'),
                    ]),

                Forms\Components\Section::make('Products & Pricing')
                    ->schema([
                        Forms\Components\Select::make('product_ids')
                            ->label('Select Products')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(Product::where('is_active', true)->pluck('name', 'id'))
                            ->required()
                            ->helperText('Select products included in this offer')
                            ->columnSpanFull()
                            ->reactive()
                            ->afterStateUpdated(function (Set $set, $state) {
                                if (!empty($state)) {
                                    // Calculate total price of selected products using minimum variant price
                                    $totalPrice = Product::whereIn('id', $state)
                                        ->with('variants')
                                        ->get()
                                        ->sum(function ($product) {
                                            return $product->variants()->where('is_active', true)->min('price') ?? 0;
                                        });
                                    $set('price', $totalPrice);
                                }
                            }),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->label('Original Price')
                                    ->numeric()
                                    ->prefix('₹')
                                    ->required()
                                    ->reactive()
                                    ->helperText('Combined price of all products'),

                                Forms\Components\TextInput::make('discount_percentage')
                                    ->label('Discount %')
                                    ->numeric()
                                    ->suffix('%')
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->reactive()
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                        $price = $get('price') ?? 0;
                                        if ($price && $state) {
                                            $discountedPrice = $price - ($price * $state / 100);
                                            $set('discounted_price', round($discountedPrice, 2));
                                        }
                                    }),

                                Forms\Components\TextInput::make('discounted_price')
                                    ->label('Discounted Price')
                                    ->numeric()
                                    ->prefix('₹')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText('Auto-calculated'),
                            ]),

                        Forms\Components\Textarea::make('price_rule_json')
                            ->label('Advanced Pricing Rules (JSON)')
                            ->rows(3)
                            ->helperText('Optional: Define complex pricing rules in JSON format')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Time & Quantity Limits')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DateTimePicker::make('starts_at')
                                    ->label('Start Date & Time')
                                    ->displayFormat('M d, Y H:i')
                                    ->minDate(now())
                                    ->reactive()
                                    ->afterStateUpdated(fn (Set $set, $state) => 
                                        $state ? $set('ends_at', null) : null
                                    ),

                                Forms\Components\DateTimePicker::make('ends_at')
                                    ->label('End Date & Time')
                                    ->displayFormat('M d, Y H:i')
                                    ->minDate(fn (Get $get) => $get('starts_at') ?? now())
                                    ->helperText('When the offer expires'),

                                Forms\Components\TextInput::make('max_quantity')
                                    ->label('Maximum Quantity')
                                    ->numeric()
                                    ->minValue(1)
                                    ->helperText('Leave empty for unlimited'),

                                Forms\Components\TextInput::make('sold_quantity')
                                    ->label('Already Sold')
                                    ->numeric()
                                    ->default(0)
                                    ->disabled()
                                    ->dehydrated(true),
                            ]),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Inactive offers won\'t be shown to customers'),
                    ]),

                Forms\Components\Section::make('Offer Statistics')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\Placeholder::make('status')
                                    ->label('Status')
                                    ->content(function ($record) {
                                        if (!$record) return 'New';
                                        if (!$record->is_active) return 'Inactive';
                                        if ($record->isSoldOut()) return 'Sold Out';
                                        if ($record->ends_at && $record->ends_at->isPast()) return 'Expired';
                                        if ($record->starts_at && $record->starts_at->isFuture()) return 'Upcoming';
                                        return 'Active';
                                    }),

                                Forms\Components\Placeholder::make('time_remaining')
                                    ->label('Time Remaining')
                                    ->content(fn ($record) => $record ? $record->getTimeRemaining() ?? 'No limit' : 'N/A'),

                                Forms\Components\Placeholder::make('remaining_quantity')
                                    ->label('Remaining Quantity')
                                    ->content(fn ($record) => $record ? ($record->getRemainingQuantity() ?? 'Unlimited') : 'N/A'),

                                Forms\Components\Placeholder::make('conversion_rate')
                                    ->label('Savings')
                                    ->content(fn ($record) => $record && $record->discount_percentage 
                                        ? '₹' . number_format($record->getDiscountAmount(), 2) . ' (' . $record->discount_percentage . '% OFF)'
                                        : 'N/A'),
                            ]),
                    ])
                    ->visible(fn ($record) => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Banner')
                    ->square()
                    ->size(60),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('products_count')
                    ->label('Products')
                    ->getStateUsing(fn ($record) => count($record->product_ids ?? []))
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('price_display')
                    ->label('Price')
                    ->getStateUsing(fn ($record) => 
                        '₹' . number_format($record->getDiscountedPrice(), 2) . 
                        ' (₹' . number_format($record->price, 2) . ')'
                    )
                    ->html(),

                Tables\Columns\TextColumn::make('discount_percentage')
                    ->label('Discount')
                    ->formatStateUsing(fn ($state) => $state ? $state . '%' : '-')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('time_status')
                    ->label('Time Status')
                    ->getStateUsing(function ($record) {
                        if ($record->starts_at && $record->starts_at->isFuture()) {
                            return 'Starts: ' . $record->starts_at->format('M d, H:i');
                        }
                        if ($record->ends_at) {
                            return 'Ends: ' . $record->ends_at->format('M d, H:i');
                        }
                        return 'Always Active';
                    })
                    ->color(fn ($record) => 
                        $record->ends_at && $record->ends_at->isPast() ? 'danger' : 'primary'
                    ),

                Tables\Columns\TextColumn::make('stock_status')
                    ->label('Stock')
                    ->getStateUsing(function ($record) {
                        if (!$record->max_quantity) {
                            return $record->sold_quantity . ' sold';
                        }
                        return $record->sold_quantity . '/' . $record->max_quantity;
                    })
                    ->badge()
                    ->color(function ($record) {
                        if (!$record->max_quantity) return 'info';
                        $percentage = ($record->sold_quantity / $record->max_quantity) * 100;
                        if ($percentage >= 100) return 'danger';
                        if ($percentage >= 75) return 'warning';
                        return 'success';
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),

                Tables\Filters\Filter::make('active_now')
                    ->query(fn ($query) => $query->active())
                    ->label('Currently Active'),

                Tables\Filters\Filter::make('upcoming')
                    ->query(fn ($query) => $query->upcoming())
                    ->label('Upcoming'),

                Tables\Filters\Filter::make('expired')
                    ->query(fn ($query) => $query->expired())
                    ->label('Expired'),

                Tables\Filters\Filter::make('sold_out')
                    ->query(fn ($query) => $query->whereColumn('sold_quantity', '>=', 'max_quantity'))
                    ->label('Sold Out'),
            ])
            ->actions([
                Tables\Actions\Action::make('duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->action(function ($record) {
                        $newOffer = $record->replicate();
                        $newOffer->name = $record->name . ' (Copy)';
                        $newOffer->slug = Str::slug($newOffer->name);
                        $newOffer->sold_quantity = 0;
                        $newOffer->starts_at = null;
                        $newOffer->ends_at = null;
                        $newOffer->save();
                    })
                    ->successNotificationTitle('Offer duplicated successfully'),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListLimitedTimeOffers::route('/'),
            'create' => Pages\CreateLimitedTimeOffer::route('/create'),
            'edit' => Pages\EditLimitedTimeOffer::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::active()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}