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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('brands');
            $table->Enum('categories',['sedan','suv','hatchback']);
            $table->string('model');
            $table->integer('year');
            $table->integer('seats_count');
            $table->integer('doors');
            $table->string('fuel_type');
            $table->string('transmission'); 
            $table->integer('luggage_capacity');
            $table->boolean('air_conditioning')->default(true);
            $table->json('features')->nullable(); 
            $table->json('images')->nullable(); 
            $table->text('license_requirements')->nullable();
            $table->text('cancellation_policy')->nullable();
            $table->string('location'); 
            $table->decimal('current_location_lat', 10, 7);
            $table->decimal('current_location_lng', 10, 7);
            $table->decimal('base_price_per_hour', 10, 2);
            $table->boolean('is_available')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
