<?php

namespace App\Filament\Resources\ComboOfferResource\Pages;

use App\Filament\Resources\ComboOfferResource;
use Filament\Resources\Pages\CreateRecord;

class CreateComboOffer extends CreateRecord
{
    protected static string $resource = ComboOfferResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}