<?php

namespace App\Filament\Resources\HomePageCategoryResource\Pages;

use App\Filament\Resources\HomePageCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHomePageCategory extends EditRecord
{
    protected static string $resource = HomePageCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
