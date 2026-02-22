<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * HasSlug Trait
 * 
 * Provides automatic slug generation functionality.
 * Follows Don't Repeat Yourself (DRY) principle.
 */
trait HasSlug
{
    /**
     * Boot the trait.
     */
    protected static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug) && isset($model->name)) {
                $model->slug = static::generateUniqueSlug($model->name, $model);
            }
        });
    }

    /**
     * Generate a unique slug.
     */
    protected static function generateUniqueSlug(string $name, $model): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        // Check for parent relationship (like hotel_id for rooms)
        $parentColumn = null;
        if (method_exists($model, 'hotel')) {
            $parentColumn = 'hotel_id';
        }

        while (static::slugExists($slug, $model, $parentColumn)) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Check if slug exists.
     */
    protected static function slugExists(string $slug, $model, ?string $parentColumn): bool
    {
        $query = static::where('slug', $slug);

        if ($parentColumn && isset($model->{$parentColumn})) {
            $query->where($parentColumn, $model->{$parentColumn});
        }

        if (isset($model->id)) {
            $query->where('id', '!=', $model->id);
        }

        return $query->exists();
    }
}