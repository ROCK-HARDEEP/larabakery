<?php

namespace App\Filament\Pages;

use App\Models\HeaderFooterSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ManageHeaderSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static string $view = 'filament.pages.manage-header-settings';
    protected static bool $shouldRegisterNavigation = false;
    
    public ?array $data = [];
    
    public function mount(): void
    {
        $settings = HeaderFooterSettings::first();
        if (!$settings) {
            $settings = HeaderFooterSettings::create();
        }
        
        $this->form->fill([
            'header_logo' => $settings->header_logo,
            'header_brand_name' => $settings->header_brand_name,
            'announcement_bar_enabled' => $settings->announcement_bar_enabled,
            'announcement_bar_text' => $settings->announcement_bar_text,
            'announcement_bar_bg_color' => $settings->announcement_bar_bg_color,
            'announcement_bar_text_color' => $settings->announcement_bar_text_color,
        ]);
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Header Settings')
                    ->description('Configure your website header')
                    ->schema([
                        Forms\Components\FileUpload::make('header_logo')
                            ->label('Header Logo')
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public')
                            ->optimizeToWebP(),
                        Forms\Components\TextInput::make('header_brand_name')
                            ->label('Brand Name')
                            ->maxLength(255),
                    ]),
                Forms\Components\Section::make('Announcement Bar')
                    ->description('Configure the announcement bar that appears above the header')
                    ->schema([
                        Forms\Components\Toggle::make('announcement_bar_enabled')
                            ->label('Enable Announcement Bar'),
                        Forms\Components\TextInput::make('announcement_bar_text')
                            ->label('Announcement Text')
                            ->maxLength(255),
                        Forms\Components\ColorPicker::make('announcement_bar_bg_color')
                            ->label('Background Color'),
                        Forms\Components\ColorPicker::make('announcement_bar_text_color')
                            ->label('Text Color'),
                    ]),
            ])
            ->statePath('data');
    }
    
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Header Settings')
                ->submit('save'),
        ];
    }
    
    public function save(): void
    {
        $data = $this->form->getState();
        
        $settings = HeaderFooterSettings::first();
        $settings->update([
            'header_logo' => $data['header_logo'],
            'header_brand_name' => $data['header_brand_name'],
            'announcement_bar_enabled' => $data['announcement_bar_enabled'],
            'announcement_bar_text' => $data['announcement_bar_text'],
            'announcement_bar_bg_color' => $data['announcement_bar_bg_color'],
            'announcement_bar_text_color' => $data['announcement_bar_text_color'],
        ]);
        
        Notification::make()
            ->title('Header settings saved successfully')
            ->success()
            ->send();
    }
}