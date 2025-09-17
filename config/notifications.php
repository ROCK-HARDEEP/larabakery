<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Channel Settings
    |--------------------------------------------------------------------------
    */
    'default_channels' => ['in_app', 'email'],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limits' => [
        'per_minute' => env('NOTIFICATION_RATE_LIMIT_PER_MINUTE', 60),
        'email' => [
            'per_minute' => 30,
            'per_hour' => 1000,
        ],
        'sms' => [
            'per_minute' => 10,
            'per_hour' => 100,
        ],
        'whatsapp' => [
            'per_minute' => 20,
            'per_hour' => 500,
        ],
        'push' => [
            'per_minute' => 100,
            'per_hour' => 5000,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Batch Processing
    |--------------------------------------------------------------------------
    */
    'batch_size' => env('NOTIFICATION_BATCH_SIZE', 100),
    'queue' => env('NOTIFICATION_QUEUE', 'notifications'),
    'retry_attempts' => env('NOTIFICATION_RETRY_ATTEMPTS', 3),
    'retry_delay' => 60, // seconds

    /*
    |--------------------------------------------------------------------------
    | Channel Configurations
    |--------------------------------------------------------------------------
    */
    'channels' => [
        'email' => [
            'enabled' => env('MAIL_MAILER', null) !== null,
            'from_address' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
            'from_name' => env('MAIL_FROM_NAME', 'Bakery Shop'),
            'track_opens' => true,
            'track_clicks' => true,
        ],

        'whatsapp' => [
            'default_provider' => env('WHATSAPP_PROVIDER', 'interakt'), // 'interakt' or 'twilio'
            
            'interakt' => [
                'enabled' => env('INTERAKT_API_KEY', null) !== null,
                'api_key' => env('INTERAKT_API_KEY'),
                'base_url' => env('INTERAKT_BASE_URL', 'https://api.interakt.ai/v1'),
                'sender' => env('INTERAKT_SENDER'),
            ],
            
            'twilio' => [
                'enabled' => env('TWILIO_ACCOUNT_SID', null) !== null,
                'account_sid' => env('TWILIO_ACCOUNT_SID'),
                'auth_token' => env('TWILIO_AUTH_TOKEN'),
                'from' => env('TWILIO_WHATSAPP_FROM', 'whatsapp:+14155238886'),
            ],
        ],

        'sms' => [
            'provider' => env('SMS_PROVIDER', 'test'),
            
            'test' => [
                'enabled' => true,
            ],
            
            'twilio' => [
                'enabled' => env('TWILIO_ACCOUNT_SID', null) !== null,
                'account_sid' => env('TWILIO_ACCOUNT_SID'),
                'auth_token' => env('TWILIO_AUTH_TOKEN'),
                'from' => env('TWILIO_SMS_FROM'),
            ],
        ],

        'push' => [
            'provider' => env('PUSH_PROVIDER', 'fcm'),
            
            'fcm' => [
                'enabled' => env('FCM_PROJECT_ID', null) !== null,
                'project_id' => env('FCM_PROJECT_ID'),
                'private_key_id' => env('FCM_PRIVATE_KEY_ID'),
                'private_key' => env('FCM_PRIVATE_KEY'),
                'client_email' => env('FCM_CLIENT_EMAIL'),
                'client_id' => env('FCM_CLIENT_ID'),
                'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
                'token_uri' => 'https://oauth2.googleapis.com/token',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Template Variables
    |--------------------------------------------------------------------------
    */
    'template_variables' => [
        'user' => [
            'name' => 'User Name',
            'email' => 'User Email',
            'phone' => 'User Phone',
            'first_name' => 'User First Name',
        ],
        'app' => [
            'name' => 'Application Name',
            'url' => 'Application URL',
            'logo' => 'Application Logo URL',
        ],
        'date' => [
            'today' => 'Today\'s Date',
            'time' => 'Current Time',
            'year' => 'Current Year',
        ],
        'order' => [
            'id' => 'Order ID',
            'total' => 'Order Total',
            'status' => 'Order Status',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    */
    'webhooks' => [
        'enabled' => true,
        'signature_verification' => true,
        'endpoints' => [
            'twilio' => '/api/webhooks/twilio',
            'interakt' => '/api/webhooks/interakt',
            'fcm' => '/api/webhooks/fcm',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tracking Configuration
    |--------------------------------------------------------------------------
    */
    'tracking' => [
        'enabled' => true,
        'pixel_route' => '/api/notifications/track/open',
        'click_route' => '/api/notifications/track/click',
        'retention_days' => 90, // Keep tracking data for 90 days
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Templates
    |--------------------------------------------------------------------------
    */
    'default_templates' => [
        'welcome' => [
            'title' => 'Welcome to Bakery Shop',
            'subject' => 'Welcome to {{ app.name }}!',
            'body' => 'Hi {{ user.name }}, welcome to {{ app.name }}! We\'re excited to have you on board.',
            'channels' => ['email', 'in_app'],
        ],
        'order_confirmation' => [
            'title' => 'Order Confirmation',
            'subject' => 'Order #{{ order.id }} Confirmed',
            'body' => 'Hi {{ user.name }}, your order #{{ order.id }} for ₹{{ order.total }} has been confirmed.',
            'channels' => ['email', 'in_app', 'whatsapp'],
        ],
        'password_reset' => [
            'title' => 'Password Reset',
            'subject' => 'Reset Your Password',
            'body' => 'Hi {{ user.name }}, click the link below to reset your password.',
            'channels' => ['email'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cleanup Configuration
    |--------------------------------------------------------------------------
    */
    'cleanup' => [
        'keep_deliveries_days' => 180, // Keep delivery records for 6 months
        'keep_failed_jobs_days' => 30,
        'auto_cleanup_enabled' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    */
    'features' => [
        'ab_testing' => false,
        'analytics' => true,
        'templates' => true,
        'segments' => true,
        'scheduling' => true,
        'bulk_actions' => true,
    ],
];