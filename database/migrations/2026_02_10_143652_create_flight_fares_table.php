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
        Schema::create('flight_fares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flight_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('class_type', ['economy', 'business', 'first']);

            $table->decimal('base_price', 10, 2);
            $table->decimal('taxes', 10, 2)->default(0);
            $table->decimal('baggage_price', 10, 2)->default(0);
            $table->integer('seats_available');
            $table->boolean('is_refundable')->default(false);
            $table->unsignedTinyInteger('stops')->default(0);
            $table->timestamps();
            $table->unique(['flight_id', 'class_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_fares');
    }
};
