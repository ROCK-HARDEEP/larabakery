<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FixedRolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Clear existing roles and permissions
        Permission::query()->delete();
        Role::query()->delete();

        // Create all permissions
        $permissions = [
            // Customer permissions - Read only for both admin and superadmin
            'view_customers',

            // Admin management - Only for superadmin
            'view_admin_users',
            'create_admin_users',
            'edit_admin_users',
            'delete_admin_users',

            // Product Management - Full access for both admin and superadmin
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',

            // Order Management - Full access for both admin and superadmin
            'view_orders',
            'create_orders',
            'edit_orders',
            'delete_orders',

            // Category Management - Full access for both admin and superadmin
            'view_categories',
            'create_categories',
            'edit_categories',
            'delete_categories',

            // Other resources - Full access for both admin and superadmin
            'manage_settings',
            'manage_reports',
            'manage_content',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create SUPERADMIN role with ALL permissions
        $superadminRole = Role::create(['name' => 'superadmin', 'guard_name' => 'web']);
        $superadminRole->givePermissionTo(Permission::all());

        // Create ADMIN role with LIMITED permissions
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);

        // Admin permissions - Can view customers only, and manage other resources
        $adminPermissions = [
            'view_customers',      // ONLY view customers, no edit/delete
            'view_products',       // Full product management
            'create_products',
            'edit_products',
            'delete_products',
            'view_orders',         // Full order management
            'create_orders',
            'edit_orders',
            'delete_orders',
            'view_categories',     // Full category management
            'create_categories',
            'edit_categories',
            'delete_categories',
            'manage_settings',     // Can manage settings
            'manage_reports',      // Can view reports
            'manage_content',      // Can manage content
        ];

        $adminRole->givePermissionTo($adminPermissions);

        // Create or update superadmin user
        $superadmin = User::updateOrCreate(
            ['email' => 'superadmin@bakeryshop.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('SuperAdmin@2025'),
                'phone' => '+91 9876543210',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]
        );
        $superadmin->syncRoles(['superadmin']);

        // Create or update admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@bakeryshop.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('Admin@2025'),
                'phone' => '+91 9876543211',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['admin']);

        $this->command->info('');
        $this->command->info('=====================================');
        $this->command->info('ROLES AND PERMISSIONS CONFIGURED');
        $this->command->info('=====================================');
        $this->command->info('');

        $this->command->info('SUPERADMIN PERMISSIONS:');
        $this->command->info('------------------------');
        $this->command->info('✓ Admin Users: Full CRUD (Create, Read, Update, Delete)');
        $this->command->info('✓ Customers: View Only (Read-only)');
        $this->command->info('✓ Products: Full CRUD');
        $this->command->info('✓ Orders: Full CRUD');
        $this->command->info('✓ Categories: Full CRUD');
        $this->command->info('✓ All other resources: Full access');
        $this->command->info('');

        $this->command->info('ADMIN PERMISSIONS:');
        $this->command->info('------------------------');
        $this->command->info('✗ Admin Users: NO ACCESS (Hidden)');
        $this->command->info('✓ Customers: View Only (Read-only)');
        $this->command->info('✓ Products: Full CRUD');
        $this->command->info('✓ Orders: Full CRUD');
        $this->command->info('✓ Categories: Full CRUD');
        $this->command->info('✓ Other resources: Full access');
        $this->command->info('');

        $this->command->info('LOGIN CREDENTIALS:');
        $this->command->info('------------------------');
        $this->command->info('Superadmin: superadmin@bakeryshop.com / SuperAdmin@2025');
        $this->command->info('Admin: admin@bakeryshop.com / Admin@2025');
        $this->command->info('=====================================');
    }
}