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
        Schema::create('tour_schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tour_id')
                ->constrained('tours')
                ->cascadeOnDelete();

            $table->date('start_date');
            $table->date('end_date');

            $table->unsignedInteger('capacity');
            $table->unsignedInteger('available_slots');

            $table->foreignId('price_tier_id')
                ->constrained('tour_price_tiers')
                ->cascadeOnDelete();
            $table->text('best_time_visit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_schedules');
    }
};
