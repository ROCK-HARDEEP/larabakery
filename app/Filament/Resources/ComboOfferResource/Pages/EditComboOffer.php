<?php

namespace App\Filament\Resources\ComboOfferResource\Pages;

use App\Filament\Resources\ComboOfferResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditComboOffer extends EditRecord
{
    protected static string $resource = ComboOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}