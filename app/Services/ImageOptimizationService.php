<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;

class ImageOptimizationService
{
    protected int $targetSize = 50 * 1024; // 50KB in bytes
    protected string $format = 'webp';
    protected int $maxWidth = 1920;
    protected int $maxHeight = 1080;
    protected ?ImageManager $manager = null;

    public function __construct()
    {
        // Check if GD is available before initializing
        if (extension_loaded('gd')) {
            // Create new image manager with GD driver
            $this->manager = new ImageManager(new Driver());
        }
    }

    /**
     * Optimize and convert image to WebP format
     */
    public function optimizeImage($source, string $disk = 'public', string $directory = 'optimized'): ?string
    {
        try {
            // Return null if manager is not initialized (GD not available)
            if (!$this->manager) {
                \Log::warning('ImageOptimizationService: GD extension not available');
                return null;
            }
            // Handle different input types
            if ($source instanceof UploadedFile) {
                $image = $this->manager->read($source->getRealPath());
                $originalName = pathinfo($source->getClientOriginalName(), PATHINFO_FILENAME);
            } elseif (is_string($source)) {
                // Handle file path or URL
                if (filter_var($source, FILTER_VALIDATE_URL)) {
                    $image = $this->manager->read($source);
                    $originalName = Str::random(10);
                } else {
                    $fullPath = Storage::disk($disk)->path($source);
                    if (!file_exists($fullPath)) {
                        return null;
                    }
                    $image = $this->manager->read($fullPath);
                    $originalName = pathinfo($source, PATHINFO_FILENAME);
                }
            } else {
                return null;
            }

            // Resize if image is too large
            if ($image->width() > $this->maxWidth || $image->height() > $this->maxHeight) {
                $image->scale($this->maxWidth, $this->maxHeight);
            }

            // Generate unique filename
            $filename = $originalName . '_' . time() . '_' . Str::random(5) . '.webp';
            $path = $directory . '/' . $filename;

            // Start with quality 90 and reduce until file size is under target
            $quality = 90;
            $minQuality = 20;
            $tempPath = null;

            while ($quality >= $minQuality) {
                // Encode to WebP with current quality
                $encoded = $image->toWebp($quality);
                
                // Check file size
                if (strlen($encoded) <= $this->targetSize) {
                    // Save to storage
                    Storage::disk($disk)->put($path, $encoded);
                    return $path;
                }

                // Reduce quality for next iteration
                $quality -= 10;
            }

            // If we couldn't get under 50KB even at minimum quality,
            // resize the image further and try again
            $scaleFactor = 0.8;
            while ($scaleFactor > 0.3) {
                $newWidth = intval($image->width() * $scaleFactor);
                $newHeight = intval($image->height() * $scaleFactor);
                
                $resizedImage = clone $image;
                $resizedImage->scale($newWidth, $newHeight);
                
                // Try with quality 60 after resizing
                $encoded = $resizedImage->toWebp(60);
                
                if (strlen($encoded) <= $this->targetSize) {
                    Storage::disk($disk)->put($path, $encoded);
                    return $path;
                }
                
                $scaleFactor -= 0.1;
            }

            // Last resort: save at minimum size and quality
            $finalImage = clone $image;
            $finalImage->scale(800, 600);
            
            $encoded = $finalImage->toWebp(30);
            Storage::disk($disk)->put($path, $encoded);
            
            return $path;

        } catch (\Exception $e) {
            \Log::error('Image optimization failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Optimize multiple images
     */
    public function optimizeMultiple(array $sources, string $disk = 'public', string $directory = 'optimized'): array
    {
        $results = [];
        
        foreach ($sources as $key => $source) {
            $results[$key] = $this->optimizeImage($source, $disk, $directory);
        }
        
        return $results;
    }

    /**
     * Delete original image after optimization
     */
    public function deleteOriginal(string $path, string $disk = 'public'): bool
    {
        try {
            if (Storage::disk($disk)->exists($path)) {
                return Storage::disk($disk)->delete($path);
            }
            return false;
        } catch (\Exception $e) {
            \Log::error('Failed to delete original image: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get file size in human readable format
     */
    public function getFileSize(string $path, string $disk = 'public'): string
    {
        try {
            $size = Storage::disk($disk)->size($path);
            
            if ($size < 1024) {
                return $size . ' B';
            } elseif ($size < 1048576) {
                return round($size / 1024, 2) . ' KB';
            } else {
                return round($size / 1048576, 2) . ' MB';
            }
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }
}