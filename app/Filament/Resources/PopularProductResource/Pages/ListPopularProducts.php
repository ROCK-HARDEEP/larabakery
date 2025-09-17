<?php

namespace App\Filament\Resources\PopularProductResource\Pages;

use App\Filament\Resources\PopularProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPopularProducts extends ListRecords
{
    protected static string $resource = PopularProductResource::class;

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
            url()->current() => 'Popular Products',
        ];
    }
}
