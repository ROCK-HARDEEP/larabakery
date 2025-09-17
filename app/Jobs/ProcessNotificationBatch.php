<?php

namespace App\Jobs;

use App\Models\MessageCampaign;
use App\Models\User;
use App\Notifications\AdminBroadcast;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessNotificationBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120; // 2 minutes
    public $tries = 2;

    public function __construct(
        protected MessageCampaign $campaign,
        protected $userBatch
    ) {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        try {
            Log::info("Processing notification batch", [
                'campaign_id' => $this->campaign->id,
                'batch_size' => $this->userBatch->count()
            ]);

            foreach ($this->userBatch as $user) {
                // Check if user can receive any of the campaign channels
                $availableChannels = array_intersect(
                    $this->campaign->channels,
                    $user->getNotificationChannels()
                );

                if (empty($availableChannels)) {
                    Log::info("User has no available channels", [
                        'user_id' => $user->id,
                        'campaign_channels' => $this->campaign->channels,
                        'user_channels' => $user->getNotificationChannels()
                    ]);
                    continue;
                }

                // Send notification to user
                SendNotificationToUser::dispatch($this->campaign, $user, $availableChannels);
            }

            Log::info("Notification batch processed", [
                'campaign_id' => $this->campaign->id,
                'batch_size' => $this->userBatch->count()
            ]);

        } catch (\Exception $e) {
            Log::error("Notification batch processing failed", [
                'campaign_id' => $this->campaign->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Notification batch job failed", [
            'campaign_id' => $this->campaign->id,
            'error' => $exception->getMessage()
        ]);
    }
}
