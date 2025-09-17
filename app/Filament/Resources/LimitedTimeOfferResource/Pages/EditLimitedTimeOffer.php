<?php

namespace App\Filament\Resources\LimitedTimeOfferResource\Pages;

use App\Filament\Resources\LimitedTimeOfferResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLimitedTimeOffer extends EditRecord
{
    protected static string $resource = LimitedTimeOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}