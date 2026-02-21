<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotel;
use App\Models\Room;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        $hotels = [
            [
                'name'         => 'Four Seasons Cairo',
                'slug'         => 'four-seasons-cairo',
                'description'  => 'فندق فاخر في قلب القاهرة',
                'address'      => '35 شارع الجيزة',
                'city'         => 'Cairo',
                'country'      => 'Egypt',
                'star_rating'  => 5,
                'latitude'     => 30.0444,
                'longitude'    => 31.2357,
                'check_in_time'  => '14:00',
                'check_out_time' => '12:00',
                'is_active'      => true,
                'is_featured'    => true,
                'is_recommended' => true,
            ],
            [
                'name'         => 'Sofitel Alexandria',
                'slug'         => 'sofitel-alexandria',
                'description'  => 'فندق على شاطئ البحر المتوسط',
                'address'      => 'كورنيش الإسكندرية',
                'city'         => 'Alexandria',
                'country'      => 'Egypt',
                'star_rating'  => 5,
                'latitude'     => 31.2001,
                'longitude'    => 29.9187,
                'check_in_time'  => '15:00',
                'check_out_time' => '11:00',
                'is_active'      => true,
                'is_featured'    => true,
                'is_recommended' => false,
            ],
            [
                'name'         => 'Steigenberger Sharm',
                'slug'         => 'steigenberger-sharm',
                'description'  => 'منتجع على البحر الأحمر',
                'address'      => 'شارع النصر، شرم الشيخ',
                'city'         => 'Sharm El Sheikh',
                'country'      => 'Egypt',
                'star_rating'  => 5,
                'latitude'     => 27.9158,
                'longitude'    => 34.3300,
                'check_in_time'  => '14:00',
                'check_out_time' => '12:00',
                'is_active'      => true,
                'is_featured'    => false,
                'is_recommended' => true,
            ],
            [
                'name'         => 'Hilton Luxor',
                'slug'         => 'hilton-luxor',
                'description'  => 'فندق بإطلالة على النيل والمعابد',
                'address'      => 'كورنيش النيل، الأقصر',
                'city'         => 'Luxor',
                'country'      => 'Egypt',
                'star_rating'  => 4,
                'latitude'     => 25.6872,
                'longitude'    => 32.6396,
                'check_in_time'  => '14:00',
                'check_out_time' => '12:00',
                'is_active'      => true,
                'is_featured'    => false,
                'is_recommended' => true,
            ],
            [
                'name'         => 'Kempinski Hurghada',
                'slug'         => 'kempinski-hurghada',
                'description'  => 'منتجع فاخر على البحر الأحمر',
                'address'      => 'سيدي عبد الرحمن، الغردقة',
                'city'         => 'Hurghada',
                'country'      => 'Egypt',
                'star_rating'  => 5,
                'latitude'     => 27.2579,
                'longitude'    => 33.8116,
                'check_in_time'  => '15:00',
                'check_out_time' => '11:00',
                'is_active'      => true,
                'is_featured'    => true,
                'is_recommended' => true,
            ],
        ];

        foreach ($hotels as $hotelData) {
            $hotel = Hotel::create($hotelData);

            // إنشاء 2 غرف لكل فندق
            $rooms = [
                [
                    'hotel_id'             => $hotel->id,
                    'name'                 => 'غرفة ستاندرد',
                    'slug'                 => $hotel->slug . '-standard-room',
                    'description'          => 'غرفة مريحة بإطلالة جميلة',
                    'max_adults'           => 2,
                    'max_children'         => 1,
                    'max_infants'          => 1,
                    'total_occupancy'      => 3,
                    'bed_type'             => 'double',
                    'number_of_beds'       => 1,
                    'room_area'            => 35,
                    'room_area_unit'       => 'sqm',
                    'base_price_per_night' => 150.00,
                    'currency'             => 'USD',
                    'total_rooms'          => 10,
                    'is_refundable'        => true,
                    'is_active'            => true,
                    'display_order'        => 1,
                ],
                [
                    'hotel_id'             => $hotel->id,
                    'name'                 => 'جناح ديلوكس',
                    'slug'                 => $hotel->slug . '-deluxe-suite',
                    'description'          => 'جناح فاخر بصالة جلوس منفصلة',
                    'max_adults'           => 3,
                    'max_children'         => 2,
                    'max_infants'          => 2,
                    'total_occupancy'      => 5,
                    'bed_type'             => 'king',
                    'number_of_beds'       => 1,
                    'room_area'            => 65,
                    'room_area_unit'       => 'sqm',
                    'base_price_per_night' => 350.00,
                    'currency'             => 'USD',
                    'total_rooms'          => 5,
                    'is_refundable'        => true,
                    'is_active'            => true,
                    'display_order'        => 2,
                ],
            ];

            foreach ($rooms as $roomData) {
                Room::create($roomData);
            }
        }
    }
}