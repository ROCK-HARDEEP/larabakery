<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PopularProductResource\Pages;
use App\Filament\Resources\PopularProductResource\RelationManagers;
use App\Models\PopularProduct;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PopularProductResource extends Resource
{
    protected static ?string $model = PopularProduct::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationGroup = null;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationLabel = 'Popular Products';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Product')
                    ->options(Product::where('is_active', true)->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->placeholder('Select a product'),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Display Order')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower numbers appear first'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Show on Homepage')
                    ->required()
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->width('60px')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('product.image')
                    ->label('Image')
                    ->circular()
                    ->defaultImageUrl(url('/img/placeholder-product.jpg')),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('product.min_price')
                    ->label('Price')
                    ->money('INR')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->product->min_price ?? 0),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Edit Popular Product')
                    ->modalWidth('md')
                    ->form(function (Form $form): Form {
                        return $form
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Product')
                                    ->options(Product::where('is_active', true)->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Select a product'),
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Display Order')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Lower numbers appear first'),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Show on Homepage')
                                    ->required()
                                    ->default(true),
                            ]);
                    }),
                Tables\Actions\DeleteAction::make()
                    ->modalHeading('Remove Popular Product')
                    ->modalDescription('Are you sure you want to remove this product from popular products?'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Popular Product')
                    ->modalHeading('Add to Popular Products')
                    ->modalWidth('md')
                    ->form(function (Form $form): Form {
                        return $form
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Product')
                                    ->options(Product::where('is_active', true)->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Select a product'),
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Display Order')
                                    ->required()
                                    ->numeric()
                                    ->default(fn () => PopularProduct::max('sort_order') + 1 ?? 0)
                                    ->helperText('Lower numbers appear first'),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Show on Homepage')
                                    ->required()
                                    ->default(true),
                            ]);
                    })
                    ->mutateFormDataUsing(function (array $data): array {
                        if (!isset($data['sort_order']) || $data['sort_order'] === null) {
                            $data['sort_order'] = PopularProduct::max('sort_order') + 1 ?? 0;
                        }
                        return $data;
                    }),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPopularProducts::route('/'),
            // Removed create and edit pages - using modals instead
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
