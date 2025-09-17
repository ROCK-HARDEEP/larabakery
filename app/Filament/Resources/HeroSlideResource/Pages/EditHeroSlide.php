<?php

namespace App\Filament\Resources\HeroSlideResource\Pages;

use App\Filament\Resources\HeroSlideResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHeroSlide extends EditRecord
{
    protected static string $resource = HeroSlideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.pages.home-page') => 'Home Page',
            HeroSlideResource::getUrl() => 'Hero Slides',
            url()->current() => 'Edit',
        ];
    }
}
