<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Models\Testimonial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';
    protected static ?string $navigationGroup = null;
    protected static ?string $navigationLabel = 'Testimonials';
    protected static ?int $navigationSort = 7;
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Customer Information')
                    ->schema([
                        Forms\Components\TextInput::make('customer_name')
                            ->label('Customer Name')
                            ->required(),
                        Forms\Components\TextInput::make('customer_position')
                            ->label('Position/Title')
                            ->helperText('Optional: e.g., Marketing Manager'),
                        Forms\Components\TextInput::make('customer_company')
                            ->label('Company')
                            ->helperText('Optional: Company or business name'),
                        Forms\Components\TextInput::make('location')
                            ->label('Location')
                            ->helperText('Optional: City or area'),
                        Forms\Components\DatePicker::make('review_date')
                            ->label('Review Date')
                            ->default(today())
                            ->helperText('When the review was given'),
                    ])->columns(2),

                Forms\Components\Section::make('Review Details')
                    ->schema([
                        Forms\Components\Textarea::make('review')
                            ->label('Customer Review')
                            ->required()
                            ->rows(4)
                            ->helperText('The customer testimonial text')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('rating')
                            ->label('Rating')
                            ->options([
                                5 => '⭐⭐⭐⭐⭐ (5 stars)',
                                4 => '⭐⭐⭐⭐☆ (4 stars)',
                                3 => '⭐⭐⭐☆☆ (3 stars)',
                                2 => '⭐⭐☆☆☆ (2 stars)',
                                1 => '⭐☆☆☆☆ (1 star)',
                            ])
                            ->default(5)
                            ->required(),
                        Forms\Components\TextInput::make('product_reviewed')
                            ->label('Product Reviewed')
                            ->helperText('Optional: Specific product they reviewed'),
                    ])->columns(2),

                Forms\Components\Section::make('Customer Photo')
                    ->schema([
                        Forms\Components\FileUpload::make('customer_image')
                            ->label('Customer Photo')
                            ->image()
                            ->imageEditor()
                            ->imagePreviewHeight('150')
                            ->directory('testimonials')
                            ->visibility('public')
                            ->disk('public')
                            ->helperText('Upload customer photo. Recommended size: 200x200px (optional)')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured Testimonial')
                            ->helperText('Featured testimonials appear prominently'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Only active testimonials are shown on website'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('customer_image')
                    ->label('Photo')
                    ->circular()
                    ->size(50),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_company')
                    ->label('Company')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('review')
                    ->label('Review')
                    ->limit(50)
                    ->wrap(),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn ($state) => str_repeat('⭐', $state) . str_repeat('☆', 5 - $state))
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured'),
                Tables\Filters\SelectFilter::make('rating')
                    ->options([
                        5 => '5 Stars',
                        4 => '4 Stars',
                        3 => '3 Stars',
                        2 => '2 Stars',
                        1 => '1 Star',
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
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order');
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
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}