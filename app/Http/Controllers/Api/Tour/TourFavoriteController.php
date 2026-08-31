<?php

namespace App\Http\Controllers\Api\Tour;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class TourFavoriteController extends Controller
{
    // List favorites for the logged-in user
    public function index(Request $request)
    {
        $favorites = Favorite::where('user_id', Auth::id())
            ->where('favoriteable_type', Tour::class)  // فقط tours
            ->get();

        return response()->json([
            'success' => true,
            'data' => $favorites
        ]);
    }

    // Add a new favorite
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id'  => 'required|integer|exists:tours,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error'   => [
                    'code'    => 'VALIDATION_ERROR',
                    'message' => 'Invalid input',
                    'details' => $validator->errors()
                ]
            ], 422);
        }

        $favorite = Favorite::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'favoriteable_type' => Tour::class,
                'favoriteable_id' => $request->item_id
            ],
            ['created_at' => now(), 'updated_at' => now()]
        );

        return response()->json([
            'success' => true,
            'data'    => $favorite
        ]);
    }

    // Remove a favorite
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id'  => 'required|integer|exists:tours,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error'   => [
                    'code'    => 'VALIDATION_ERROR',
                    'message' => 'Invalid input',
                    'details' => $validator->errors()
                ]
            ], 422);
        }

        $deleted = Favorite::where('user_id', Auth::id())
            ->where('favoriteable_type', Tour::class)
            ->where('favoriteable_id', $request->item_id)
            ->delete();

        return response()->json([
            'success' => $deleted ? true : false,
            'message' => $deleted ? 'Favorite removed' : 'Favorite not found'
        ]);
    }
}