<?php

namespace App\Filament\Resources\HomepageFaqResource\Pages;

use App\Filament\Resources\HomepageFaqResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageHomepageFaqs extends ManageRecords
{
    protected static string $resource = HomepageFaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
