<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['email_verified_at'] = now();
        return $data;
    }

    protected function afterCreate(): void
    {
        $user = $this->record;
        $roleName = $this->data['role'] ?? 'customer';

        // Assign role to user
        if ($role = \App\Models\Role::where('name', $roleName)->first()) {
            $user->assignRole($role);
        } else {
            // Fallback to customer role
            if ($customerRole = \App\Models\Role::where('name', 'customer')->first()) {
                $user->assignRole($customerRole);
            }
        }

        \Filament\Notifications\Notification::make()
            ->title('User Created Successfully')
            ->body("User '{$user->name}' has been created with role '{$roleName}'.")
            ->success()
            ->send();
    }
}