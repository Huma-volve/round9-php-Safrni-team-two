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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            
            // Basic Info
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            
            // Media
            $table->json('photos')->nullable();
            
            // Occupancy
            $table->tinyInteger('max_adults')->unsigned()->default(2);
            $table->tinyInteger('max_children')->unsigned()->default(0);
            $table->tinyInteger('max_infants')->unsigned()->default(0);
            $table->tinyInteger('total_occupancy')->unsigned()->default(2);
            
            // Room Details
            $table->string('bed_type', 100)->nullable();
            $table->tinyInteger('number_of_beds')->unsigned()->default(1);
            $table->decimal('room_area', 8, 2)->nullable();
            $table->enum('room_area_unit', ['sqm', 'sqft'])->default('sqm');
            
            // Pricing
            $table->decimal('base_price_per_night', 10, 2);
            $table->string('currency', 3)->default('USD');
            
            // Availability
            $table->integer('total_rooms')->unsigned()->default(1);
            $table->boolean('is_refundable')->default(true);
            
            // Amenities & Extras
            $table->json('amenities')->nullable();
            $table->json('extras')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('hotel_id');
            $table->index('base_price_per_night');
            $table->index('total_occupancy');
            $table->index('is_active');
            $table->unique(['hotel_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};