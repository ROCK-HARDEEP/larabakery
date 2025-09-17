<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImageOptimizationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category;
use App\Models\HeroSlide;
use App\Models\Blog;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\HeaderFooterSetting;
use App\Models\AboutUs;
use App\Models\ContactUs;
use App\Models\CustomSection;

class ConvertExistingImagesToWebP extends Command
{
    protected $signature = 'images:convert-webp 
                            {--dry-run : Preview changes without executing}
                            {--model= : Convert images for specific model only}';

    protected $description = 'Convert all existing images to WebP format and compress to under 50KB';

    protected ImageOptimizationService $optimizer;
    protected int $converted = 0;
    protected int $failed = 0;
    protected int $skipped = 0;

    public function __construct()
    {
        parent::__construct();
        $this->optimizer = new ImageOptimizationService();
    }

    public function handle()
    {
        $this->info('Starting image conversion to WebP format...');
        
        $isDryRun = $this->option('dry-run');
        $specificModel = $this->option('model');
        
        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        // Define models and their image fields
        $models = [
            'Product' => ['images_path'], // JSON array field
            'Category' => ['image'],
            'HeroSlide' => ['image', 'mobile_image'],
            'Blog' => ['featured_image'],
            'Testimonial' => ['customer_image'],
            'User' => ['avatar'],
            'HeaderFooterSetting' => ['logo', 'favicon'],
            'AboutUs' => ['image'],
            'ContactUs' => ['background_image'],
            'CustomSection' => ['background_image'],
        ];

        if ($specificModel) {
            if (!isset($models[$specificModel])) {
                $this->error("Model {$specificModel} not found!");
                return 1;
            }
            $models = [$specificModel => $models[$specificModel]];
        }

        foreach ($models as $modelName => $fields) {
            $this->processModel($modelName, $fields, $isDryRun);
        }

        // Process storage directories for orphaned images
        $this->processStorageDirectories($isDryRun);

        $this->info("\n=== Conversion Summary ===");
        $this->info("Converted: {$this->converted}");
        $this->info("Failed: {$this->failed}");
        $this->info("Skipped: {$this->skipped}");
        
        if ($isDryRun) {
            $this->warn('This was a dry run. Run without --dry-run to apply changes.');
        }

        return 0;
    }

    protected function processModel(string $modelName, array $fields, bool $isDryRun)
    {
        $this->info("\nProcessing {$modelName}...");
        
        $modelClass = "App\\Models\\{$modelName}";
        
        if (!class_exists($modelClass)) {
            $this->warn("Model class {$modelClass} not found, skipping...");
            return;
        }

        $records = $modelClass::all();
        
        if ($records->isEmpty()) {
            $this->info("No records found for {$modelName}");
            return;
        }

        $bar = $this->output->createProgressBar($records->count());
        $bar->start();

        foreach ($records as $record) {
            foreach ($fields as $field) {
                if (!empty($record->$field)) {
                    $this->processImageField($record, $field, $isDryRun);
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->line('');
    }

    protected function processImageField($record, string $field, bool $isDryRun)
    {
        $value = $record->$field;
        
        // Handle arrays (like images_path in Product model)
        if (is_array($value)) {
            $convertedImages = [];
            
            foreach ($value as $image) {
                $converted = $this->convertSingleImage($image, $isDryRun);
                if ($converted) {
                    $convertedImages[] = $converted;
                } else {
                    $convertedImages[] = $image; // Keep original if conversion fails
                }
            }
            
            if (!$isDryRun && count($convertedImages) > 0) {
                $record->$field = $convertedImages;
                $record->save();
            }
        }
        // Handle JSON strings
        elseif (is_string($value) && $this->isJson($value)) {
            $images = json_decode($value, true);
            $convertedImages = [];
            
            foreach ($images as $image) {
                $converted = $this->convertSingleImage($image, $isDryRun);
                if ($converted) {
                    $convertedImages[] = $converted;
                } else {
                    $convertedImages[] = $image; // Keep original if conversion fails
                }
            }
            
            if (!$isDryRun) {
                $record->$field = json_encode($convertedImages);
                $record->save();
            }
        } else {
            // Handle single image field
            $converted = $this->convertSingleImage($value, $isDryRun);
            
            if ($converted && !$isDryRun) {
                $record->$field = $converted;
                $record->save();
            }
        }
    }

    protected function convertSingleImage(string $imagePath, bool $isDryRun): ?string
    {
        // Skip if already WebP
        if (pathinfo($imagePath, PATHINFO_EXTENSION) === 'webp') {
            $this->skipped++;
            return null;
        }

        // Skip if file doesn't exist
        if (!Storage::disk('public')->exists($imagePath)) {
            $this->failed++;
            return null;
        }

        if ($isDryRun) {
            $size = $this->optimizer->getFileSize($imagePath);
            $this->line("Would convert: {$imagePath} (Current size: {$size})");
            return $imagePath;
        }

        try {
            // Convert image
            $directory = dirname($imagePath);
            if ($directory === '.') {
                $directory = 'optimized';
            }
            
            $optimizedPath = $this->optimizer->optimizeImage($imagePath, 'public', $directory);
            
            if ($optimizedPath) {
                // Delete original image
                $this->optimizer->deleteOriginal($imagePath);
                
                $this->converted++;
                return $optimizedPath;
            }
        } catch (\Exception $e) {
            $this->error("Failed to convert {$imagePath}: " . $e->getMessage());
            $this->failed++;
        }

        return null;
    }

    protected function processStorageDirectories(bool $isDryRun)
    {
        $this->info("\nProcessing orphaned images in storage...");
        
        $directories = [
            'products',
            'categories',
            'hero-slides',
            'blogs',
            'testimonials',
            'avatars',
            'settings',
            'about',
            'contact',
            'custom-sections',
        ];

        foreach ($directories as $dir) {
            if (!Storage::disk('public')->exists($dir)) {
                continue;
            }

            $files = Storage::disk('public')->files($dir);
            
            foreach ($files as $file) {
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                
                // Skip non-image files and WebP files
                if (!in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'bmp']) 
                    || $extension === 'webp') {
                    continue;
                }

                if ($isDryRun) {
                    $size = $this->optimizer->getFileSize($file);
                    $this->line("Would convert orphaned: {$file} (Size: {$size})");
                } else {
                    try {
                        $optimizedPath = $this->optimizer->optimizeImage($file, 'public', $dir);
                        
                        if ($optimizedPath) {
                            $this->optimizer->deleteOriginal($file);
                            $this->converted++;
                            $this->info("Converted orphaned: {$file} -> {$optimizedPath}");
                        }
                    } catch (\Exception $e) {
                        $this->error("Failed to convert orphaned {$file}: " . $e->getMessage());
                        $this->failed++;
                    }
                }
            }
        }
    }

    protected function isJson($string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}