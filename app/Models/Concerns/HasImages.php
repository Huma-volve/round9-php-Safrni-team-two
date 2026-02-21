<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;

/**
 * HasImages Trait
 * 
 * Provides image handling functionality.
 * Follows Single Responsibility and DRY principles.
 */
trait HasImages
{
    /**
     * Get URL for a single image path.
     */
    public function getImageUrl(?string $imagePath, ?string $default = null): string
    {
        if (!$imagePath) {
            return $default ?? asset('images/placeholder.jpg');
        }

        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            return $imagePath;
        }

        return asset('storage/' . $imagePath);
    }

    /**
     * Get URLs for multiple image paths.
     */
    public function getImageUrls(?array $imagePaths): array
    {
        if (!$imagePaths) {
            return [];
        }

        return array_map(function ($path) {
            return $this->getImageUrl($path);
        }, $imagePaths);
    }

    /**
     * Delete an image file.
     */
    public function deleteImage(?string $imagePath): bool
    {
        if (!$imagePath) {
            return false;
        }

        if (Storage::disk('public')->exists($imagePath)) {
            return Storage::disk('public')->delete($imagePath);
        }

        return false;
    }

    /**
     * Delete multiple image files.
     */
    public function deleteImages(?array $imagePaths): void
    {
        if (!$imagePaths) {
            return;
        }

        foreach ($imagePaths as $path) {
            $this->deleteImage($path);
        }
    }

    /**
     * Boot the trait - handle cleanup on delete.
     */
    protected static function bootHasImages(): void
    {
        static::deleting(function ($model) {
            // Delete main image if exists
            if (isset($model->main_image)) {
                $model->deleteImage($model->main_image);
            }

            // Delete gallery/photos if exists
            if (isset($model->gallery) && is_array($model->gallery)) {
                $model->deleteImages($model->gallery);
            }

            if (isset($model->photos) && is_array($model->photos)) {
                $model->deleteImages($model->photos);
            }
        });
    }
}