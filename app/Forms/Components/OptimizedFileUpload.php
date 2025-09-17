<?php

namespace App\Forms\Components;

use Filament\Forms\Components\FileUpload;
use App\Services\ImageOptimizationService;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class OptimizedFileUpload extends FileUpload
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->acceptedFileTypes([
            'image/jpeg',
            'image/jpg', 
            'image/png',
            'image/gif',
            'image/bmp',
            'image/webp',
            'image/svg+xml',
        ]);

        // Override the save upload process
        $this->saveUploadedFileUsing(function (TemporaryUploadedFile $file, ?string $storagePath = null) {
            $optimizer = new ImageOptimizationService();

            // Determine the directory from the storage path or use default
            if ($storagePath) {
                $directory = dirname($storagePath);
                if ($directory === '.') {
                    $directory = 'optimized';
                }
            } else {
                // Use the directory set on the component or default
                $directory = $this->getDirectory() ?? 'optimized';
            }

            // Optimize the image
            $optimizedPath = $optimizer->optimizeImage($file, 'public', $directory);

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

            Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));

            return $path;
        });

        // Override the delete process to clean up optimized images
        $this->deleteUploadedFileUsing(function (string $file) {
            if (Storage::disk('public')->exists($file)) {
                Storage::disk('public')->delete($file);
            }
        });

        // Add helper text
        $this->helperText('Images will be automatically converted to WebP format and optimized to under 50KB');
    }

    /**
     * Create a static make method for easy instantiation
     */
    public static function make(string $name): static
    {
        $static = parent::make($name);
        
        return $static;
    }
}