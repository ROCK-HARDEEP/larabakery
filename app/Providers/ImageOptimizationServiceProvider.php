<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Forms\Components\FileUpload;
use App\Services\ImageOptimizationService;
use App\Services\SimpleImageOptimizationService;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ImageOptimizationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Check if GD extension is available
        if (extension_loaded('gd')) {
            $this->app->singleton(ImageOptimizationService::class, function ($app) {
                return new ImageOptimizationService();
            });
        } else {
            // Use simple fallback service when GD is not available
            $this->app->singleton(ImageOptimizationService::class, function ($app) {
                return new SimpleImageOptimizationService();
            });
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Add a macro to FileUpload component for easy optimization
        FileUpload::macro('optimizeToWebP', function () {
            /** @var FileUpload $this */
            
            $this->acceptedFileTypes([
                'image/jpeg',
                'image/jpg', 
                'image/png',
                'image/gif',
                'image/bmp',
                'image/webp',
                'image/svg+xml',
            ]);

            $this->saveUploadedFileUsing(function (TemporaryUploadedFile $file, ?string $storagePath = null) {
                $optimizer = app(ImageOptimizationService::class);

                // Determine the directory from the storage path or use default
                if ($storagePath) {
                    $directory = dirname($storagePath);
                    if ($directory === '.') {
                        $directory = $this->getDirectory() ?? 'optimized';
                    }
                } else {
                    // Use the directory set on the component or default
                    $directory = $this->getDirectory() ?? 'optimized';
                }

                // Optimize the image
                $optimizedPath = $optimizer->optimizeImage($file, $this->getDiskName(), $directory);

                if ($optimizedPath) {
                    // Delete the temporary file
                    $file->delete();

                    return $optimizedPath;
                }

                // Fallback to normal upload if optimization fails
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $newFilename = $filename . '_' . time() . '.' . $extension;
                $path = $directory . '/' . $newFilename;

                Storage::disk($this->getDiskName())->put($path, file_get_contents($file->getRealPath()));

                return $path;
            });

            $this->deleteUploadedFileUsing(function (string $file) {
                if (Storage::disk($this->getDiskName())->exists($file)) {
                    Storage::disk($this->getDiskName())->delete($file);
                }
            });

            $this->helperText('Images will be automatically converted to WebP format and optimized to under 50KB');
            
            return $this;
        });
    }
}