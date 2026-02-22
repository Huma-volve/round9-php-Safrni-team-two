<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\Room;
use App\Repositories\Contracts\ReviewRepositoryInterface;
use App\Repositories\Contracts\RoomBookingRepositoryInterface;

class ReviewService
{
    // Map بين الـ type اللي بييجي من الـ API والـ Model الفعلي
    private const TYPE_MAP = [
        'hotel' => Hotel::class,
        'room'  => Room::class,
        // 'tour'  => Tour::class,   — هتضيفيها لما تشتغلي على Tours
        // 'car'   => Car::class,
    ];

    public function __construct(
        protected ReviewRepositoryInterface $reviewRepo,
        protected RoomBookingRepositoryInterface $bookingRepo,
    ) {}

    // =====================================================
    // Create Review
    // =====================================================

    public function createReview(int $userId, array $data): array
    {
        $modelClass = self::TYPE_MAP[$data['type']] ?? null;

        if (! $modelClass) {
            return ['success' => false, 'message' => 'Invalid review type.'];
        }

        // تحقق أن الـ entity موجود
        $entity = $modelClass::find($data['entity_id']);
        if (! $entity) {
            return ['success' => false, 'message' => ucfirst($data['type']) . ' not found.'];
        }

        // تحقق من عدم التكرار
        if ($this->reviewRepo->hasReviewed($userId, $modelClass, $data['entity_id'])) {
            return ['success' => false, 'message' => 'You have already reviewed this ' . $data['type'] . '.'];
        }

        // تحقق أن المستخدم أكمل حجز (إذا كانت السياسة تشترط ذلك)
        if (! empty($data['booking_id'])) {
            $booking = $this->bookingRepo->findById($data['booking_id']);
            if (! $booking || $booking->user_id !== $userId || ! $booking->isCompleted()) {
                return ['success' => false, 'message' => 'You can only review after completing your stay.'];
            }
        }

        $review = $this->reviewRepo->create([
            'user_id'          => $userId,
            'reviewable_type'  => $modelClass,
            'reviewable_id'    => $data['entity_id'],
            'booking_id'       => $data['booking_id'] ?? null,
            'rating'           => $data['rating'],
            'title'            => $data['title'] ?? null,
            'body'             => $data['body'],
            'photos'           => $data['photos'] ?? null,
            'status'           => 'pending',  // يستنى الـ admin يوافق
        ]);

        return ['success' => true, 'data' => $review->load('user')];
    }

    // =====================================================
    // Get Reviews For Entity
    // =====================================================

    public function getReviews(string $type, int $entityId, int $perPage = 15): array
    {
        $modelClass = self::TYPE_MAP[$type] ?? null;

        if (! $modelClass) {
            return ['success' => false, 'message' => 'Invalid type.'];
        }

        $reviews       = $this->reviewRepo->getForEntity($modelClass, $entityId, $perPage);
        $averageRating = $this->reviewRepo->getAverageRating($modelClass, $entityId);

        return [
            'success' => true,
            'data'    => $reviews,
            'meta'    => ['average_rating' => round($averageRating, 1)],
        ];
    }

    // =====================================================
    // Delete Review (by owner)
    // =====================================================

    public function deleteReview(int $reviewId, int $userId): array
    {
        $review = $this->reviewRepo->findById($reviewId);

        if (! $review) {
            return ['success' => false, 'message' => 'Review not found.'];
        }

        if ($review->user_id !== $userId) {
            return ['success' => false, 'message' => 'Unauthorized.'];
        }

        $this->reviewRepo->delete($reviewId);

        return ['success' => true, 'message' => 'Review deleted.'];
    }

    // =====================================================
    // Mark Review as Helpful
    // =====================================================

    public function markHelpful(int $reviewId): array
    {
        $this->reviewRepo->incrementHelpful($reviewId);
        return ['success' => true, 'message' => 'Marked as helpful.'];
    }

    // =====================================================
    // Admin: Approve / Reject
    // =====================================================

    public function approveReview(int $id): array
    {
        $this->reviewRepo->approve($id);
        return ['success' => true, 'message' => 'Review approved.'];
    }

    public function rejectReview(int $id, string $reason): array
    {
        $this->reviewRepo->reject($id, $reason);
        return ['success' => true, 'message' => 'Review rejected.'];
    }
}