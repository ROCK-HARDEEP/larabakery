<?php

namespace App\Console\Commands;

use App\Services\CampaignDispatchService;
use Illuminate\Console\Command;

class ProcessScheduledCampaigns extends Command
{
    protected $signature = 'campaigns:process-scheduled {--dry-run : Show what would be processed without actually sending}';
    protected $description = 'Process scheduled campaigns that are due to be sent';

    public function handle(CampaignDispatchService $dispatchService): int
    {
        $this->info('Processing scheduled campaigns...');

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE - No campaigns will actually be sent');
        }

        try {
            $dispatchService->dispatchScheduledCampaigns();
            
            $this->info('Scheduled campaigns processed successfully');
            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Failed to process scheduled campaigns: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
