<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ModulePermission;

class ModulePermissionSeeder extends Seeder
{
    public function run()
    {
        $modules = [
            // User Management
            [
                'module_name' => 'User Management',
                'module_slug' => 'user-management',
                'resource_class' => 'App\\Filament\\Resources\\UserResource',
                'group' => 'User Management',
                'description' => 'Manage system users',
                'sort_order' => 1
            ],
            [
                'module_name' => 'Admin Management',
                'module_slug' => 'admin-management',
                'resource_class' => 'App\\Filament\\Resources\\AdminResource',
                'group' => 'User Management',
                'description' => 'Manage admin users',
                'sort_order' => 2
            ],
            [
                'module_name' => 'Customer Management',
                'module_slug' => 'customer-management',
                'resource_class' => 'App\\Filament\\Resources\\CustomerResource',
                'group' => 'User Management',
                'description' => 'Manage customer accounts',
                'sort_order' => 3
            ],
            [
                'module_name' => 'Role Manager',
                'module_slug' => 'role-manager',
                'resource_class' => 'App\\Filament\\Resources\\RoleResource',
                'group' => 'User Management',
                'description' => 'Manage roles and permissions',
                'sort_order' => 4
            ],

            // E-commerce Management
            [
                'module_name' => 'Product Management',
                'module_slug' => 'product-management',
                'resource_class' => 'App\\Filament\\Resources\\ProductResource',
                'group' => 'E-commerce',
                'description' => 'Manage products',
                'sort_order' => 1
            ],
            [
                'module_name' => 'Category Management',
                'module_slug' => 'category-management',
                'resource_class' => 'App\\Filament\\Resources\\CategoryResource',
                'group' => 'E-commerce',
                'description' => 'Manage product categories',
                'sort_order' => 2
            ],
            [
                'module_name' => 'Order Management',
                'module_slug' => 'order-management',
                'resource_class' => 'App\\Filament\\Resources\\OrderResource',
                'group' => 'E-commerce',
                'description' => 'Manage customer orders',
                'sort_order' => 3
            ],
            [
                'module_name' => 'Coupon Management',
                'module_slug' => 'coupon-management',
                'resource_class' => 'App\\Filament\\Resources\\CouponResource',
                'group' => 'E-commerce',
                'description' => 'Manage discount coupons',
                'sort_order' => 4
            ],
            [
                'module_name' => 'Payment Management',
                'module_slug' => 'payment-management',
                'resource_class' => 'App\\Filament\\Resources\\PaymentResource',
                'group' => 'E-commerce',
                'description' => 'Manage payments',
                'sort_order' => 5
            ],
            [
                'module_name' => 'Shipment Management',
                'module_slug' => 'shipment-management',
                'resource_class' => 'App\\Filament\\Resources\\ShipmentResource',
                'group' => 'E-commerce',
                'description' => 'Manage shipments',
                'sort_order' => 6
            ],

            // Content Management
            [
                'module_name' => 'Hero Slides',
                'module_slug' => 'hero-slides',
                'resource_class' => 'App\\Filament\\Resources\\HeroSlideResource',
                'group' => 'Content Management',
                'description' => 'Manage hero slider content',
                'sort_order' => 1
            ],

            // System Management
            [
                'module_name' => 'Audit Log',
                'module_slug' => 'audit-log',
                'resource_class' => 'App\\Filament\\Resources\\AuditLogResource',
                'group' => 'System',
                'description' => 'View system audit logs',
                'sort_order' => 1
            ],
            [
                'module_name' => 'Admin Settings',
                'module_slug' => 'admin-settings',
                'page_class' => 'App\\Filament\\Pages\\AdminSettings',
                'group' => 'System',
                'description' => 'System configuration',
                'sort_order' => 2
            ],
        ];

        foreach ($modules as $module) {
            ModulePermission::updateOrCreate(
                ['module_slug' => $module['module_slug']],
                array_merge($module, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}