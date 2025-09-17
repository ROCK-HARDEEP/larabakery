<?php
// This is a temporary file to show the role selector code that needs to be added to UserResource

// Add this section right before the "User Tags & Segmentation" section in UserResource form:

/*
Section::make('Role Assignment')
    ->description('Assign role to user (superadmin only)')
    ->schema([
        Forms\Components\Select::make('role')
            ->label('User Role')
            ->options(function () {
                $user = auth()->user();

                if (!$user || !$user->hasRole('superadmin')) {
                    return ['customer' => 'Customer (Default)'];
                }

                // Get all available roles
                $roles = \App\Models\Role::where('is_active', true)
                    ->pluck('name', 'name')
                    ->toArray();

                return $roles;
            })
            ->required()
            ->default('customer')
            ->helperText('Select role for this user')
            ->disabled(function () {
                $user = auth()->user();
                return !$user || !$user->hasRole('superadmin');
            })
            ->afterStateHydrated(function ($component, $state, $record) {
                if ($record && $record->roles->count() > 0) {
                    $component->state($record->roles->first()->name);
                } else {
                    $component->state('customer');
                }
            }),
    ])
    ->visible(function () {
        $user = auth()->user();
        return $user && $user->hasRole('superadmin');
    }),
*/