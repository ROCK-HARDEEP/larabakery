# Multi-Channel Notification Module Structure

## Completed Components

### 1. Database Migrations ✅
- `2025_09_01_105755_add_notification_fields_to_users_table.php` - User notification preferences
- `2025_09_01_105817_create_message_campaigns_table.php` - Campaign management
- `2025_09_01_105838_create_message_deliveries_table.php` - Delivery tracking
- `2025_09_01_105854_create_notification_templates_table.php` - Reusable templates
- `2025_09_01_105909_create_saved_segments_table.php` - User segments

### 2. Models ✅
- `app/Models/MessageCampaign.php` - Campaign model with statistics
- `app/Models/MessageDelivery.php` - Delivery tracking model
- `app/Models/NotificationTemplate.php` - Template management
- `app/Models/SavedSegment.php` - Segment management
- `app/Models/User.php` - Updated with notification preferences

## Components to Create

### 3. Services
- `app/Services/UserSegmentService.php` - User filtering/segmentation
- `app/Services/NotificationTemplateService.php` - Template rendering
- `app/Services/CampaignDispatchService.php` - Campaign orchestration

### 4. Notification Classes
- `app/Notifications/AdminBroadcast.php` - Main notification class
- `app/Notifications/Channels/FCMChannel.php` - Firebase Cloud Messaging
- `app/Notifications/Channels/WhatsAppChannel.php` - WhatsApp via Interakt/Twilio
- `app/Notifications/Channels/SMSChannel.php` - SMS via Twilio

### 5. Jobs
- `app/Jobs/DispatchCampaign.php` - Process campaign sending
- `app/Jobs/ProcessNotificationBatch.php` - Batch processing
- `app/Jobs/SendNotificationToUser.php` - Individual notification

### 6. Filament Resources
- `app/Filament/Resources/MessageCampaignResource.php` - Campaign management UI
- `app/Filament/Resources/MessageDeliveryResource.php` - Delivery tracking UI
- `app/Filament/Resources/NotificationTemplateResource.php` - Template management UI
- `app/Filament/Resources/SavedSegmentResource.php` - Segment management UI

### 7. Configuration
- `config/notifications.php` - Notification configuration
- `.env` updates - Provider credentials

### 8. Commands
- `app/Console/Commands/ProcessScheduledCampaigns.php` - Process scheduled campaigns
- `app/Console/Commands/CleanupOldDeliveries.php` - Cleanup old data

### 9. Events
- `app/Events/CampaignCreated.php`
- `app/Events/CampaignStarted.php`
- `app/Events/CampaignCompleted.php`
- `app/Events/NotificationSent.php`
- `app/Events/NotificationFailed.php`

### 10. API/Webhooks
- `app/Http/Controllers/Api/NotificationWebhookController.php` - Provider webhooks
- `app/Http/Controllers/Api/NotificationTrackingController.php` - Open/click tracking

### 11. Blade Views
- `resources/views/notifications/in-app-dropdown.blade.php` - In-app notification UI
- `resources/views/notifications/preferences.blade.php` - User preferences page
- `resources/views/emails/admin-broadcast.blade.php` - Email template

## Quick Setup Guide

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Add Environment Variables
```env
# Email
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@bakeryshop.com
MAIL_FROM_NAME="Bakery Shop"

# Firebase Cloud Messaging
FCM_PROJECT_ID=your-project-id
FCM_PRIVATE_KEY_ID=your-key-id
FCM_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\n...\n-----END PRIVATE KEY-----\n"
FCM_CLIENT_EMAIL=firebase-adminsdk@project.iam.gserviceaccount.com
FCM_CLIENT_ID=your-client-id

# Interakt WhatsApp
INTERAKT_API_KEY=your-api-key
INTERAKT_BASE_URL=https://api.interakt.ai/v1
INTERAKT_SENDER=your-sender-id

# Twilio (Fallback WhatsApp & SMS)
TWILIO_ACCOUNT_SID=your-account-sid
TWILIO_AUTH_TOKEN=your-auth-token
TWILIO_WHATSAPP_FROM=whatsapp:+14155238886
TWILIO_SMS_FROM=+1234567890

# Notification Settings
NOTIFICATION_RATE_LIMIT_PER_MINUTE=60
NOTIFICATION_BATCH_SIZE=100
NOTIFICATION_RETRY_ATTEMPTS=3
```

