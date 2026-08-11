<?php

namespace App\Http\Controllers\Cars;

use App\Http\Controllers\Controller;
use App\Models\CarFavourire;
use Illuminate\Http\Request;

class CarFavouriteController extends Controller
{
    public function index()
    {
        $favorites = CarFavourire::with('car')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return response()->json($favorites);
    }

    // إضافة للمفضلة
    public function store(Request $request)
    {
        $request->validate([
            'car_id' => 'required|exists:cars,id',
        ]);

        $favorite = CarFavourire::firstOrCreate([
            'user_id' => auth()->id(),
            'car_id' => $request->car_id,
        ]);

        return response()->json([
            'message' => 'Car added to favorites',
            'data' => $favorite
        ], 201);
    }

    // حذف من المفضلة
    public function destroy($carId)
    {
        $favorite = CarFavourire::where('user_id', auth()->id())
            ->where('car_id', $carId)
            ->first();

        if (!$favorite) {
            return response()->json([
                'message' => 'Favorite not found'
            ], 404);
        }

        $favorite->delete();

        return response()->json([
            'message' => 'Car removed from favorites'
        ]);
    }
}
