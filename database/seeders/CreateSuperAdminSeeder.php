<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdminSeeder extends Seeder
{
    public function run()
    {
        // Create admin role if it doesn't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Create super-admin role if it doesn't exist
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);

        // Create permissions if they don't exist
        $permissions = [
            'access-admin-panel',
            'manage-users',
            'manage-products',
            'manage-categories',
            'manage-orders',
            'manage-settings',
            'view-reports',
            'manage-content',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all permissions to admin and super-admin roles
        $adminRole->syncPermissions(Permission::all());
        $superAdminRole->syncPermissions(Permission::all());

        // Create super admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@bakeryshop.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Admin@123'),
                'email_verified_at' => now(),
                'phone' => '9999999999',
            ]
        );

        // Assign super-admin role
        $superAdmin->assignRole('super-admin');
        $superAdmin->assignRole('admin'); // Also assign admin role for Filament access

        // Create regular admin user
        $admin = User::firstOrCreate(
            ['email' => 'manager@bakeryshop.com'],
            [
                'name' => 'Store Manager',
                'password' => Hash::make('Manager@123'),
                'email_verified_at' => now(),
                'phone' => '8888888888',
            ]
        );

        // Assign admin role
        $admin->assignRole('admin');

        $this->command->info('Super Admin created successfully!');
        $this->command->info('Email: admin@bakeryshop.com');
        $this->command->info('Password: Admin@123');
        $this->command->info('');
        $this->command->info('Store Manager created successfully!');
        $this->command->info('Email: manager@bakeryshop.com');
        $this->command->info('Password: Manager@123');
    }
}