<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomePageCategoryResource\Pages;
use App\Filament\Resources\HomePageCategoryResource\RelationManagers;
use App\Models\HomePageCategory;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HomePageCategoryResource extends Resource
{
    protected static ?string $model = HomePageCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationGroup = null;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationLabel = 'Shop by Categories';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->options(Category::where('is_active', true)->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->placeholder('Select a category'),
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
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
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
                    ->modalHeading('Edit Category')
                    ->modalWidth('md')
                    ->form(function (Form $form): Form {
                        return $form
                            ->schema([
                                Forms\Components\Select::make('category_id')
                                    ->label('Category')
                                    ->options(Category::where('is_active', true)->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Select a category'),
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
                    ->modalHeading('Delete Category')
                    ->modalDescription('Are you sure you want to remove this category from the homepage?'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Category')
                    ->modalHeading('Add Category to Homepage')
                    ->modalWidth('md')
                    ->form(function (Form $form): Form {
                        return $form
                            ->schema([
                                Forms\Components\Select::make('category_id')
                                    ->label('Category')
                                    ->options(Category::where('is_active', true)->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Select a category'),
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Display Order')
                                    ->required()
                                    ->numeric()
                                    ->default(fn () => HomePageCategory::max('sort_order') + 1 ?? 0)
                                    ->helperText('Lower numbers appear first'),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Show on Homepage')
                                    ->required()
                                    ->default(true),
                            ]);
                    })
                    ->mutateFormDataUsing(function (array $data): array {
                        if (!isset($data['sort_order']) || $data['sort_order'] === null) {
                            $data['sort_order'] = HomePageCategory::max('sort_order') + 1 ?? 0;
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
            'index' => Pages\ListHomePageCategories::route('/'),
            // Removed create and edit pages - using modals instead
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
