<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Review\CreateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(
        protected ReviewService $reviewService
    ) {}

    /**
     * GET /api/safarni/{type}/{id}/reviews
     * عرض reviews لفندق أو غرفة
     */
    public function index(Request $request, string $type, int $id): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $result  = $this->reviewService->getReviews($type, $id, $perPage);

        if (! $result['success']) {
            return response()->json(['success' => false, 'message' => $result['message']], 400);
        }

        return response()->json([
            'success' => true,
            'data'    => ReviewResource::collection($result['data']),
            'meta'    => [
                'average_rating' => $result['meta']['average_rating'],
                'current_page'   => $result['data']->currentPage(),
                'total'          => $result['data']->total(),
                'per_page'       => $result['data']->perPage(),
                'last_page'      => $result['data']->lastPage(),
            ],
        ]);
    }

    /**
     * POST /api/safarni/reviews
     * إضافة review جديد
     */
    public function store(CreateReviewRequest $request): JsonResponse
    {
        $result = $this->reviewService->createReview(
            auth()->id(),
            $request->validated()
        );

        if (! $result['success']) {
            return response()->json(['success' => false, 'message' => $result['message']], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Review submitted and pending approval.',
            'data'    => new ReviewResource($result['data']),
        ], 201);
    }

    /**
     * DELETE /api/safarni/reviews/{id}
     * حذف review (الـ owner بس)
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->reviewService->deleteReview($id, auth()->id());

        if (! $result['success']) {
            $code = $result['message'] === 'Unauthorized.' ? 403 : 404;
            return response()->json(['success' => false, 'message' => $result['message']], $code);
        }

        return response()->json(['success' => true, 'message' => $result['message']]);
    }

    /**
     * POST /api/safarni/reviews/{id}/helpful
     * تصويت مفيد
     */
    public function markHelpful(int $id): JsonResponse
    {
        $result = $this->reviewService->markHelpful($id);
        return response()->json(['success' => true, 'message' => $result['message']]);
    }
}