<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    /**
     * Store an uploaded image in the standardized product images directory.
     *
     * @param UploadedFile $file The uploaded image file
     * @param string $productType The product type (raw-materials, semi-finished, finished)
     * @param bool $useTimestamp Whether to prefix filename with timestamp
     * @return string|null The relative path to the stored image
     */
    public static function storeProductImage(UploadedFile $file, string $productType, bool $useTimestamp = true)
    {
        // Make sure product type is valid
        if (!in_array($productType, ['materials', 'semi-finished', 'finished'])) {
            throw new \InvalidArgumentException("Invalid product type: $productType");
        }
        
        // Generate unique filename
        $filename = '';
        if ($useTimestamp) {
            $filename .= time() . '_';
        }
        $filename .= uniqid() . '.' . $file->getClientOriginalExtension();
        
        // Store the file
        $path = $file->storeAs("storage/{$productType}", $filename, 'public');
        
        // Return the path relative to storage/app/public
        return $path;
    }
    
    /**
     * Get the full public URL for an image path.
     *
     * @param string|null $path The image path relative to storage/app/public
     * @return string|null The public URL to the image
     */
    public static function getImageUrl(?string $path)
    {
        if (empty($path)) {
            return null;
        }
        
        // Normalize: trim whitespace and normalize slashes
        $path = trim($path);
        if ($path === '') {
            return null;
        }

        // If absolute URL, return as-is
        if (preg_match('~^https?://~i', $path)) {
            \Log::info('ImageHelper@getImageUrl absolute URL', ['input' => $path]);
            return $path;
        }

        // Normalize slashes and strip accidental leading slash for asset()
        $normalized = str_replace('\\', '/', $path);
        $normalized = ltrim($normalized, '/');
        $lower = strtolower($normalized);

        // Handle paths starting with public/
        if (str_starts_with($lower, 'public/')) {
            $normalized = substr($normalized, 7); // remove 'public/'
            $lower = strtolower($normalized);
        }

        // Handle old image paths under public/img
        if (str_starts_with($lower, 'img/')) {
            $resolved = asset($normalized);
            \Log::info('ImageHelper@getImageUrl resolved public img', [
                'input' => $path,
                'normalized' => $normalized,
                'resolved' => $resolved
            ]);
            return $resolved;
        }

        // Handle storage paths that already include 'storage/'
        if (str_starts_with($lower, 'storage/')) {
            $resolved = asset($normalized);
            \Log::info('ImageHelper@getImageUrl resolved storage path', [
                'input' => $path,
                'normalized' => $normalized,
                'resolved' => $resolved
            ]);
            return $resolved;
        }

        // Default case - use Storage::url like the index page
        $resolved = Storage::url($normalized);
        // Also check existence on public disk for debugging
        $exists = Storage::disk('public')->exists($normalized);
        \Log::info('ImageHelper@getImageUrl resolved public disk', [
            'input' => $path,
            'normalized' => $normalized,
            'resolved' => $resolved,
            'exists' => $exists
        ]);
        return $resolved;
    }
    
    /**
     * Delete an image file.
     *
     * @param string|null $path The image path to delete
     * @return bool Whether the deletion was successful
     */
    public static function deleteImage(?string $path)
    {
        if (empty($path)) {
            return false;
        }
        
        // Handle old image paths
        if (str_starts_with($path, 'img/')) {
            return unlink(public_path($path));
        }
        
        // Handle storage paths that start with storage/
        if (str_starts_with($path, 'storage/')) {
            $realPath = str_replace('storage/', '', $path);
            return Storage::disk('public')->delete($realPath);
        }
        
        // Default case - use Storage::delete
        return Storage::disk('public')->delete($path);
    }
}
