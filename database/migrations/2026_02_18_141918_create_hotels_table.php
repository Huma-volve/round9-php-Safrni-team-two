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
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('full_description')->nullable();
            
            // Location
            $table->text('address');
            $table->string('city', 100);
            $table->string('state', 100)->nullable();
            $table->string('country', 100);
            $table->string('postal_code', 20)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Media
            $table->string('main_image')->nullable();
            $table->json('gallery')->nullable();
            
            // Rating
            $table->tinyInteger('star_rating')->unsigned()->default(0);
            $table->decimal('overall_rating', 3, 2)->default(0.00);
            $table->integer('total_reviews')->default(0);
            
            // Amenities
            $table->json('amenities')->nullable();
            
            // Policies
            $table->time('check_in_time')->default('14:00:00');
            $table->time('check_out_time')->default('12:00:00');
            $table->text('cancellation_policy')->nullable();
            $table->json('policies')->nullable();
            
            // Contact
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->json('contact_info')->nullable();
            
            // Pricing
            $table->decimal('tax_percentage', 5, 2)->default(0.00);
            $table->decimal('service_fee', 10, 2)->default(0.00);
            
            // Status
            $table->boolean('is_recommended')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('city');
            $table->index('slug');
            $table->index(['latitude', 'longitude']);
            $table->index('overall_rating');
            $table->index('is_active');
            $table->fullText(['name', 'city', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};