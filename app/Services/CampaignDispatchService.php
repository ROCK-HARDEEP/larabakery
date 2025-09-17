<?php

namespace App\Services;

use App\Jobs\DispatchCampaign;
use App\Models\MessageCampaign;
use Illuminate\Support\Facades\Log;

class CampaignDispatchService
{
    public function dispatchCampaign(MessageCampaign $campaign): void
    {
        try {
            Log::info("Dispatching campaign", ['campaign_id' => $campaign->id]);

            if (!$campaign->canBeSent()) {
                Log::warning("Campaign cannot be sent", [
                    'campaign_id' => $campaign->id,
                    'status' => $campaign->status,
                    'channels' => $campaign->channels
                ]);
                return;
            }

            // Dispatch the campaign job
            DispatchCampaign::dispatch($campaign);

            Log::info("Campaign dispatched successfully", ['campaign_id' => $campaign->id]);

        } catch (\Exception $e) {
            Log::error("Failed to dispatch campaign", [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage()
            ]);

            $campaign->update([
                'status' => 'failed',
                'completed_at' => now()
            ]);

            throw $e;
        }
    }

    public function dispatchScheduledCampaigns(): void
    {
        $scheduledCampaigns = MessageCampaign::due()->get();

        Log::info("Processing scheduled campaigns", ['count' => $scheduledCampaigns->count()]);

        foreach ($scheduledCampaigns as $campaign) {
            try {
                $this->dispatchCampaign($campaign);
            } catch (\Exception $e) {
                Log::error("Failed to dispatch scheduled campaign", [
                    'campaign_id' => $campaign->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    public function cancelCampaign(MessageCampaign $campaign): bool
    {
        if (!$campaign->canBeCancelled()) {
            Log::warning("Campaign cannot be cancelled", [
                'campaign_id' => $campaign->id,
                'status' => $campaign->status
            ]);
            return false;
        }

        try {
            $campaign->update([
                'status' => 'cancelled',
                'completed_at' => now()
            ]);

            Log::info("Campaign cancelled", ['campaign_id' => $campaign->id]);
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to cancel campaign", [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function getCampaignStats(MessageCampaign $campaign): array
    {
        return [
            'total_recipients' => $campaign->total_recipients,
            'sent_count' => $campaign->sent_count,
            'failed_count' => $campaign->failed_count,
            'opened_count' => $campaign->opened_count,
            'clicked_count' => $campaign->clicked_count,
            'success_rate' => $campaign->success_rate,
            'open_rate' => $campaign->open_rate,
            'click_rate' => $campaign->click_rate,
            'status' => $campaign->status,
            'started_at' => $campaign->started_at,
            'completed_at' => $campaign->completed_at,
        ];
    }

    public function getSystemStats(): array
    {
        $totalCampaigns = MessageCampaign::count();
        $activeCampaigns = MessageCampaign::active()->count();
        $scheduledCampaigns = MessageCampaign::where('status', 'scheduled')->count();
        $failedCampaigns = MessageCampaign::where('status', 'failed')->count();

        return [
            'total_campaigns' => $totalCampaigns,
            'active_campaigns' => $activeCampaigns,
            'scheduled_campaigns' => $scheduledCampaigns,
            'failed_campaigns' => $failedCampaigns,
            'success_rate' => $totalCampaigns > 0 ? 
                round((($totalCampaigns - $failedCampaigns) / $totalCampaigns) * 100, 2) : 0,
        ];
    }
}
