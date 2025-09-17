<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ApplyImageOptimizationToResources extends Command
{
    protected $signature = 'images:apply-optimization {--dry-run : Preview changes without executing}';
    protected $description = 'Add optimizeToWebP() to all FileUpload fields in Filament resources';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        $resourcesPath = app_path('Filament/Resources');
        $pagesPath = app_path('Filament/Pages');
        
        // Process Resources
        $this->info('Processing Filament Resources...');
        $resourceFiles = File::glob($resourcesPath . '/*.php');
        $this->processFiles($resourceFiles, $isDryRun);
        
        // Process Pages
        $this->info('Processing Filament Pages...');
        $pageFiles = File::glob($pagesPath . '/*.php');
        $this->processFiles($pageFiles, $isDryRun);
        
        if ($isDryRun) {
            $this->warn('This was a dry run. Run without --dry-run to apply changes.');
        } else {
            $this->info('Image optimization has been applied to all FileUpload fields!');
            $this->info('Remember to clear cache: php artisan optimize:clear');
        }
        
        return 0;
    }

    protected function processFiles(array $files, bool $isDryRun)
    {
        foreach ($files as $file) {
            $content = File::get($file);
            $originalContent = $content;
            $modified = false;
            
            // Pattern to find FileUpload::make that doesn't already have optimizeToWebP
            $pattern = '/(Forms\\\\Components\\\\FileUpload::make\([^)]+\)(?:\s*->(?:image|disk|directory|visibility|imagePreviewHeight|maxSize|acceptedFileTypes|multiple|label|required|helperText|columnSpan|columnSpanFull)\([^)]*\))*)/';
            
            $content = preg_replace_callback($pattern, function ($matches) use (&$modified) {
                $fileUpload = $matches[0];
                
                // Skip if already has optimizeToWebP
                if (strpos($fileUpload, 'optimizeToWebP') !== false) {
                    return $fileUpload;
                }
                
                // Skip if it's not an image upload (check for ->image() or image-related settings)
                if (strpos($fileUpload, '->image()') === false && 
                    strpos($fileUpload, '->acceptedFileTypes') === false &&
                    strpos($fileUpload, 'featured_image') === false &&
                    strpos($fileUpload, 'customer_image') === false &&
                    strpos($fileUpload, 'avatar') === false &&
                    strpos($fileUpload, 'logo') === false &&
                    strpos($fileUpload, 'favicon') === false &&
                    strpos($fileUpload, 'background_image') === false &&
                    strpos($fileUpload, 'mobile_image') === false &&
                    strpos($fileUpload, 'gallery') === false &&
                    strpos($fileUpload, 'images') === false) {
                    return $fileUpload;
                }
                
                $modified = true;
                
                // Add optimizeToWebP() at the end
                return $fileUpload . "\n                            ->optimizeToWebP()";
            }, $content);
            
            if ($modified) {
                $filename = basename($file);
                
                if ($isDryRun) {
                    $this->line("Would modify: {$filename}");
                } else {
                    File::put($file, $content);
                    $this->info("Modified: {$filename}");
                }
            }
        }
    }
}