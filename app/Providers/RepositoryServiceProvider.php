<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Hotels & Rooms
use App\Repositories\Contracts\HotelRepositoryInterface;
use App\Repositories\HotelRepository;

use App\Repositories\Contracts\RoomRepositoryInterface;
use App\Repositories\RoomRepository;

// Booking
use App\Repositories\Contracts\RoomBookingRepositoryInterface;
use App\Repositories\RoomBookingRepository;

// Review
use App\Repositories\Contracts\ReviewRepositoryInterface;
use App\Repositories\ReviewRepository;

// Favorite
use App\Repositories\Contracts\FavoriteRepositoryInterface;
use App\Repositories\FavoriteRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(HotelRepositoryInterface::class,       HotelRepository::class);
        $this->app->bind(RoomBookingRepositoryInterface::class,  RoomBookingRepository::class);
        $this->app->bind(ReviewRepositoryInterface::class,       ReviewRepository::class);
        $this->app->bind(FavoriteRepositoryInterface::class,     FavoriteRepository::class);
        $this->app->bind(RoomRepositoryInterface::class, RoomRepository::class);

       

    }
}