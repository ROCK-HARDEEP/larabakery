<?php

namespace App\Console\Commands;

use App\Models\MessageDelivery;
use Illuminate\Console\Command;

class CleanupOldDeliveries extends Command
{
    protected $signature = 'deliveries:cleanup {--days=90 : Number of days to keep delivery records} {--dry-run : Show what would be deleted without actually deleting}';
    protected $description = 'Clean up old delivery records to free up database space';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoffDate = now()->subDays($days);

        $this->info("Cleaning up delivery records older than {$days} days...");

        $query = MessageDelivery::where('created_at', '<', $cutoffDate);

        if ($this->option('dry-run')) {
            $count = $query->count();
            $this->warn("DRY RUN MODE - Would delete {$count} delivery records");
            $this->info("Records older than: {$cutoffDate->format('Y-m-d H:i:s')}");
            return self::SUCCESS;
        }

        try {
            $count = $query->count();
            $deleted = $query->delete();

            $this->info("Successfully deleted {$deleted} delivery records");
            $this->info("Records older than: {$cutoffDate->format('Y-m-d H:i:s')}");

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Failed to cleanup delivery records: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
