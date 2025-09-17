<?php

namespace App\Jobs;

use App\Models\MessageCampaign;
use App\Models\User;
use App\Services\UserSegmentService;
use App\Services\CampaignDispatchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DispatchCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;

    public function __construct(
        protected MessageCampaign $campaign
    ) {
        $this->onQueue('notifications');
    }

    public function handle(UserSegmentService $segmentService, CampaignDispatchService $dispatchService): void
    {
        try {
            Log::info("Starting campaign dispatch", ['campaign_id' => $this->campaign->id]);

            // Mark campaign as sending
            $this->campaign->markAsSending();

            // Get target users based on filters
            $users = $segmentService->getUsersForCampaign($this->campaign);
            
            // Update total recipients count
            $this->campaign->update(['total_recipients' => $users->count()]);

            if ($users->isEmpty()) {
                Log::warning("No users found for campaign", ['campaign_id' => $this->campaign->id]);
                $this->campaign->markAsCompleted();
                return;
            }

            // Process users in batches
            $users->chunk(100)->each(function ($userBatch) use ($dispatchService) {
                ProcessNotificationBatch::dispatch($this->campaign, $userBatch);
            });

            Log::info("Campaign dispatch completed", [
                'campaign_id' => $this->campaign->id,
                'total_recipients' => $users->count()
            ]);

        } catch (\Exception $e) {
            Log::error("Campaign dispatch failed", [
                'campaign_id' => $this->campaign->id,
                'error' => $e->getMessage()
            ]);

            $this->campaign->update([
                'status' => 'failed',
                'completed_at' => now()
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Campaign dispatch job failed", [
            'campaign_id' => $this->campaign->id,
            'error' => $exception->getMessage()
        ]);

        $this->campaign->update([
            'status' => 'failed',
            'completed_at' => now()
        ]);
    }
}
