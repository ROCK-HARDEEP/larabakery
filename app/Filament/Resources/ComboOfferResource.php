<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ComboOfferResource\Pages;
use App\Models\ComboOffer;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ComboOfferResource extends Resource
{
    protected static ?string $model = ComboOffer::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';
    
    protected static ?string $navigationLabel = 'Combo Offers';
    
    protected static ?string $navigationGroup = 'Shop Management';
    
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Combo Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => 
                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                            ),
                        
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Combo Image')
                            ->image()
                            ->directory('combo-offers')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('original_price')
                            ->required()
                            ->numeric()
                            ->prefix('₹')
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                                $comboPrice = $get('combo_price');
                                if ($state && $comboPrice) {
                                    $discount = (($state - $comboPrice) / $state) * 100;
                                    $set('discount_percentage', round($discount, 2));
                                }
                            }),
                        
                        Forms\Components\TextInput::make('combo_price')
                            ->required()
                            ->numeric()
                            ->prefix('₹')
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                                $originalPrice = $get('original_price');
                                if ($originalPrice && $state) {
                                    $discount = (($originalPrice - $state) / $originalPrice) * 100;
                                    $set('discount_percentage', round($discount, 2));
                                }
                            }),
                        
                        Forms\Components\TextInput::make('discount_percentage')
                            ->numeric()
                            ->suffix('%')
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Products in Combo')
                    ->schema([
                        Forms\Components\Repeater::make('comboProducts')
                            ->relationship('products')
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Product')
                                    ->options(Product::where('is_active', true)->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state) {
                                            $product = Product::find($state);
                                            if ($product) {
                                                $set('pivot.unit_price', $product->price);
                                            }
                                        }
                                    }),
                                
                                Forms\Components\TextInput::make('pivot.quantity')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->minValue(1),
                                
                                Forms\Components\TextInput::make('pivot.unit_price')
                                    ->label('Unit Price')
                                    ->numeric()
                                    ->prefix('₹')
                                    ->disabled()
                                    ->dehydrated(),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->minItems(2)
                            ->reorderable()
                            ->collapsible(),
                    ]),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\TextInput::make('max_quantity')
                            ->label('Maximum Purchase Quantity')
                            ->numeric()
                            ->default(10)
                            ->minValue(1),
                        
                        Forms\Components\TextInput::make('display_order')
                            ->numeric()
                            ->default(0),
                        
                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('Start Date'),
                        
                        Forms\Components\DateTimePicker::make('ends_at')
                            ->label('End Date')
                            ->after('starts_at'),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Image')
                    ->square()
                    ->size(60),
                
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('products_count')
                    ->counts('products')
                    ->label('Products'),
                
                Tables\Columns\TextColumn::make('original_price')
                    ->money('INR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('combo_price')
                    ->money('INR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('discount_percentage')
                    ->label('Discount')
                    ->suffix('%')
                    ->color('success')
                    ->badge(),
                
                Tables\Columns\TextColumn::make('sold_quantity')
                    ->label('Sold')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('ends_at')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->options([
                        true => 'Active',
                        false => 'Inactive',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('display_order', 'asc');
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
            'index' => Pages\ListComboOffers::route('/'),
            'create' => Pages\CreateComboOffer::route('/create'),
            'edit' => Pages\EditComboOffer::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }
}