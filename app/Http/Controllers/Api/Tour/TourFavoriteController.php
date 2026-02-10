<?php

namespace App\Http\Controllers\Api\Tour;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class TourFavoriteController extends Controller
{
    // List favorites for the logged-in user
    public function index(Request $request)
    {
        $category_id = Category::where('key', "tour")->value('id');

        // $favorites = Favorite::where('user_id', Auth::id())->where('category_id',$category_id)->get();
        $favorites = Favorite::where('user_id', 1)->where('category_id',$category_id)->get();  //test

        return response()->json([
            'success' => true,
            'data' => $favorites
        ]);
    }

    // Add a new favorite
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
              'item_id'  => 'required|integer',
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
        $category_id = Category::where('key', "tour")->value('id');
        $favorite = Favorite::updateOrCreate(
            [
                'user_id'  => 1, //Auth::id()
                'category_id' => $category_id,
                'item_id'  => $request->item_id
            ],
            ['added_at' => now()]
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
             'item_id'  => 'required|integer',
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
        $category_id = Category::where('key', "tour")->value('id');

        $deleted = Favorite::where('user_id', 1) //Auth::id()
            ->where('category_id', $category_id)
            ->where('item_id', $request->item_id)
            ->delete();

        return response()->json([
            'success' => $deleted ? true : false,
            'message' => $deleted ? 'Favorite removed' : 'Favorite not found'
        ]);
    }
}
