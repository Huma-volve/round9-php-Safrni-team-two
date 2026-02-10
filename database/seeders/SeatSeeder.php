<?php

namespace Database\Seeders;

use App\Models\Aircraft;
use App\Models\Seat;
use Illuminate\Database\Seeder;

class SeatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $aircrafts = Aircraft::all();

        foreach ($aircrafts as $aircraft) {
            // Let's assume 3 rows for Business (Row 1-3) and others for Economy
            // Layout: ABC (Window, Middle, Window) - simplistic for now
            // Aircraft 1: 10 seats total
            
            $rows = ceil($aircraft->total_seats / 3);

            for ($r = 1; $r <= $rows; $r++) {
                $cols = ['A', 'B', 'C'];
                foreach ($cols as $c) {
                    $class = ($r <= 1) ? 'business' : 'economy';
                    $pos = ($c == 'B') ? 'middle' : 'window';

                    Seat::firstOrCreate([
                        'aircraft_id' => $aircraft->id,
                        'row_number' => $r,
                        'column_letter' => $c,
                    ], [
                        'class_type' => $class,
                        'seat_position' => $pos,
                        'status' => 'active',
                    ]);
                }
            }
        }
    }
}
