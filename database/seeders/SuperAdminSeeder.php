<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles if they don't exist
        if (!Role::where('name', 'superadmin')->exists()) {
            Role::create(['name' => 'superadmin', 'guard_name' => 'web']);
        }

        if (!Role::where('name', 'admin')->exists()) {
            Role::create(['name' => 'admin', 'guard_name' => 'web']);
        }

        // Create superadmin user
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@bakeryshop.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('SuperAdmin@2025'),
                'phone' => '+91 9876543210',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]
        );

        // Assign superadmin role
        $superadmin->assignRole('superadmin');

        // Create a regular admin user for testing
        $admin = User::firstOrCreate(
            ['email' => 'admin@bakeryshop.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('Admin@2025'),
                'phone' => '+91 9876543211',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]
        );

        // Assign admin role
        $admin->assignRole('admin');

        $this->command->info('Super Admin created successfully!');
        $this->command->info('Email: superadmin@bakeryshop.com');
        $this->command->info('Password: SuperAdmin@2025');
        $this->command->info('');
        $this->command->info('Admin User created successfully!');
        $this->command->info('Email: admin@bakeryshop.com');
        $this->command->info('Password: Admin@2025');
    }
}