<?php

namespace App\Filament\Resources\HeaderFooterSettingsResource\Pages;

use App\Filament\Resources\HeaderFooterSettingsResource;
use Filament\Resources\Pages\EditRecord;
use App\Models\HeaderFooterSettings;

class EditHeaderFooterSettings extends EditRecord
{
    protected static string $resource = HeaderFooterSettingsResource::class;

    public function mount($record = null): void
    {
        $settings = HeaderFooterSettings::first();
        if (!$settings) {
            $settings = HeaderFooterSettings::create();
        }
        parent::mount($settings->getKey());
    }

    public function getBreadcrumbs(): array
    {
        return [
            url()->to('/admin') => 'Dashboard',
            static::getResource()::getUrl('edit') => 'Header/Footer Settings',
        ];
    }
}


