<?php

namespace App\Filament\Resources\AdminResource\Pages;

use App\Filament\Resources\AdminResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAdmin extends CreateRecord
{
    protected static string $resource = AdminResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $role = $data['role'] ?? 'admin';
        unset($data['role']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $role = request()->input('data.role', 'admin');
        $this->record->assignRole($role);
    }
}