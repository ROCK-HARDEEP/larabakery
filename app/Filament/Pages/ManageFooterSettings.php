<?php

namespace App\Filament\Pages;

use App\Models\HeaderFooterSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ManageFooterSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static string $view = 'filament.pages.manage-footer-settings';
    protected static bool $shouldRegisterNavigation = false;
    
    public ?array $data = [];
    
    public function mount(): void
    {
        $settings = HeaderFooterSettings::first();
        if (!$settings) {
            $settings = HeaderFooterSettings::create();
        }
        
        $this->form->fill([
            'footer_logo' => $settings->footer_logo,
            'footer_brand_name' => $settings->footer_brand_name,
            'footer_description' => $settings->footer_description,
            'footer_background_color' => $settings->footer_background_color,
            'footer_text_color' => $settings->footer_text_color,
            'social_media_links' => $settings->social_media_links,
            'quick_links' => $settings->quick_links,
            'category_links' => $settings->category_links,
            'customer_service_links' => $settings->customer_service_links,
            'contact_address' => $settings->contact_address,
            'contact_phone' => $settings->contact_phone,
            'contact_email' => $settings->contact_email,
            'contact_hours' => $settings->contact_hours,
        ]);
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Footer Branding')
                    ->description('Configure your footer branding and appearance')
                    ->schema([
                        Forms\Components\FileUpload::make('footer_logo')
                            ->label('Footer Logo')
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public')
                            ->optimizeToWebP(),
                        Forms\Components\TextInput::make('footer_brand_name')
                            ->label('Brand Name')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('footer_description')
                            ->label('Footer Description')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\ColorPicker::make('footer_background_color')
                            ->label('Background Color'),
                        Forms\Components\ColorPicker::make('footer_text_color')
                            ->label('Text Color'),
                    ]),
                Forms\Components\Section::make('Footer Links')
                    ->description('Manage various link sections in the footer')
                    ->schema([
                        Forms\Components\Repeater::make('social_media_links')
                            ->label('Social Media Links')
                            ->schema([
                                Forms\Components\TextInput::make('platform')
                                    ->label('Platform'),
                                Forms\Components\TextInput::make('url')
                                    ->label('URL')
                                    ->url(),
                                Forms\Components\TextInput::make('icon')
                                    ->label('Icon Class'),
                            ])->columns(3),
                        Forms\Components\Repeater::make('quick_links')
                            ->label('Quick Links')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Title'),
                                Forms\Components\TextInput::make('url')
                                    ->label('URL'),
                            ])->columns(2),
                        Forms\Components\Repeater::make('category_links')
                            ->label('Category Links')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Title'),
                                Forms\Components\TextInput::make('url')
                                    ->label('URL'),
                            ])->columns(2),
                        Forms\Components\Repeater::make('customer_service_links')
                            ->label('Customer Service Links')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Title'),
                                Forms\Components\TextInput::make('url')
                                    ->label('URL'),
                            ])->columns(2),
                    ]),
                Forms\Components\Section::make('Contact Information')
                    ->description('Footer contact details')
                    ->schema([
                        Forms\Components\TextInput::make('contact_address')
                            ->label('Address'),
                        Forms\Components\TextInput::make('contact_phone')
                            ->label('Phone'),
                        Forms\Components\TextInput::make('contact_email')
                            ->label('Email')
                            ->email(),
                        Forms\Components\TextInput::make('contact_hours')
                            ->label('Business Hours'),
                    ])->columns(2),
            ])
            ->statePath('data');
    }
    
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Footer Settings')
                ->submit('save'),
        ];
    }
    
    public function save(): void
    {
        $data = $this->form->getState();
        
        $settings = HeaderFooterSettings::first();
        $settings->update([
            'footer_logo' => $data['footer_logo'],
            'footer_brand_name' => $data['footer_brand_name'],
            'footer_description' => $data['footer_description'],
            'footer_background_color' => $data['footer_background_color'],
            'footer_text_color' => $data['footer_text_color'],
            'social_media_links' => $data['social_media_links'],
            'quick_links' => $data['quick_links'],
            'category_links' => $data['category_links'],
            'customer_service_links' => $data['customer_service_links'],
            'contact_address' => $data['contact_address'],
            'contact_phone' => $data['contact_phone'],
            'contact_email' => $data['contact_email'],
            'contact_hours' => $data['contact_hours'],
        ]);
        
        Notification::make()
            ->title('Footer settings saved successfully')
            ->success()
            ->send();
    }
}