<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Flight;
use App\Models\FlightFare;
use App\Models\Passenger;
use App\Models\Seat;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class BookingService
{
    /**
     * Create a new booking with passengers and tickets.
     *
     * @param array $data
     * @param int|null $userId
     * @return Booking
     * @throws ValidationException
     */
    public function createBooking(array $data, $userId = null)
    {
        return DB::transaction(function () use ($data, $userId) {
            $flight = Flight::with(['fares'])->findOrFail($data['flight_id']);
            $classType = $data['class_type'];

            // 1. Get Fare for the selected class
            $fare = $flight->fares->where('class_type', $classType)->first();

            if (!$fare || $fare->seats_available < count($data['passengers'])) {
                throw ValidationException::withMessages(['class_type' => 'Not enough seats available for this class.']);
            }

            // 2. Create Booking Record
            $booking = Booking::create([
                'user_id' => $userId,
                'booking_reference' => strtoupper(Str::random(8)), // Unique PNR
                'contact_email' => $data['contact_email'],
                'contact_phone' => $data['contact_phone'],
                'total_price' => 0, // Will calculate below
                'tax_amount' => 0,
                'status' => 'confirmed', // Assuming instant confirmation for testing
            ]);

            $totalPrice = 0;
            $totalTax = 0;

            foreach ($data['passengers'] as $passengerData) {
                // 3. Create Passenger
                $passenger = Passenger::create([
                    'booking_id' => $booking->id,
                    'first_name' => $passengerData['first_name'],
                    'last_name' => $passengerData['last_name'],
                    'date_of_birth' => $passengerData['date_of_birth'],
                    'passport_number' => $passengerData['passport_number'],
                    'nationality' => $passengerData['nationality'],
                    'special_requests' => $passengerData['special_requests'] ?? null,
                ]);

                // 4. Handle Seat Selection & Validate Availability
                $seatId = $passengerData['seat_id'] ?? null;
                $ticketPrice = $fare->base_price; // Base fare price
                // Enforce that seat selection is mandatory at the service level as well
                if (! $seatId) {
                    throw ValidationException::withMessages(['seat_id' => 'Seat selection is required for each passenger.']);
                }

                // Check if seat is already booked for this flight
                $isSeatTaken = Ticket::where('flight_id', $flight->id)
                    ->where('seat_id', $seatId)
                    ->exists();

                if ($isSeatTaken) {
                    throw ValidationException::withMessages(['seat_id' => "Seat ID {$seatId} is already taken."]);
                }

                // Verify seat exists, belongs to the flight aircraft, and matches the selected class
                $seat = Seat::where('id', $seatId)
                    ->whereHas('aircraft', function ($q) use ($flight) {
                        $q->where('id', $flight->aircraft_id);
                    })
                    ->first();

                if (! $seat) {
                    throw ValidationException::withMessages(['seat_id' => "Selected seat is not valid for this flight."]);
                }

                if ($seat->class_type !== $classType) {
                    throw ValidationException::withMessages(['seat_id' => "Seat {$seat->seat_number} does not belong to {$classType} class."]);
                }

                $totalPrice += ($ticketPrice + $fare->taxes);
                $totalTax += $fare->taxes;

                // 5. Create Ticket
                Ticket::create([
                    'booking_id' => $booking->id,
                    'passenger_id' => $passenger->id,
                    'flight_id' => $flight->id,
                    'seat_id' => $seatId,
                    'ticket_number' => strtoupper(Str::random(10)),
                    'class_type' => $classType,
                    'price_paid' => $ticketPrice + $fare->taxes,
                ]);
            }

            // 6. Update Booking Totals
            $booking->update([
                'total_price' => $totalPrice,
                'tax_amount' => $totalTax,
            ]);

            // 7. Decrement Available Seats
            $fare->decrement('seats_available', count($data['passengers']));

            return $booking->load('passengers.ticket', 'tickets.flight');
        });
    }
}
