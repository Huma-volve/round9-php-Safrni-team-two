<?php

namespace App\Http\Controllers\Cars;

use App\Http\Controllers\Controller;
use App\Http\Resources\Cars\CarCompareResource;
use App\Http\Resources\Cars\CarResource;
use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{

    // Home Page With Search and Filter Functionality
    public function index(Request $request)
    {

        $query = Car::with(['brand', 'reviews'])->where('is_available', 1)
            ->when($request->location, fn($q) => $q->where('location', 'like', '%' . $request->location . '%'))
            ->when($request->start && $request->end, fn($q) => $q->where('is_available', true)
                ->whereDoesntHave('bookings', fn($b) => $b->whereBetween('pickup_datetime', [$request->start, $request->end])))
            ->when($request->brand_id, fn($q) => $q->whereHas('brand', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->brand . '%');
            }))
            ->when($request->min_seats, fn($q) => $q->where('seats_count', '>=', $request->min_seats))
            ->when($request->max_price, fn($q) => $q->where('base_price_per_hour', '<=', $request->max_price))
            ->when($request->features, fn($q) => $q->whereJsonContains('features', $request->features));

            if($query->count() == 0){
                return response()->json(['message' => 'No cars found matching the criteria.'], 404);
            }
        return CarResource::collection($query->paginate(5));
    }

    public function show(Car $car)
    {
        return new CarResource($car);
    }

    public function compare(Request $request)
    {
        $carIds = $request->input('car_ids', []);
        $cars = Car::with(['brand', 'reviews'])
            ->whereIn('id', $carIds)
            ->get();
        return CarCompareResource::collection($cars);
    }


}
