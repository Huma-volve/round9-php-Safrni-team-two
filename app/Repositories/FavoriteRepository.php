<?php

namespace App\Repositories;

use App\Models\Favorite;
use App\Repositories\Contracts\FavoriteRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class FavoriteRepository implements FavoriteRepositoryInterface
{
    public function add(int $userId, string $type, int $id): Favorite
    {
        return Favorite::firstOrCreate([
            'user_id'           => $userId,
            'favoriteable_type' => $type,
            'favoriteable_id'   => $id,
        ]);
    }

    public function remove(int $userId, string $type, int $id): bool
    {
        return (bool) Favorite::where('user_id', $userId)
            ->where('favoriteable_type', $type)
            ->where('favoriteable_id', $id)
            ->delete();
    }

    public function isFavorited(int $userId, string $type, int $id): bool
    {
        return Favorite::where('user_id', $userId)
            ->where('favoriteable_type', $type)
            ->where('favoriteable_id', $id)
            ->exists();
    }

    public function getUserFavorites(int $userId, ?string $type = null): Collection
    {
        $query = Favorite::with('favoriteable')
            ->where('user_id', $userId)
            ->latest();

        if ($type) {
            $query->where('favoriteable_type', $type);
        }

        return $query->get();
    }

    public function findById(int $id): ?Favorite
    {
        return Favorite::find($id);
    }
}