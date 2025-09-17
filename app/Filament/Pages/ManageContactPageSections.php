<?php

namespace App\Filament\Pages;

use App\Models\CustomSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\CreateAction;
use Illuminate\Database\Eloquent\Builder;

class ManageContactPageSections extends Page implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = null;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.manage-contact-page-sections';
    
    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.pages.contact-page') => 'Contact Page',
            url()->current() => 'Custom Sections',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->model(CustomSection::class)
                ->form($this->getFormSchema())
                ->mutateFormDataUsing(function (array $data): array {
                    $data['page'] = 'contact';
                    // Set default values for nullable fields
                    $data['title'] = $data['title'] ?? null;
                    $data['subtitle'] = $data['subtitle'] ?? null;
                    $data['content'] = $data['content'] ?? null;
                    return $data;
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(CustomSection::query()->where('page', 'contact'))
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->square()
                    ->size(60),
                Tables\Columns\TextColumn::make('layout')
                    ->badge(),
                Tables\Columns\SelectColumn::make('position')
                    ->options($this->getPositionOptions())
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('layout')
                    ->options([
                        'default' => 'Default',
                        'left-image' => 'Left Image',
                        'right-image' => 'Right Image',
                        'hero' => 'Hero',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form($this->getFormSchema()),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('position', 'asc')
            ->reorderable('position');
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\FileUpload::make('image')
                ->image()
                ->disk('public')
                ->directory('custom-sections')
                ->visibility('public')
                            ->optimizeToWebP(),
            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\TextInput::make('button_text')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('button_link')
                        ->url()
                        ->maxLength(255),
                ]),
            Forms\Components\Select::make('button_style')
                ->options([
                    'btn-primary' => 'Primary',
                    'btn-secondary' => 'Secondary',
                    'btn-success' => 'Success',
                    'btn-danger' => 'Danger',
                ])
                ->default('btn-primary'),
            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\ColorPicker::make('background_color')
                        ->default('#ffffff'),
                    Forms\Components\ColorPicker::make('text_color')
                        ->default('#000000'),
                ]),
            Forms\Components\Select::make('layout')
                ->options([
                    'default' => 'Default',
                    'left-image' => 'Left Image',
                    'right-image' => 'Right Image',
                    'hero' => 'Hero',
                ])
                ->default('default')
                ->required(),
            Forms\Components\Select::make('position')
                ->label('Position in Page')
                ->options($this->getPositionOptions())
                ->helperText('Select where this section should appear on the page')
                ->required()
                ->default('after_contact_form'),
            Forms\Components\Toggle::make('is_active')
                ->default(true),
        ];
    }

    protected function getPositionOptions(): array
    {
        return [
            'before_header' => 'Before Page Header',
            'after_header' => 'After Page Header',
            'before_contact_form' => 'Before Contact Form',
            'after_contact_form' => 'After Contact Form',
            'before_map' => 'Before Map Section',
            'after_map' => 'After Map Section',
            'before_contact_info' => 'Before Contact Information',
            'after_contact_info' => 'After Contact Information',
            'before_hours' => 'Before Business Hours',
            'after_hours' => 'After Business Hours',
            'end_of_page' => 'End of Page',
        ];
    }
}