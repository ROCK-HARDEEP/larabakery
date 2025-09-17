<?php

namespace App\Listeners;

use App\Services\AuditLogService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;

class AuthActivityListener
{
    /**
     * Handle user login events.
     */
    public function handleLogin(Login $event)
    {
        // Only log admin/superadmin logins
        if ($event->user && $event->user->hasRole(['admin', 'superadmin'])) {
            // Update last login information
            $event->user->update([
                'last_login_at' => now(),
                'login_ip' => request()->ip()
            ]);

            AuditLogService::logLogin($event->user);
        }
    }

    /**
     * Handle user logout events.
     */
    public function handleLogout(Logout $event)
    {
        // Only log admin/superadmin logouts
        if ($event->user && $event->user->hasRole(['admin', 'superadmin'])) {
            AuditLogService::logLogout($event->user);
        }
    }

    /**
     * Handle failed login attempts.
     */
    public function handleFailed(Failed $event)
    {
        $credentials = $event->credentials;
        $email = $credentials['email'] ?? 'Unknown';

        AuditLogService::logFailedLogin($email);
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe($events)
    {
        $events->listen(
            Login::class,
            [AuthActivityListener::class, 'handleLogin']
        );

        $events->listen(
            Logout::class,
            [AuthActivityListener::class, 'handleLogout']
        );

        $events->listen(
            Failed::class,
            [AuthActivityListener::class, 'handleFailed']
        );
    }
}