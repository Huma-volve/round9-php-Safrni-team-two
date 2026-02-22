<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Review\RejectReviewRequest;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(
        protected ReviewService $reviewService
    ) {}

    // جلب كل الريفيوز
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'reviewable_type']);
        $perPage = $request->input('per_page', 15);

        $result = $this->reviewService->getAllReviews($filters, $perPage);

        return response()->json([
            'success' => true,
            'data'    => $result['data'],
            'meta'    => [
                'current_page' => $result['data']->currentPage(),
                'total'        => $result['data']->total(),
                'per_page'     => $result['data']->perPage(),
                'last_page'    => $result['data']->lastPage(),
            ],
        ]);
    }

    // الموافقة على ريفيو
    public function approve(int $id): JsonResponse
    {
        $result = $this->reviewService->approveReview($id);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
        ]);
    }

    // رفض ريفيو
    public function reject(RejectReviewRequest $request, int $id): JsonResponse
    {
        $result = $this->reviewService->rejectReview($id, $request->rejection_reason);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
        ]);
    }

    // حذف ريفيو
    public function destroy(int $id): JsonResponse
    {
        $result = $this->reviewService->deleteReview($id);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
        ]);
    }
}