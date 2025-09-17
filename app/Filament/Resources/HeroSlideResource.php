<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeroSlideResource\Pages;
use App\Filament\Resources\HeroSlideResource\RelationManagers;
use App\Models\HeroSlide;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HeroSlideResource extends Resource
{
    protected static ?string $model = HeroSlide::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = null;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Slide Content')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Main Title')
                            ->required(),
                        Forms\Components\Textarea::make('subtitle')
                            ->label('Subtitle')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('button_label')
                            ->label('Button Text'),
                    ])->columns(2),

                Forms\Components\Section::make('Hero Image')
                    ->schema([
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Slide Background Image')
                            ->image()
                            ->imageEditor()
                            ->imagePreviewHeight('200')
                            ->directory('hero-slides')
                            ->visibility('public')
                            ->disk('public')
                            ->helperText('Upload a hero slide image. Recommended size: 1920x800px')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Link Destination')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('Link to Category')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('product_id')
                            ->label('Link to Product')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload(),
                    ])->columns(2)
                    ->description('Choose either a category or product to link to when button is clicked'),

                Forms\Components\Section::make('Styling & Settings')
                    ->schema([
                        Forms\Components\ColorPicker::make('title_color')
                            ->label('Title Color')
                            ->default('#ffffff'),
                        Forms\Components\ColorPicker::make('subtitle_color')
                            ->label('Subtitle Color')
                            ->default('#f0f0f0'),
                        Forms\Components\TextInput::make('title_size')
                            ->label('Title Font Size')
                            ->numeric()
                            ->suffix('px')
                            ->default(48),
                        Forms\Components\TextInput::make('subtitle_size')
                            ->label('Subtitle Font Size')
                            ->numeric()
                            ->suffix('px')
                            ->default(18),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Display Order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subtitle')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image_path'),
                Tables\Columns\TextColumn::make('button_label')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Linked Category')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Linked Product')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('title_color')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subtitle_color')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title_size')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subtitle_size')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHeroSlides::route('/'),
            'create' => Pages\CreateHeroSlide::route('/create'),
            'edit' => Pages\EditHeroSlide::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
