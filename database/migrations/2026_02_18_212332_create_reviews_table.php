<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Polymorphic: reviewable_type = Hotel, Room, Tour, Car...
            $table->morphs('reviewable');  // reviewable_id + reviewable_type
            $table->foreignId('booking_id')->nullable()->constrained('room_bookings')->nullOnDelete();
            $table->unsignedTinyInteger('rating');          // 1-5
            $table->string('title', 150)->nullable();
            $table->text('body');
            $table->json('photos')->nullable();
            $table->unsignedInteger('helpful_votes')->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['reviewable_type', 'reviewable_id', 'status']);
            $table->index(['user_id', 'status']);
            // منع المستخدم من عمل أكتر من review لنفس الحجز
            $table->unique(['user_id', 'booking_id', 'reviewable_type', 'reviewable_id'], 'unique_review');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};