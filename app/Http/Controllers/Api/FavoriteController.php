<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Favorite\ToggleFavoriteRequest;
use App\Http\Resources\FavoriteResource;
use App\Services\FavoriteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function __construct(
        protected FavoriteService $favoriteService
    ) {}

    /**
     * GET /api/safarni/favorites
     * قائمة المفضلة للمستخدم
     */
    public function index(Request $request): JsonResponse
    {
        $type   = $request->input('type'); // hotel, room, tour...
        $result = $this->favoriteService->getUserFavorites(auth()->id(), $type);

        return response()->json([
            'success' => true,
            'data'    => FavoriteResource::collection($result['data']),
        ]);
    }

    /**
     * POST /api/safarni/favorites
     * إضافة / إزالة من المفضلة (toggle)
     */
    public function toggle(ToggleFavoriteRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $result = $this->favoriteService->toggle(
            auth()->id(),
            $validated['type'],
            $validated['entity_id']
        );

        if (! $result['success']) {
            return response()->json(['success' => false, 'message' => $result['message']], 422);
        }

        return response()->json([
            'success'   => true,
            'favorited' => $result['favorited'],
            'message'   => $result['message'],
        ]);
    }

    /**
     * DELETE /api/safarni/favorites/{id}
     * حذف من المفضلة بالـ ID
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->favoriteService->remove($id, auth()->id());

        if (! $result['success']) {
            $code = $result['message'] === 'Unauthorized.' ? 403 : 404;
            return response()->json(['success' => false, 'message' => $result['message']], $code);
        }

        return response()->json(['success' => true, 'message' => $result['message']]);
    }
}