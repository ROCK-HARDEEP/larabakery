<?php

namespace App\Filament\Resources\HomePageCategoryResource\Pages;

use App\Filament\Resources\HomePageCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHomePageCategories extends ListRecords
{
    protected static string $resource = HomePageCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Removed the Create button - using modal in table instead
        ];
    }
    
    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.pages.home-page') => 'Home Page',
            url()->current() => 'Shop by Category',
        ];
    }
}
