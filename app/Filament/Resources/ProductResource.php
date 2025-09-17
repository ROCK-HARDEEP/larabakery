<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Shop Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) =>
                                $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null
                            ),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(Product::class, 'slug', ignoreRecord: true),
                        Forms\Components\TextInput::make('hsn_code')
                            ->label('HSN Code'),
                        Forms\Components\TextInput::make('tax_rate')
                            ->numeric()
                            ->suffix('%')
                            ->default(0),
                    ])->columns(2),

                Forms\Components\Section::make('Product Images')
                    ->schema([
                        Forms\Components\FileUpload::make('images_path')
                            ->label('Product Images')
                            ->image()
                            ->imageEditor()
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->imagePreviewHeight('150')
                            ->directory('products')
                            ->visibility('public')
                            ->disk('public')
                            ->helperText('Upload multiple product images. You can reorder them by dragging.')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Product Details')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Short Description')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('full_description')
                            ->label('Full Description')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('ingredients')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\KeyValue::make('nutritional_info')
                            ->label('Nutritional Information')
                            ->keyLabel('Nutrient')
                            ->valueLabel('Amount')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('allergen_info')
                            ->label('Allergen Information')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('storage_instructions')
                            ->label('Storage Instructions')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('shelf_life')
                            ->label('Shelf Life')
                            ->suffix('days'),
                    ])->columns(2),

                Forms\Components\Section::make('Product Status & Reviews')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Product Active')
                            ->default(true)
                            ->helperText('Enable or disable this product on the website'),
                        Forms\Components\TextInput::make('rating')
                            ->numeric()
                            ->step(0.1)
                            ->minValue(0)
                            ->maxValue(5)
                            ->default(0)
                            ->helperText('Product rating (0-5 stars)'),
                        Forms\Components\TextInput::make('review_count')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->helperText('Number of customer reviews'),
                    ])->columns(3),

                Forms\Components\Section::make('Taste Profile')
                    ->schema([
                        Forms\Components\Select::make('taste_type')
                            ->label('Taste Type')
                            ->options([
                                'sweetness' => 'Sweetness',
                                'spiciness' => 'Spiciness',
                            ])
                            ->placeholder('Select taste type (optional)')
                            ->helperText('Choose between sweetness or spiciness')
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set, $state) =>
                                $state === null ? $set('taste_value', null) : null
                            ),
                        Forms\Components\TextInput::make('taste_value')
                            ->label('Taste Value')
                            ->placeholder('E.g., "honey sweetness", "chili heat", "brown sugar", "mild pepper"')
                            ->helperText('Enter the specific type of sweetness or spiciness')
                            ->maxLength(100)
                            ->visible(fn (Forms\Get $get): bool => !empty($get('taste_type'))),
                        Forms\Components\Select::make('taste_level')
                            ->label('Taste Level (Optional)')
                            ->options([
                                1 => '1 - Mild',
                                2 => '2 - Light',
                                3 => '3 - Medium',
                                4 => '4 - Strong',
                                5 => '5 - Intense',
                            ])
                            ->placeholder('Select intensity level')
                            ->helperText('Rate the intensity from 1 (mild) to 5 (intense)')
                            ->visible(fn (Forms\Get $get): bool => !empty($get('taste_type'))),
                        Forms\Components\TextInput::make('taste_description')
                            ->label('Taste Description (Optional)')
                            ->maxLength(255)
                            ->placeholder('E.g., "Rich honey sweetness with floral notes" or "Mild chili heat with smoky finish"')
                            ->helperText('Optional detailed description of the taste profile')
                            ->visible(fn (Forms\Get $get): bool => !empty($get('taste_type'))),
                    ])->columns(1)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Image')
                    ->circular()
                    ->size(60)
                    ->defaultImageUrl(function ($record) {
                        return $record->image_url ?? 'https://images.unsplash.com/photo-1486427944299-bb1a5e99bd69?w=100';
                    }),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('variants_count')
                    ->label('Variants')
                    ->counts('variants')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('min_price')
                    ->label('Price (From)')
                    ->getStateUsing(function ($record) {
                        $minPrice = $record->variants()->where('is_active', true)->min('price');
                        return $minPrice ?: 0;
                    })
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_stock')
                    ->label('Total Stock')
                    ->getStateUsing(function ($record) {
                        return $record->variants()->where('is_active', true)->sum('stock_quantity');
                    })
                    ->numeric()
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0, 1) . ' ⭐'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('hsn_code')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tax_rate')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('review_count')
                    ->label('Reviews')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status'),
                Tables\Filters\Filter::make('in_stock')
                    ->label('In Stock')
                    ->query(fn (Builder $query): Builder => $query->whereHas('variants', fn ($q) => $q->where('stock_quantity', '>', 0))),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\VariantsRelationManager::class,
            RelationManagers\FaqsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}