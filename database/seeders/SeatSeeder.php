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
            // Layout: ABC (Window, Middle, Window) with Row 1 = Business, rest = Economy
            // Always respect the configured total_seats for the aircraft.
            $cols = ['A', 'B', 'C'];
            $createdSeats = 0;
            $row = 0;

            while ($createdSeats < $aircraft->total_seats) {
                $row++;

                foreach ($cols as $c) {
                    if ($createdSeats >= $aircraft->total_seats) {
                        break;
                    }

                    $class = ($row === 1) ? 'business' : 'economy';
                    $pos = ($c === 'B') ? 'middle' : 'window';

                    Seat::firstOrCreate(
                        [
                            'aircraft_id' => $aircraft->id,
                            'row_number' => $row,
                            'column_letter' => $c,
                        ],
                        [
                            'class_type' => $class,
                            'seat_position' => $pos,
                            'status' => 'active',
                        ]
                    );

                    $createdSeats++;
                }
            }
        }
    }
}
