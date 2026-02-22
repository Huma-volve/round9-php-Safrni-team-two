<?php

namespace Database\Seeders;

use App\Models\Aircraft;
use App\Models\Airport;
use App\Models\Flight;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FlightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cai = Airport::where('airport_code', 'CAI')->first();
        $dxb = Airport::where('airport_code', 'DXB')->first();
        $lhr = Airport::where('airport_code', 'LHR')->first();
        $ruh = Airport::where('airport_code', 'RUH')->first();

        $b737 = Aircraft::where('model', 'Boeing 737')->first();
        $a320 = Aircraft::where('model', 'Airbus A320')->first();

        if (!$cai || !$dxb || !$b737) {
            return;
        }

        // Flight 1: CAI -> DXB (Tomorrow Morning)
        Flight::create([
            'flight_number' => 'MS901',
            'carrier' => 'EgyptAir',
            'origin_id' => $cai->id,
            'destination_id' => $dxb->id,
            'aircraft_id' => $b737->id,
            'departure_time' => Carbon::tomorrow()->setTime(10, 0),
            'arrival_time' => Carbon::tomorrow()->setTime(14, 30), // 3.5 hours later + time diff
            'refundability' => true,
            'status' => 'scheduled',
        ]);

        // Flight 2: CAI -> DXB (Tomorrow Evening)
        Flight::create([
            'flight_number' => 'EK924',
            'carrier' => 'Emirates',
            'origin_id' => $cai->id,
            'destination_id' => $dxb->id,
            'aircraft_id' => $a320->id,
            'departure_time' => Carbon::tomorrow()->setTime(18, 0),
            'arrival_time' => Carbon::tomorrow()->setTime(23, 30),
            'refundability' => false,
            'status' => 'scheduled',
        ]);

        // Flight 3: DXB -> LHR (Day After Tomorrow)
        Flight::create([
            'flight_number' => 'BA101',
            'carrier' => 'British Airways',
            'origin_id' => $dxb->id,
            'destination_id' => $lhr->id,
            'aircraft_id' => $b737->id,
            'departure_time' => Carbon::tomorrow()->addDay()->setTime(8, 0),
            'arrival_time' => Carbon::tomorrow()->addDay()->setTime(12, 0),
            'refundability' => true,
            'status' => 'scheduled',
        ]);

         // Flight 4: RUH -> CAI (Today)
         Flight::create([
            'flight_number' => 'SV303',
            'carrier' => 'Saudia',
            'origin_id' => $ruh->id,
            'destination_id' => $cai->id,
            'aircraft_id' => $b737->id,
            'departure_time' => Carbon::today()->setTime(15, 0),
            'arrival_time' => Carbon::today()->setTime(17, 0),
            'refundability' => true,
            'status' => 'scheduled',
        ]);
    }
}
