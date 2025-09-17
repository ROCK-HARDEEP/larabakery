<?php

namespace App\Filament\Resources\HeroSlideResource\Pages;

use App\Filament\Resources\HeroSlideResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateHeroSlide extends CreateRecord
{
    protected static string $resource = HeroSlideResource::class;
    
    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.pages.home-page') => 'Home Page',
            HeroSlideResource::getUrl() => 'Hero Slides',
            url()->current() => 'Create',
        ];
    }
}
