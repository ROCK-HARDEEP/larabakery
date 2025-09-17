<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Simple image optimization service that works without GD/Imagick
 * This creates a mock WebP conversion by renaming files
 */
class SimpleImageOptimizationService
{
    protected int $targetSize = 50 * 1024; // 50KB in bytes
    protected string $format = 'webp';

    /**
     * Mock optimize image - just renames to .webp extension
     * In production, you would need GD or Imagick installed
     */
    public function optimizeImage($source, string $disk = 'public', string $directory = 'optimized'): ?string
    {
        try {
            $originalName = '';
            $sourceContent = '';
            
            // Handle different input types
            if ($source instanceof UploadedFile) {
                $originalName = pathinfo($source->getClientOriginalName(), PATHINFO_FILENAME);
                $sourceContent = file_get_contents($source->getRealPath());
            } elseif (is_string($source)) {
                if (filter_var($source, FILTER_VALIDATE_URL)) {
                    $originalName = Str::random(10);
                    $sourceContent = file_get_contents($source);
                } else {
                    $fullPath = Storage::disk($disk)->path($source);
                    if (!file_exists($fullPath)) {
                        // If full path doesn't exist, try as relative path
                        if (Storage::disk($disk)->exists($source)) {
                            $sourceContent = Storage::disk($disk)->get($source);
                            $originalName = pathinfo($source, PATHINFO_FILENAME);
                        } else {
                            return null;
                        }
                    } else {
                        $sourceContent = file_get_contents($fullPath);
                        $originalName = pathinfo($source, PATHINFO_FILENAME);
                    }
                }
            } else {
                return null;
            }

            // Generate unique filename with .webp extension
            $filename = $originalName . '_' . time() . '_' . Str::random(5) . '.webp';
            $path = $directory . '/' . $filename;

            // For now, just save the original content with .webp extension
            // In production with GD/Imagick, this would actually convert and compress
            Storage::disk($disk)->put($path, $sourceContent);
            
            // Log that optimization is simulated
            \Log::info("Image optimization simulated (GD not available): {$path}");
            
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