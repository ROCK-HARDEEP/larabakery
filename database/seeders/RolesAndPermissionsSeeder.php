<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // Customer Management
            'view_customers',
            'create_customers',
            'edit_customers',
            'delete_customers',

            // Admin User Management
            'view_admins',
            'create_admins',
            'edit_admins',
            'delete_admins',

            // Product Management
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',

            // Order Management
            'view_orders',
            'create_orders',
            'edit_orders',
            'delete_orders',

            // Category Management
            'view_categories',
            'create_categories',
            'edit_categories',
            'delete_categories',

            // All other resources
            'manage_all_resources',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create Roles

        // 1. SUPERADMIN ROLE - Has ALL permissions
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        $superadminRole->givePermissionTo(Permission::all());

        // 2. ADMIN ROLE - Limited permissions (can only view customers)
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo([
            'view_customers',  // Can ONLY view customers, no create/edit/delete
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            'view_orders',
            'create_orders',
            'edit_orders',
            'delete_orders',
            'view_categories',
            'create_categories',
            'edit_categories',
            'delete_categories',
        ]);

        // Create or update superadmin user
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

        // Remove all roles and assign only superadmin
        $superadmin->syncRoles(['superadmin']);

        // Create or update regular admin user
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

        // Remove all roles and assign only admin
        $admin->syncRoles(['admin']);

        $this->command->info('Roles and Permissions created successfully!');
        $this->command->info('');
        $this->command->info('=== SUPERADMIN PERMISSIONS ===');
        $this->command->info('- Full access to Admin Users (create, edit, delete)');
        $this->command->info('- Full access to Customers (view only)');
        $this->command->info('- Full access to all other resources');
        $this->command->info('');
        $this->command->info('=== ADMIN PERMISSIONS ===');
        $this->command->info('- Can ONLY view Customers (read-only)');
        $this->command->info('- Cannot access Admin Users section');
        $this->command->info('- Full access to Products, Orders, Categories');
        $this->command->info('');
        $this->command->info('Login Credentials:');
        $this->command->info('Superadmin: superadmin@bakeryshop.com / SuperAdmin@2025');
        $this->command->info('Admin: admin@bakeryshop.com / Admin@2025');
    }
}