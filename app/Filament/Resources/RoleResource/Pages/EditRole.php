<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn ($record) => !$record->isSystemRole()),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $modulePermissions = [];

        foreach ($this->record->modulePermissions as $permission) {
            $modulePermissions[$permission->module_permission_id] = [
                'can_view' => $permission->can_view,
                'can_create' => $permission->can_create,
                'can_update' => $permission->can_update,
                'can_delete' => $permission->can_delete,
                'can_export' => $permission->can_export,
            ];
        }

        $data['module_permissions'] = $modulePermissions;

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->modulePermissions()->delete();

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
            ->title('Role Updated Successfully')
            ->body("Role '{$this->record->name}' has been updated with new permissions.")
            ->success()
            ->send();
    }
}