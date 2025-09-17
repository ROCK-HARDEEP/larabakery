<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin role if it doesn't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Create admin user if it doesn't exist
        $admin = User::firstOrCreate(
            ['email' => 'admin@bakery.com'],
            [
                'name' => 'Admin',
                'email' => 'admin@bakery.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Assign admin role to the user
        $admin->assignRole($adminRole);

        // Also assign admin role to the user we created manually
        $manualAdmin = User::where('email', 'hardeepb2003@gmail.com')->first();
        if ($manualAdmin) {
            $manualAdmin->assignRole($adminRole);
            $this->command->info('Admin role assigned to hardeepb2003@gmail.com');
        }

        $this->command->info('Admin user ready: admin@bakery.com / password');
        $this->command->info('Or use: hardeepb2003@gmail.com with your password');
    }
}
