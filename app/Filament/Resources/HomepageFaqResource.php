<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomepageFaqResource\Pages;
use App\Filament\Resources\HomepageFaqResource\RelationManagers;
use App\Models\HomepageFaq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HomepageFaqResource extends Resource
{
    protected static ?string $model = HomepageFaq::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $modelLabel = 'Homepage FAQ';
    protected static ?string $pluralModelLabel = 'Homepage FAQs';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('question')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter the FAQ question'),
                Forms\Components\Textarea::make('answer')
                    ->required()
                    ->rows(4)
                    ->placeholder('Enter the detailed answer'),
                Forms\Components\TextInput::make('order_index')
                    ->numeric()
                    ->default(0)
                    ->label('Display Order')
                    ->helperText('Lower numbers appear first'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->helperText('Toggle to show/hide this FAQ'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('answer')
                    ->limit(100)
                    ->wrap(),
                Tables\Columns\TextColumn::make('order_index')
                    ->label('Order')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order_index', 'asc')
            ->reorderable('order_index')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageHomepageFaqs::route('/'),
        ];
    }
}
