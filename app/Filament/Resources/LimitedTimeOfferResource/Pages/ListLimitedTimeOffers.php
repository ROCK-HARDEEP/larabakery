<?php

namespace App\Filament\Resources\LimitedTimeOfferResource\Pages;

use App\Filament\Resources\LimitedTimeOfferResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLimitedTimeOffers extends ListRecords
{
    protected static string $resource = LimitedTimeOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}