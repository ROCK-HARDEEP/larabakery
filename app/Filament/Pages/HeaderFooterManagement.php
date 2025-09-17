<?php

namespace App\Filament\Pages;

use App\Models\HeaderFooterSettings;
use Filament\Pages\Page;

class HeaderFooterManagement extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-swatch';
    protected static ?string $navigationLabel = 'Header/Footer';
    protected static ?string $navigationGroup = 'Pages';
    protected static ?int $navigationSort = 4;
    protected static bool $shouldRegisterNavigation = true;
    protected static string $view = 'filament.pages.header-footer-management';

    public function getIsActive(): bool
    {
        return HeaderFooterSettings::query()->exists();
    }
}


