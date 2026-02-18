<?php

namespace App\Contracts;

interface Payable
{
    // Return the total amount of the booking
    public function getPayableAmount(): float;

    // mark the booking status as (completed || paid || confirmed)
    public function markAsPaid(): void;
}
