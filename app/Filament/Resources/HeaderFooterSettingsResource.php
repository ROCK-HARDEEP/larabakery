<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeaderFooterSettingsResource\Pages;
use App\Models\HeaderFooterSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;

class HeaderFooterSettingsResource extends Resource
{
    protected static ?string $model = HeaderFooterSettings::class;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';
    protected static ?string $navigationGroup = 'Pages';
    protected static ?string $navigationLabel = 'Header/Footer';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Header')
                    ->schema([
                        Forms\Components\FileUpload::make('header_logo')
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public')
                            ->optimizeToWebP(),
                        Forms\Components\TextInput::make('header_brand_name')->maxLength(255),
                    ]),
                Forms\Components\Section::make('Announcement Bar')
                    ->schema([
                        Forms\Components\Toggle::make('announcement_bar_enabled'),
                        Forms\Components\TextInput::make('announcement_bar_text')->maxLength(255),
                        Forms\Components\ColorPicker::make('announcement_bar_bg_color'),
                        Forms\Components\ColorPicker::make('announcement_bar_text_color'),
                    ]),
                Forms\Components\Section::make('Footer')
                    ->schema([
                        Forms\Components\FileUpload::make('footer_logo')
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public')
                            ->optimizeToWebP(),
                        Forms\Components\TextInput::make('footer_brand_name')->maxLength(255),
                        Forms\Components\Textarea::make('footer_description')->columnSpanFull(),
                        Forms\Components\ColorPicker::make('footer_background_color'),
                        Forms\Components\ColorPicker::make('footer_text_color'),
                    ]),
                Forms\Components\Section::make('Links')
                    ->schema([
                        Forms\Components\Repeater::make('social_media_links')
                            ->schema([
                                Forms\Components\TextInput::make('platform'),
                                Forms\Components\TextInput::make('url')->url(),
                                Forms\Components\TextInput::make('icon'),
                            ])->columns(3),
                        Forms\Components\Repeater::make('quick_links')
                            ->schema([
                                Forms\Components\TextInput::make('title'),
                                Forms\Components\TextInput::make('url'),
                            ])->columns(2),
                        Forms\Components\Repeater::make('category_links')
                            ->schema([
                                Forms\Components\TextInput::make('title'),
                                Forms\Components\TextInput::make('url'),
                            ])->columns(2),
                        Forms\Components\Repeater::make('customer_service_links')
                            ->schema([
                                Forms\Components\TextInput::make('title'),
                                Forms\Components\TextInput::make('url'),
                            ])->columns(2),
                    ]),
                Forms\Components\Section::make('Contact')
                    ->schema([
                        Forms\Components\TextInput::make('contact_address'),
                        Forms\Components\TextInput::make('contact_phone'),
                        Forms\Components\TextInput::make('contact_email')->email(),
                        Forms\Components\TextInput::make('contact_hours'),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'edit' => Pages\EditHeaderFooterSettings::route('/'),
        ];
    }

    public static function getNavigationUrl(): string
    {
        return static::getUrl('edit');
    }
}


