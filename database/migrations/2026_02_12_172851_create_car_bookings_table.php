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
        Schema::create('car_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('car_id')->constrained();
            $table->dateTime('pickup_datetime');
            $table->dateTime('dropoff_datetime');
            $table->string('pickup_location');
            $table->string('dropoff_location');
            $table->integer('driver_age');
            $table->decimal('total_price', 15, 2);
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });

        Schema::create('car_booking_extra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('extra_id')->constrained();
            $table->decimal('unit_price', 10, 2); 
            $table->timestamps();
        });

        Schema::create('car_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->foreignId('car_booking_id')->constrained();
            $table->integer('rating')->unsigned(); 
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_bookings');
    }
};
