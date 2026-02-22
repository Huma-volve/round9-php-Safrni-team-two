<?php

namespace App\Repositories\Contracts;

use App\Models\Review;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ReviewRepositoryInterface
{
    public function create(array $data): Review;

    public function findById(int $id): ?Review;

    public function getForEntity(string $type, int $id, int $perPage = 15): LengthAwarePaginator;

    public function getUserReviews(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function hasReviewed(int $userId, string $type, int $id): bool;

    public function approve(int $id): bool;

    public function reject(int $id, string $reason): bool;

    public function delete(int $id): bool;

    public function incrementHelpful(int $id): bool;

    public function getAverageRating(string $type, int $id): float;
}