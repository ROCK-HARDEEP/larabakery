<?php

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user if not exists
        $admin = User::firstOrCreate(
            ['email' => 'admin@bakeryshop.com'],
            [
                'name' => 'System Administrator',
                'password' => bcrypt('admin123'),
                'email_verified_at' => now(),
                'notify_in_app' => true,
                'notify_email' => true,
                'notify_whatsapp' => false,
                'notify_sms' => false,
                'notify_push' => false,
            ]
        );

        // Assign admin role if exists
        if (class_exists('Spatie\Permission\Models\Role')) {
            $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
            $admin->assignRole($adminRole);
        }

        // Create demo users with different preferences
        $demoUsers = [
            [
                'name' => 'John Customer',
                'email' => 'john@example.com',
                'phone' => '+911234567890',
                'notify_in_app' => true,
                'notify_email' => true,
                'notify_whatsapp' => true,
                'wa_number' => '+911234567890',
                'tags' => ['vip', 'active'],
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'phone' => '+919876543210',
                'notify_in_app' => true,
                'notify_email' => false,
                'notify_sms' => true,
                'sms_number' => '+919876543210',
                'tags' => ['regular'],
            ],
            [
                'name' => 'Bob Wilson',
                'email' => 'bob@example.com',
                'notify_in_app' => true,
                'notify_email' => true,
                'notify_push' => true,
                'fcm_token' => 'demo_fcm_token_123',
                'tags' => ['premium', 'frequent'],
            ],
        ];

        foreach ($demoUsers as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, [
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ])
            );
        }

        // Create default notification templates
        $templates = [
            [
                'title' => 'Welcome Message',
                'slug' => 'welcome',
                'default_subject' => 'Welcome to {{ app.name }}!',
                'default_body_template' => 'Hi {{ user.name }},

Welcome to {{ app.name }}! We\'re excited to have you as part of our bakery family.

Explore our delicious range of fresh baked goods, cakes, and pastries. We use only the finest ingredients to create treats that will make your day special.

Visit us today: {{ app.url }}

Best regards,
The Bakery Team',
                'default_channels' => ['email', 'in_app'],
                'variables' => [
                    'user.name' => 'Customer Name',
                    'app.name' => 'Bakery Shop Name',
                    'app.url' => 'Website URL',
                ],
                'created_by' => $admin->id,
            ],

            [
                'title' => 'Order Confirmation',
                'slug' => 'order_confirmation',
                'default_subject' => 'Order #{{ order.id }} Confirmed - {{ app.name }}',
                'default_body_template' => 'Hi {{ user.name }},

Great news! Your order has been confirmed.

**Order Details:**
- Order ID: #{{ order.id }}
- Total: ₹{{ order.total }}
- Status: {{ order.status }}

We\'ll start preparing your delicious treats right away. You\'ll receive another notification when your order is ready for pickup or delivery.

Thank you for choosing {{ app.name }}!

Order tracking: {{ app.url }}/orders/{{ order.id }}',
                'default_channels' => ['email', 'in_app', 'whatsapp'],
                'variables' => [
                    'user.name' => 'Customer Name',
                    'order.id' => 'Order Number',
                    'order.total' => 'Order Total Amount',
                    'order.status' => 'Order Status',
                    'app.name' => 'Bakery Shop Name',
                    'app.url' => 'Website URL',
                ],
                'created_by' => $admin->id,
            ],

            [
                'title' => 'Order Ready',
                'slug' => 'order_ready',
                'default_subject' => 'Your Order is Ready! - {{ app.name }}',
                'default_body_template' => 'Hi {{ user.name }},

Your order #{{ order.id }} is now ready!

**Pickup Details:**
- Order Total: ₹{{ order.total }}
- Ready Time: {{ date.time }}
- Pickup Location: {{ app.name }} Store

Please bring your order confirmation when collecting your items.

We hope you enjoy your fresh baked goods!',
                'default_channels' => ['email', 'in_app', 'whatsapp', 'sms'],
                'variables' => [
                    'user.name' => 'Customer Name',
                    'order.id' => 'Order Number',
                    'order.total' => 'Order Total Amount',
                    'date.time' => 'Current Time',
                    'app.name' => 'Bakery Shop Name',
                ],
                'created_by' => $admin->id,
            ],

            [
                'title' => 'Special Offer',
                'slug' => 'special_offer',
                'default_subject' => 'Special Offer Just for You! - {{ app.name }}',
                'default_body_template' => 'Hi {{ user.name }},

We have a special offer just for you!

**Limited Time Offer:**
Get 20% off on all cakes and pastries this weekend.

*Offer valid until {{ date.today }}*

Don\'t miss out on this sweet deal!

Use code: SWEET20 at checkout
Shop now: {{ app.url }}

Happy baking!
{{ app.name }}',
                'default_channels' => ['email', 'in_app'],
                'variables' => [
                    'user.name' => 'Customer Name',
                    'date.today' => 'Current Date',
                    'app.name' => 'Bakery Shop Name',
                    'app.url' => 'Website URL',
                ],
                'created_by' => $admin->id,
            ],

            [
                'title' => 'Password Reset',
                'slug' => 'password_reset',
                'default_subject' => 'Reset Your Password - {{ app.name }}',
                'default_body_template' => 'Hi {{ user.name }},

You requested to reset your password for your {{ app.name }} account.

Click the link below to create a new password:
{{ reset_url }}

If you didn\'t request this password reset, please ignore this email.

This link will expire in 60 minutes for security reasons.

Best regards,
{{ app.name }} Team',
                'default_channels' => ['email'],
                'variables' => [
                    'user.name' => 'Customer Name',
                    'reset_url' => 'Password Reset URL',
                    'app.name' => 'Bakery Shop Name',
                ],
                'created_by' => $admin->id,
            ],
        ];

        foreach ($templates as $templateData) {
            NotificationTemplate::firstOrCreate(
                ['slug' => $templateData['slug']],
                $templateData
            );
        }

        $this->command->info('✅ Notification system seeded successfully!');
        $this->command->line('');
        $this->command->info('Demo accounts created:');
        $this->command->line('• Admin: admin@bakeryshop.com (password: admin123)');
        $this->command->line('• Customer 1: john@example.com (password: password)');
        $this->command->line('• Customer 2: jane@example.com (password: password)');
        $this->command->line('• Customer 3: bob@example.com (password: password)');
        $this->command->line('');
        $this->command->info('Notification templates created:');
        $this->command->line('• Welcome Message');
        $this->command->line('• Order Confirmation');
        $this->command->line('• Order Ready');
        $this->command->line('• Special Offer');
        $this->command->line('• Password Reset');
    }
}