<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->string('flight_number', 20);
            $table->string('carrier');
            $table->foreignId('origin_id')->constrained('airports');
            $table->foreignId('destination_id')->constrained('airports');
            $table->foreignId('aircraft_id')->constrained('aircrafts');
            $table->dateTime('departure_time');
            $table->dateTime('arrival_time');
            $table->boolean('refundability')->default(false);
            $table->enum('status', ['scheduled', 'delayed', 'cancelled'])->default('scheduled');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};
