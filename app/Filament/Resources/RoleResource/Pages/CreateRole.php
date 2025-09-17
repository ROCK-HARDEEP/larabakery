<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['is_custom'] = true;
        $data['guard_name'] = 'web';

        return $data;
    }

    protected function afterCreate(): void
    {
        $modulePermissions = $this->data['module_permissions'] ?? [];

        foreach ($modulePermissions as $moduleId => $permissions) {
            if (is_array($permissions) && array_filter($permissions)) {
                \App\Models\RoleModulePermission::create([
                    'role_id' => $this->record->id,
                    'module_permission_id' => $moduleId,
                    'can_view' => $permissions['can_view'] ?? false,
                    'can_create' => $permissions['can_create'] ?? false,
                    'can_update' => $permissions['can_update'] ?? false,
                    'can_delete' => $permissions['can_delete'] ?? false,
                    'can_export' => $permissions['can_export'] ?? false,
                ]);
            }
        }

        \Filament\Notifications\Notification::make()
            ->title('Role Created Successfully')
            ->body("Role '{$this->record->name}' has been created with permissions.")
            ->success()
            ->send();
    }
}
