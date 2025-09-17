<?php

namespace App\Jobs;

use App\Models\MessageCampaign;
use App\Models\MessageDelivery;
use App\Models\User;
use App\Notifications\AdminBroadcast;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendNotificationToUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 60; // 1 minute
    public $tries = 3;

    public function __construct(
        protected MessageCampaign $campaign,
        protected User $user,
        protected array $channels
    ) {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        try {
            Log::info("Sending notification to user", [
                'campaign_id' => $this->campaign->id,
                'user_id' => $this->user->id,
                'channels' => $this->channels
            ]);

            // Create delivery record
            $delivery = MessageDelivery::create([
                'campaign_id' => $this->campaign->id,
                'user_id' => $this->user->id,
                'channel' => $this->channels[0], // Primary channel
                'status' => 'pending',
                'metadata' => [
                    'all_channels' => $this->channels,
                    'campaign_title' => $this->campaign->title,
                    'campaign_subject' => $this->campaign->subject,
                ]
            ]);

            // Send notification through all available channels
            foreach ($this->channels as $channel) {
                try {
                    $this->sendToChannel($channel, $delivery);
                } catch (\Exception $e) {
                    Log::error("Failed to send to channel", [
                        'campaign_id' => $this->campaign->id,
                        'user_id' => $this->user->id,
                        'channel' => $channel,
                        'error' => $e->getMessage()
                    ]);

                    $delivery->markAsFailed($e->getMessage());
                    $this->campaign->increment('failed_count');
                }
            }

            // Mark delivery as sent if successful
            if ($delivery->status === 'pending') {
                $delivery->markAsSent();
                $this->campaign->increment('sent_count');
            }

            Log::info("Notification sent to user", [
                'campaign_id' => $this->campaign->id,
                'user_id' => $this->user->id,
                'delivery_id' => $delivery->id
            ]);

        } catch (\Exception $e) {
            Log::error("Send notification job failed", [
                'campaign_id' => $this->campaign->id,
                'user_id' => $this->user->id,
                'error' => $e->getMessage()
            ]);

            $this->campaign->increment('failed_count');
            throw $e;
        }
    }

    protected function sendToChannel(string $channel, MessageDelivery $delivery): void
    {
        $notification = new AdminBroadcast(
            $this->campaign,
            $channel,
            $this->user
        );

        switch ($channel) {
            case 'email':
                if ($this->user->email) {
                    Notification::route('mail', $this->user->email)
                        ->notify($notification);
                }
                break;

            case 'in_app':
                $this->user->notify($notification);
                break;

            case 'whatsapp':
                if ($this->user->wa_number) {
                    // WhatsApp implementation would go here
                    // For now, just log it
                    Log::info("WhatsApp notification queued", [
                        'user_id' => $this->user->id,
                        'number' => $this->user->wa_number
                    ]);
                }
                break;

            case 'sms':
                if ($this->user->sms_number) {
                    // SMS implementation would go here
                    // For now, just log it
                    Log::info("SMS notification queued", [
                        'user_id' => $this->user->id,
                        'number' => $this->user->sms_number
                    ]);
                }
                break;

            case 'push':
                if ($this->user->fcm_token) {
                    // Push notification implementation would go here
                    // For now, just log it
                    Log::info("Push notification queued", [
                        'user_id' => $this->user->id,
                        'fcm_token' => $this->user->fcm_token
                    ]);
                }
                break;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Send notification job failed", [
            'campaign_id' => $this->campaign->id,
            'user_id' => $this->user->id,
            'error' => $exception->getMessage()
        ]);

        $this->campaign->increment('failed_count');
    }
}
