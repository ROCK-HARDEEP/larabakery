<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AboutUsResource\Pages;
use App\Models\AboutUs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;

class AboutUsResource extends Resource
{
    protected static ?string $model = AboutUs::class;

    protected static ?string $navigationIcon = 'heroicon-o-information-circle';

    protected static ?string $navigationGroup = 'Pages';

    protected static ?string $navigationLabel = 'About Page';

    protected static ?string $modelLabel = 'About Page';

    protected static ?int $navigationSort = 3;

    // Disable the resource interface - we'll use a custom page
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('How It All Began')
                    ->description('Tell the story of how your bakery started')
                    ->schema([
                        Forms\Components\TextInput::make('began_title')
                            ->label('Section Title')
                            ->default('How It All Began')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('began_quote')
                            ->label('Section Quote')
                            ->default('A journey of passion, tradition, and community')
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('began_content')
                            ->label('Our Beginning Story')
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'h2', 'h3',
                                'bulletList', 'orderedList', 'link', 'blockquote'
                            ])
                            ->columnSpanFull(),
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('years_experience')
                                    ->label('Years of Experience')
                                    ->placeholder('10+')
                                    ->maxLength(10),
                                Forms\Components\TextInput::make('happy_customers')
                                    ->label('Happy Customers')
                                    ->placeholder('1000+')
                                    ->maxLength(10),
                            ]),
                    ]),

                Section::make('Core Values')
                    ->description('The values and principles that guide your bakery')
                    ->schema([
                        Forms\Components\Repeater::make('values')
                            ->label('Our Values')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Value Title')
                                            ->required()
                                            ->placeholder('e.g., Quality First'),
                                        Forms\Components\TextInput::make('icon')
                                            ->label('Icon (Optional)')
                                            ->placeholder('heroicon-o-heart')
                                            ->helperText('Heroicon class name'),
                                    ]),
                                Forms\Components\Textarea::make('description')
                                    ->label('Description')
                                    ->required()
                                    ->rows(3)
                                    ->placeholder('Describe this core value...')
                                    ->columnSpanFull(),
                            ])
                            ->defaultItems(3)
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                            ->columnSpanFull(),
                    ]),

                Section::make('Team Members')
                    ->description('Showcase your talented team')
                    ->schema([
                        Forms\Components\Repeater::make('team_members')
                            ->label('Team Members')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Name')
                                            ->required()
                                            ->placeholder('John Doe'),
                                        Forms\Components\TextInput::make('designation')
                                            ->label('Position/Designation')
                                            ->required()
                                            ->placeholder('Head Baker'),
                                        Forms\Components\TextInput::make('image')
                                            ->label('Image Path')
                                            ->placeholder('team-members/photo.jpg'),
                                    ]),
                                Forms\Components\Textarea::make('description')
                                    ->label('Short Bio/Description')
                                    ->rows(3)
                                    ->placeholder('Brief description about this team member...')
                                    ->columnSpanFull(),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Show on website')
                                    ->default(true)
                                    ->inline(),
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Sort Order')
                                    ->numeric()
                                    ->default(1),
                            ])
                            ->defaultItems(2)
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                            ->columnSpanFull(),
                    ]),

                Section::make('Ready to Taste Section')
                    ->description('Bottom call-to-action section')
                    ->schema([
                        Forms\Components\TextInput::make('cta_title')
                            ->label('CTA Title')
                            ->default('Ready to Taste the Difference?')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('cta_subtitle')
                            ->label('CTA Subtitle')
                            ->rows(2)
                            ->default('Experience the love and tradition in every bite')
                            ->maxLength(500),
                        Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('cta_button_text')
                                    ->label('Button Text')
                                    ->default('Shop Our Products')
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('cta_button_link')
                                    ->label('Button Link')
                                    ->url()
                                    ->default('/products'),
                                Forms\Components\ColorPicker::make('cta_button_color')
                                    ->label('Button Color')
                                    ->default('#FF6B00'),
                            ]),
                        Forms\Components\ColorPicker::make('cta_section_color')
                            ->label('Section Background Color')
                            ->default('#000000'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('story_title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('story_quote')
                    ->label('Quote')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('values')
                    ->label('Values')
                    ->getStateUsing(function ($record) {
                        $values = is_array($record->values) ? $record->values : [];
                        return count($values) . ' values';
                    })
                    ->badge(),
                Tables\Columns\TextColumn::make('team_members')
                    ->label('Team Members')
                    ->getStateUsing(function ($record) {
                        $members = is_array($record->team_members) ? $record->team_members : [];
                        return count($members) . ' members';
                    })
                    ->badge(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->paginated(false);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAboutUs::route('/'),
            'create' => Pages\CreateAboutUs::route('/create'),
            'edit' => Pages\EditAboutUs::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return AboutUs::count() === 0;
    }
}