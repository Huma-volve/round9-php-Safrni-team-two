<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\Room;
use App\Repositories\Contracts\FavoriteRepositoryInterface;

class FavoriteService
{
    private const TYPE_MAP = [
        'hotel' => Hotel::class,
        'room'  => Room::class,
        // 'tour'  => Tour::class,
        // 'car'   => Car::class,
        // 'flight' => Flight::class,
    ];

    public function __construct(
        protected FavoriteRepositoryInterface $favoriteRepo,
    ) {}

    // =====================================================
    // Toggle Favorite (Add or Remove)
    // =====================================================

    public function toggle(int $userId, string $type, int $entityId): array
    {
        $modelClass = self::TYPE_MAP[$type] ?? null;

        if (! $modelClass) {
            return ['success' => false, 'message' => 'Invalid type.'];
        }

        // تحقق أن الـ entity موجود
        if (! $modelClass::find($entityId)) {
            return ['success' => false, 'message' => ucfirst($type) . ' not found.'];
        }

        $isFavorited = $this->favoriteRepo->isFavorited($userId, $modelClass, $entityId);

        if ($isFavorited) {
            $this->favoriteRepo->remove($userId, $modelClass, $entityId);
            return ['success' => true, 'favorited' => false, 'message' => 'Removed from favorites.'];
        }

        $favorite = $this->favoriteRepo->add($userId, $modelClass, $entityId);
        return ['success' => true, 'favorited' => true, 'message' => 'Added to favorites.', 'data' => $favorite];
    }

    // =====================================================
    // Get User Favorites
    // =====================================================

    public function getUserFavorites(int $userId, ?string $type = null): array
    {
        $modelClass = $type ? (self::TYPE_MAP[$type] ?? null) : null;

        if ($type && ! $modelClass) {
            return ['success' => false, 'message' => 'Invalid type.'];
        }

        $favorites = $this->favoriteRepo->getUserFavorites($userId, $modelClass);

        return ['success' => true, 'data' => $favorites];
    }

    // =====================================================
    // Remove Favorite by ID
    // =====================================================

    public function remove(int $favoriteId, int $userId): array
    {
        $favorite = $this->favoriteRepo->findById($favoriteId);

        if (! $favorite) {
            return ['success' => false, 'message' => 'Favorite not found.'];
        }

        if ($favorite->user_id !== $userId) {
            return ['success' => false, 'message' => 'Unauthorized.'];
        }

        $this->favoriteRepo->remove(
            $userId,
            $favorite->favoriteable_type,
            $favorite->favoriteable_id
        );

        return ['success' => true, 'message' => 'Removed from favorites.'];
    }

    // =====================================================
    // Check if Favorited
    // =====================================================

    public function isFavorited(int $userId, string $type, int $entityId): array
    {
        $modelClass  = self::TYPE_MAP[$type] ?? null;
        $isFavorited = $modelClass
            ? $this->favoriteRepo->isFavorited($userId, $modelClass, $entityId)
            : false;

        return ['success' => true, 'favorited' => $isFavorited];
    }
}