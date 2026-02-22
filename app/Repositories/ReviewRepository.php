<?php

namespace App\Repositories;

use App\Models\Review;
use App\Repositories\Contracts\ReviewRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReviewRepository implements ReviewRepositoryInterface
{
    public function create(array $data): Review
    {
        return Review::create($data);
    }

    public function findById(int $id): ?Review
    {
        return Review::with('user')->find($id);
    }

    public function getForEntity(string $type, int $id, int $perPage = 15): LengthAwarePaginator
    {
        return Review::with('user')
            ->where('reviewable_type', $type)
            ->where('reviewable_id', $id)
            ->approved()
            ->latest('approved_at')
            ->paginate($perPage);
    }

    public function getUserReviews(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Review::with('reviewable')
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    public function hasReviewed(int $userId, string $type, int $id): bool
    {
        return Review::where('user_id', $userId)
            ->where('reviewable_type', $type)
            ->where('reviewable_id', $id)
            ->exists();
    }

    public function approve(int $id): bool
    {
        return (bool) Review::where('id', $id)->update([
            'status'      => 'approved',
            'approved_at' => now(),
        ]);
    }

    public function reject(int $id, string $reason): bool
    {
        return (bool) Review::where('id', $id)->update([
            'status'           => 'rejected',
            'rejection_reason' => $reason,
        ]);
    }

    public function delete(int $id): bool
    {
        return (bool) Review::find($id)?->delete();
    }

    public function incrementHelpful(int $id): bool
    {
        return (bool) Review::where('id', $id)->increment('helpful_votes');
    }

    public function getAverageRating(string $type, int $id): float
    {
        return (float) Review::where('reviewable_type', $type)
            ->where('reviewable_id', $id)
            ->approved()
            ->avg('rating') ?? 0;
    }
}