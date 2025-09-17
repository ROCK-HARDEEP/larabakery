<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\ProductVariant;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Variant Information')
                    ->schema([
                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('e.g., PROD-VAR-001'),
                        Forms\Components\TextInput::make('variant_type')
                            ->label('Type')
                            ->required()
                            ->placeholder('e.g., Size, Flavor, Color'),
                        Forms\Components\TextInput::make('variant_value')
                            ->label('Value')
                            ->required()
                            ->placeholder('e.g., Large, Chocolate, Red'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),

                Forms\Components\Section::make('Pricing & Stock')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Price')
                            ->required()
                            ->numeric()
                            ->prefix('₹')
                            ->step(0.01),
                        Forms\Components\TextInput::make('compare_at_price')
                            ->label('Compare Price')
                            ->numeric()
                            ->prefix('₹')
                            ->step(0.01),
                        Forms\Components\TextInput::make('stock_quantity')
                            ->label('Stock Quantity')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Additional Details')
                    ->schema([
                        Forms\Components\TextInput::make('weight')
                            ->label('Weight')
                            ->placeholder('e.g., 500g'),
                        Forms\Components\TextInput::make('dimensions')
                            ->label('Dimensions')
                            ->placeholder('e.g., 20x15x10 cm'),
                        Forms\Components\FileUpload::make('image')
                            ->label('Variant Image')
                            ->image()
                            ->imageEditor()
                            ->directory('variants'),
                    ])->columns(2),
            ]);
    }

    /**
     * Generate SKU suffix from option value
     */
    protected static function generateSkuSuffix(string $value): string
    {
        // Remove special characters and spaces
        $value = preg_replace('/[^A-Za-z0-9]/', '', $value);

        // Common replacements for better SKU generation
        $replacements = [
            'small' => 'S',
            'medium' => 'M',
            'large' => 'L',
            'extralarge' => 'XL',
            'extrasmall' => 'XS',
            'chocolate' => 'CHO',
            'vanilla' => 'VAN',
            'strawberry' => 'STR',
            'butterscotch' => 'BUT',
            'pineapple' => 'PIN',
            'mango' => 'MAN',
            'orange' => 'ORG',
            'black' => 'BLK',
            'white' => 'WHT',
            'red' => 'RED',
            'blue' => 'BLU',
            'green' => 'GRN',
            'yellow' => 'YEL',
            'brown' => 'BRN',
            'pink' => 'PNK',
            'purple' => 'PUR',
            'gram' => 'G',
            'kilogram' => 'KG',
            'pound' => 'LB',
            'ounce' => 'OZ',
            'liter' => 'L',
            'milliliter' => 'ML',
        ];

        $valueLower = strtolower($value);

        // Check if we have a predefined replacement
        foreach ($replacements as $key => $replacement) {
            if (strpos($valueLower, $key) !== false) {
                return $replacement;
            }
        }

        // Extract numbers if present (for sizes like "500g", "1kg")
        if (preg_match('/(\d+)/', $value, $matches)) {
            return strtoupper($matches[1]);
        }

        // If no match, take first 3-4 characters
        $suffix = strtoupper(substr($value, 0, min(4, strlen($value))));

        // If still empty, generate a random suffix
        if (empty($suffix)) {
            $suffix = strtoupper(substr(md5($value), 0, 3));
        }

        return $suffix;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('variant_value')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->size(40)
                    ->circular(),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('variant_type')
                    ->label('Type')
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
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
                Tables\Filters\Filter::make('low_stock')
                    ->label('Low Stock')
                    ->query(fn (Builder $query) => $query->where('stock_quantity', '<=', 10)),
            ])
            ->headerActions([
                Tables\Actions\Action::make('manage_variants')
                    ->label('Manage Product Variants')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->color('primary')
                    ->size('lg')
                    ->modal()
                    ->modalHeading('Product Variant Management')
                    ->modalDescription('Set up your product variants and quantities')
                    ->modalWidth('7xl')
                    ->form([
                        Section::make()
                            ->schema([
                                Placeholder::make('instructions')
                                    ->content('Step 1: Enable variants if your product has flavors, colors, etc.')
                                    ->helperText('Step 2: Select variant type and add variants. Step 3: Add quantities for each variant with pricing.'),

                                Toggle::make('has_variants')
                                    ->label('Product has variants (Flavors, Colors, Materials, etc.)')
                                    ->helperText('Enable if your product comes in different flavors, colors, or materials')
                                    ->reactive()
                                    ->default(false),

                                // VARIANT SECTION - Only shown when has_variants is enabled
                                Section::make('Variant Configuration')
                                    ->description('Configure your product variants (Flavors, Colors, etc.)')
                                    ->visible(fn ($get) => $get('has_variants'))
                                    ->schema([
                                        Grid::make(2)->schema([
                                            Select::make('variant_type')
                                                ->label('Variant Type')
                                                ->options([
                                                    'Flavor' => 'Flavor',
                                                    'Color' => 'Color',
                                                    'Material' => 'Material',
                                                    'Style' => 'Style',
                                                    'Custom' => 'Custom Type',
                                                ])
                                                ->required()
                                                ->reactive()
                                                ->columnSpan(1),

                                            TextInput::make('custom_variant_type')
                                                ->label('Custom Variant Type')
                                                ->placeholder('Enter custom type name')
                                                ->visible(fn ($get) => $get('variant_type') === 'Custom')
                                                ->required(fn ($get) => $get('variant_type') === 'Custom')
                                                ->columnSpan(1),
                                        ]),

                                        Repeater::make('variants')
                                            ->label('Variants')
                                            ->schema([
                                                Grid::make(12)->schema([
                                                    TextInput::make('name')
                                                        ->label('Variant Name')
                                                        ->placeholder('e.g., Chocolate, Red, Cotton')
                                                        ->required()
                                                        ->columnSpan(4),

                                                    Toggle::make('is_active')
                                                        ->label('Active')
                                                        ->default(true)
                                                        ->inline()
                                                        ->columnSpan(2),

                                                    Placeholder::make('quantities_label')
                                                        ->content('Add quantities for this variant below')
                                                        ->columnSpan(6),

                                                    // Quantities Section for each variant
                                                    Section::make('Quantities for this Variant')
                                                        ->description('Add different quantities/sizes with pricing')
                                                        ->columnSpan(12)
                                                        ->schema([
                                                            Repeater::make('quantities')
                                                                ->label('')
                                                                ->schema([
                                                                    Grid::make(6)->schema([
                                                                        TextInput::make('quantity')
                                                                            ->label('Quantity/Size')
                                                                            ->placeholder('e.g., 500g, 1kg, Pack of 6')
                                                                            ->required()
                                                                            ->columnSpan(2),

                                                                        TextInput::make('price')
                                                                            ->label('Price (₹)')
                                                                            ->numeric()
                                                                            ->required()
                                                                            ->placeholder('0.00')
                                                                            ->step(0.01)
                                                                            ->columnSpan(1),

                                                                        TextInput::make('compare_price')
                                                                            ->label('Compare Price (₹)')
                                                                            ->numeric()
                                                                            ->placeholder('Optional')
                                                                            ->step(0.01)
                                                                            ->columnSpan(1),

                                                                        TextInput::make('stock')
                                                                            ->label('Stock')
                                                                            ->numeric()
                                                                            ->placeholder('0')
                                                                            ->default(0)
                                                                            ->minValue(0)
                                                                            ->columnSpan(1),

                                                                        Toggle::make('active')
                                                                            ->label('Active')
                                                                            ->default(true)
                                                                            ->inline()
                                                                            ->columnSpan(1),
                                                                    ]),
                                                                ])
                                                                ->addActionLabel('Add Quantity')
                                                                ->defaultItems(1)
                                                                ->minItems(1)
                                                                ->collapsible()
                                                                ->itemLabel(fn (array $state): ?string => $state['quantity'] ?? 'New Quantity'),
                                                        ]),
                                                ]),
                                            ])
                                            ->addActionLabel('Add Variant')
                                            ->defaultItems(1)
                                            ->minItems(1)
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'New Variant'),
                                    ]),

                                // QUANTITY ONLY SECTION - Only shown when has_variants is disabled
                                Section::make('Quantity Configuration')
                                    ->description('Add quantity/size options for products without variants')
                                    ->visible(fn ($get) => !$get('has_variants'))
                                    ->schema([
                                        Repeater::make('quantities_only')
                                            ->label('Quantity/Size Options')
                                            ->schema([
                                                Grid::make(6)->schema([
                                                    TextInput::make('quantity')
                                                        ->label('Quantity/Size')
                                                        ->placeholder('e.g., Small, 500g, Pack of 6')
                                                        ->required()
                                                        ->columnSpan(2),

                                                    TextInput::make('price')
                                                        ->label('Price (₹)')
                                                        ->numeric()
                                                        ->required()
                                                        ->placeholder('0.00')
                                                        ->step(0.01)
                                                        ->columnSpan(1),

                                                    TextInput::make('compare_price')
                                                        ->label('Compare Price (₹)')
                                                        ->numeric()
                                                        ->placeholder('Optional')
                                                        ->step(0.01)
                                                        ->columnSpan(1),

                                                    TextInput::make('stock')
                                                        ->label('Stock')
                                                        ->numeric()
                                                        ->placeholder('0')
                                                        ->default(0)
                                                        ->minValue(0)
                                                        ->columnSpan(1),

                                                    Toggle::make('active')
                                                        ->label('Active')
                                                        ->default(true)
                                                        ->inline()
                                                        ->columnSpan(1),
                                                ]),
                                            ])
                                            ->addActionLabel('Add Quantity')
                                            ->defaultItems(3)
                                            ->minItems(1)
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['quantity'] ?? 'New Quantity'),
                                    ]),
                            ]),
                    ])
                    ->fillForm(function ($record) {
                        $product = $this->getOwnerRecord();
                        $variants = $product->variants()->orderBy('variant_type')->orderBy('sort_order')->get();

                        if ($variants->isEmpty()) {
                            return [
                                'has_variants' => false,
                                'quantities_only' => [
                                    ['quantity' => '', 'price' => null, 'compare_price' => null, 'stock' => 0, 'active' => true],
                                    ['quantity' => '', 'price' => null, 'compare_price' => null, 'stock' => 0, 'active' => true],
                                    ['quantity' => '', 'price' => null, 'compare_price' => null, 'stock' => 0, 'active' => true],
                                ]
                            ];
                        }

                        // Check if variants have main variants (like flavors) or just quantities
                        $hasMainVariants = false;
                        $mainVariantType = '';
                        $groupedData = [];

                        foreach ($variants as $variant) {
                            // Check if variant value contains " - " indicating it's a main variant + quantity
                            if (strpos($variant->variant_value, ' - ') !== false) {
                                $hasMainVariants = true;
                                $mainVariantType = $variant->variant_type;

                                $parts = explode(' - ', $variant->variant_value, 2);
                                $variantName = $parts[0];
                                $quantity = $parts[1];

                                if (!isset($groupedData[$variantName])) {
                                    $groupedData[$variantName] = [
                                        'name' => $variantName,
                                        'is_active' => true,
                                        'quantities' => []
                                    ];
                                }

                                $groupedData[$variantName]['quantities'][] = [
                                    'quantity' => $quantity,
                                    'price' => $variant->price,
                                    'compare_price' => $variant->compare_at_price,
                                    'stock' => $variant->stock_quantity,
                                    'active' => $variant->is_active,
                                ];
                            } else {
                                // It's just a quantity variant
                                $groupedData[] = [
                                    'quantity' => $variant->variant_value,
                                    'price' => $variant->price,
                                    'compare_price' => $variant->compare_at_price,
                                    'stock' => $variant->stock_quantity,
                                    'active' => $variant->is_active,
                                ];
                            }
                        }

                        if ($hasMainVariants) {
                            return [
                                'has_variants' => true,
                                'variant_type' => in_array($mainVariantType, ['Flavor', 'Color', 'Material', 'Style']) ? $mainVariantType : 'Custom',
                                'custom_variant_type' => !in_array($mainVariantType, ['Flavor', 'Color', 'Material', 'Style']) ? $mainVariantType : '',
                                'variants' => array_values($groupedData),
                            ];
                        } else {
                            return [
                                'has_variants' => false,
                                'quantities_only' => $groupedData,
                            ];
                        }
                    })
                    ->action(function (array $data) {
                        $product = $this->getOwnerRecord();

                        // Delete existing variants
                        $product->variants()->delete();

                        $sortOrder = 0;

                        if ($data['has_variants']) {
                            // Product has main variants (flavors, colors, etc.)
                            $variantType = $data['variant_type'] === 'Custom'
                                ? ($data['custom_variant_type'] ?? 'Custom')
                                : $data['variant_type'];

                            foreach ($data['variants'] ?? [] as $variant) {
                                if (empty($variant['name'])) continue;

                                foreach ($variant['quantities'] ?? [] as $quantity) {
                                    if (empty($quantity['quantity']) || empty($quantity['price'])) continue;

                                    // Combine variant name and quantity
                                    $combinedValue = $variant['name'] . ' - ' . $quantity['quantity'];

                                    // Generate SKU
                                    $skuBase = strtoupper($product->slug ?? 'PROD');
                                    $variantSuffix = self::generateSkuSuffix($variant['name']);
                                    $quantitySuffix = self::generateSkuSuffix($quantity['quantity']);
                                    $sku = $skuBase . '-' . $variantSuffix . '-' . $quantitySuffix;

                                    // Ensure SKU is unique
                                    $finalSku = $sku;
                                    $counter = 1;
                                    while (ProductVariant::where('sku', $finalSku)->exists()) {
                                        $finalSku = $sku . $counter;
                                        $counter++;
                                    }

                                    ProductVariant::create([
                                        'product_id' => $product->id,
                                        'variant_type' => $variantType,
                                        'variant_value' => $combinedValue,
                                        'sku' => $finalSku,
                                        'price' => $quantity['price'],
                                        'compare_at_price' => $quantity['compare_price'] ?? null,
                                        'stock_quantity' => $quantity['stock'] ?? 0,
                                        'stock' => $quantity['stock'] ?? 0,
                                        'is_active' => ($variant['is_active'] ?? true) && ($quantity['active'] ?? true),
                                        'sort_order' => $sortOrder++,
                                        'attributes_json' => [
                                            'main_variant' => $variant['name'],
                                            'quantity_variant' => $quantity['quantity']
                                        ],
                                    ]);
                                }
                            }
                        } else {
                            // Product has only quantity variants
                            foreach ($data['quantities_only'] ?? [] as $quantityData) {
                                if (empty($quantityData['quantity']) || empty($quantityData['price'])) continue;

                                // Determine variant type based on value
                                $variantType = 'Quantity';
                                if (preg_match('/\b(small|medium|large|xl|xs)\b/i', $quantityData['quantity'])) {
                                    $variantType = 'Size';
                                } elseif (preg_match('/\d+[gk]g?/', $quantityData['quantity'])) {
                                    $variantType = 'Weight';
                                }

                                // Generate SKU
                                $skuBase = strtoupper($product->slug ?? 'PROD');
                                $quantitySuffix = self::generateSkuSuffix($quantityData['quantity']);
                                $sku = $skuBase . '-' . $quantitySuffix;

                                // Ensure SKU is unique
                                $finalSku = $sku;
                                $counter = 1;
                                while (ProductVariant::where('sku', $finalSku)->exists()) {
                                    $finalSku = $sku . $counter;
                                    $counter++;
                                }

                                ProductVariant::create([
                                    'product_id' => $product->id,
                                    'variant_type' => $variantType,
                                    'variant_value' => $quantityData['quantity'],
                                    'sku' => $finalSku,
                                    'price' => $quantityData['price'],
                                    'compare_at_price' => $quantityData['compare_price'] ?? null,
                                    'stock_quantity' => $quantityData['stock'] ?? 0,
                                    'stock' => $quantityData['stock'] ?? 0,
                                    'is_active' => $quantityData['active'] ?? true,
                                    'sort_order' => $sortOrder++,
                                    'attributes_json' => [],
                                ]);
                            }
                        }

                        // Show success notification
                        \Filament\Notifications\Notification::make()
                            ->title('Variants Updated Successfully')
                            ->success()
                            ->body('Product variants have been configured and saved.')
                            ->send();
                    }),

                Tables\Actions\CreateAction::make()
                    ->label('Quick Add')
                    ->icon('heroicon-o-plus'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('variant_type', 'asc')
            ->groups([
                'variant_type',
            ])
            ->groupingSettingsHidden(false);
    }
}