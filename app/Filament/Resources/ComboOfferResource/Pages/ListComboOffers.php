<?php

namespace App\Filament\Resources\ComboOfferResource\Pages;

use App\Filament\Resources\ComboOfferResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListComboOffers extends ListRecords
{
    protected static string $resource = ComboOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}