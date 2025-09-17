<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductVariantResource\Pages;
use App\Filament\Resources\ProductVariantResource\RelationManagers;
use App\Models\ProductVariant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductVariantResource extends Resource
{
    protected static ?string $model = ProductVariant::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationGroup = 'Shop Management';
    protected static ?string $navigationLabel = 'Product Variants';
    protected static ?int $navigationSort = 3;
    
    public static function shouldRegisterNavigation(): bool
    {
        return false; // Hide from sidebar navigation
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Product & Variant Info')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('Product')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->required()
                            ->unique(ProductVariant::class, 'sku', ignoreRecord: true)
                            ->placeholder('e.g., CAKE-CHOC-S')
                            ->helperText('Unique identifier for this variant'),
                        Forms\Components\TextInput::make('variant_type')
                            ->label('Variant Type')
                            ->placeholder('e.g., Size, Flavor, Color')
                            ->helperText('Category of the variant'),
                        Forms\Components\TextInput::make('variant_value')
                            ->label('Variant Value')
                            ->placeholder('e.g., Small, Chocolate, Red')
                            ->helperText('Specific value for this variant'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Display Order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first'),
                    ])->columns(2),

                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Variant Price')
                            ->required()
                            ->numeric()
                            ->prefix('₹')
                            ->step(0.01),
                        Forms\Components\TextInput::make('compare_at_price')
                            ->label('Compare Price (Original)')
                            ->numeric()
                            ->prefix('₹')
                            ->step(0.01)
                            ->helperText('Show this as crossed-out price if higher than variant price'),
                    ])->columns(2),

                Forms\Components\Section::make('Stock Management')
                    ->schema([
                        Forms\Components\TextInput::make('stock_quantity')
                            ->label('Stock Quantity')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Available quantity for this specific variant'),
                        Forms\Components\TextInput::make('stock')
                            ->label('Alternative Stock Field')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Legacy stock field (optional)'),
                    ])->columns(2),

                Forms\Components\Section::make('Physical Attributes')
                    ->schema([
                        Forms\Components\TextInput::make('weight')
                            ->label('Weight')
                            ->placeholder('e.g., 500g, 1.2kg')
                            ->helperText('Weight of this variant'),
                        Forms\Components\TextInput::make('dimensions')
                            ->label('Dimensions')
                            ->placeholder('e.g., 20x15x10 cm')
                            ->helperText('Dimensions of this variant'),
                    ])->columns(2),

                Forms\Components\Section::make('Variant Image')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Variant Specific Image')
                            ->image()
                            ->imageEditor()
                            ->imagePreviewHeight('150')
                            ->directory('variants')
                            ->visibility('public')
                            ->disk('public')
                            ->helperText('Optional: Upload specific image for this variant')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Additional Settings')
                    ->schema([
                        Forms\Components\KeyValue::make('attributes_json')
                            ->label('Additional Attributes')
                            ->keyLabel('Attribute Name')
                            ->valueLabel('Attribute Value')
                            ->columnSpanFull()
                            ->helperText('Add custom attributes for this variant'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Whether this variant is available for purchase'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->circular()
                    ->size(40)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('variant_type')
                    ->label('Type')
                    ->searchable()
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('variant_value')
                    ->label('Value')
                    ->searchable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('weight')
                    ->label('Weight')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product_id')
                    ->label('Product')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('variant_type')
                    ->label('Variant Type')
                    ->options(function () {
                        return ProductVariant::distinct()->pluck('variant_type', 'variant_type')->toArray();
                    }),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
                Tables\Filters\Filter::make('low_stock')
                    ->label('Low Stock (≤10)')
                    ->query(fn (Builder $query) => $query->where('stock_quantity', '<=', 10)),
                Tables\Filters\Filter::make('out_of_stock')
                    ->label('Out of Stock')
                    ->query(fn (Builder $query) => $query->where('stock_quantity', '<=', 0)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('adjust_stock')
                    ->label('Adjust Stock')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Forms\Components\TextInput::make('new_stock')
                            ->label('New Stock Quantity')
                            ->numeric()
                            ->required()
                            ->default(fn (ProductVariant $record) => $record->stock_quantity),
                    ])
                    ->action(function (ProductVariant $record, array $data) {
                        $record->update(['stock_quantity' => $data['new_stock']]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('bulk_activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check')
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => true])),
                    Tables\Actions\BulkAction::make('bulk_deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-mark')
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => false])),
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
            'index' => Pages\ListProductVariants::route('/'),
            'create' => Pages\CreateProductVariant::route('/create'),
            'edit' => Pages\EditProductVariant::route('/{record}/edit'),
        ];
    }
}