### 3. Configure Queue Workers
```bash
# Start queue worker
php artisan queue:work --queue=notifications,default

# Schedule runner (add to cron)
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### 4. Register Scheduled Tasks
In `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('campaigns:process-scheduled')->everyMinute();
    $schedule->command('deliveries:cleanup')->daily();
}
```

## Usage Examples

### Creating a Campaign (PHP)
```php
use App\Models\MessageCampaign;
use App\Jobs\DispatchCampaign;

$campaign = MessageCampaign::create([
    'title' => 'Summer Sale Announcement',
    'subject' => 'Get 20% Off This Summer!',
    'body_template' => 'Hi {{ user.name }}, enjoy 20% off on all items!',
    'channels' => ['email', 'in_app', 'whatsapp'],
    'filters' => [
        'tags' => ['vip', 'active'],
        'created_after' => '2024-01-01',
    ],
    'schedule_at' => now()->addHours(2),
    'status' => 'scheduled',
    'created_by' => auth()->id(),
]);

// Dispatch immediately
DispatchCampaign::dispatch($campaign);

// Or will be auto-dispatched at schedule_at time
```

### User Segmentation Filters
```php
$filters = [
    'role' => 'customer',                    // User role
    'active' => true,                         // Active users only
    'email_verified' => true,                 // Email verified
    'created_between' => ['2024-01-01', '2024-12-31'],
    'tags' => ['vip', 'premium'],            // Has any of these tags
    'has_orders' => true,                     // Has placed orders
    'order_count_min' => 5,                   // Minimum order count
    'total_spent_min' => 1000,                // Minimum total spent
];
```

## Testing

### Feature Tests
```bash
php artisan test --filter=NotificationTest
```

### Manual Testing
1. Create test users with different channel preferences
2. Create a test campaign in Filament admin
3. Use "Test Send" to verify single user delivery
4. Schedule campaign and verify batch processing
5. Check delivery reports and statistics

## Security Considerations

1. **Rate Limiting**: Configured per channel to prevent abuse
2. **PII Protection**: Provider IDs logged, not full messages
3. **Opt-out Compliance**: Respects user channel preferences
4. **Webhook Validation**: Validates provider webhook signatures
5. **Queue Encryption**: Sensitive data encrypted in queue jobs

## Monitoring

1. **Failed Jobs**: Check `failed_jobs` table
2. **Delivery Reports**: View in Filament admin
3. **Logs**: Check `storage/logs/notifications.log`
4. **Metrics**: Export delivery statistics as CSV

## Troubleshooting

### Common Issues

1. **Notifications not sending**
   - Check queue workers are running
   - Verify provider credentials in .env
   - Check user has channel enabled
   - Review failed_jobs table

2. **WhatsApp/SMS failing**
   - Verify phone number format (+countrycode)
   - Check provider account balance
   - Verify webhook URLs are accessible

3. **Push notifications not working**
   - Ensure FCM credentials are correct
   - Verify user has fcm_token
   - Check FCM project settings

## API Endpoints

### Webhook Endpoints
- `POST /api/webhooks/twilio` - Twilio status callbacks
- `POST /api/webhooks/interakt` - Interakt delivery reports
- `POST /api/webhooks/fcm` - FCM delivery confirmations

### Tracking Endpoints
- `GET /api/notifications/track/open/{delivery_id}` - Track email opens
- `GET /api/notifications/track/click/{delivery_id}/{link_id}` - Track link clicks

## Performance Optimization

1. **Chunked Processing**: Users processed in batches of 100
2. **Queue Priorities**: Critical notifications on high priority queue
3. **Database Indexes**: Optimized for common queries
4. **Caching**: Segment counts cached for 1 hour
5. **Async Processing**: All sending done via queues

## Future Enhancements

1. **A/B Testing**: Test different messages/subjects
2. **Advanced Analytics**: Conversion tracking, ROI calculation
3. **Template Builder**: Drag-and-drop email builder
4. **Auto-Campaigns**: Trigger based on user actions
5. **Multi-language**: Localized notifications
6. **Rich Media**: Support for images, videos in notifications