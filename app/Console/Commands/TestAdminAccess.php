<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class TestAdminAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-admin-access {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test admin panel access for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'admin@bakery.com';
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found!");
            return;
        }
        
        $this->info("Testing admin access for: {$user->email}");
        
        // Check if admin role exists
        $adminRole = Role::where('name', 'admin')->first();
        $this->info("Admin role exists: " . ($adminRole ? 'YES' : 'NO'));
        
        // Check if user has admin role
        $hasAdminRole = $user->hasRole('admin');
        $this->info("User has admin role: " . ($hasAdminRole ? 'YES' : 'NO'));
        
        // Check panel access
        try {
            $panel = app('filament')->getPanel('admin');
            $canAccess = $user->canAccessPanel($panel);
            $this->info("Can access admin panel: " . ($canAccess ? 'YES' : 'NO'));
        } catch (\Exception $e) {
            $this->error("Error checking panel access: " . $e->getMessage());
        }
        
        // Show user roles
        $roles = $user->getRoleNames();
        $this->info("User roles: " . $roles->implode(', '));
        
        if (!$hasAdminRole) {
            if ($this->confirm('Would you like to give this user admin access?')) {
                $user->assignRole('admin');
                $this->success("Admin role assigned to {$user->email}");
            }
        }
    }
}
