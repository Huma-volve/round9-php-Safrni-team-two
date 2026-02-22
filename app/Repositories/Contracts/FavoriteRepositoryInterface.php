<?php

namespace App\Repositories\Contracts;

use App\Models\Favorite;
use Illuminate\Database\Eloquent\Collection;

interface FavoriteRepositoryInterface
{
    public function add(int $userId, string $type, int $id): Favorite;

    public function remove(int $userId, string $type, int $id): bool;

    public function isFavorited(int $userId, string $type, int $id): bool;

    public function getUserFavorites(int $userId, ?string $type = null): Collection;

    public function findById(int $id): ?Favorite;
}