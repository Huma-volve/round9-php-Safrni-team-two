<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Polymorphic: يدعم Hotel, Room, Tour, Car, Flight
            $table->morphs('favoriteable');  // favoriteable_id + favoriteable_type
            $table->timestamps();

            // منع التكرار
            $table->unique(['user_id', 'favoriteable_type', 'favoriteable_id'], 'unique_favorite');
            $table->index(['user_id', 'favoriteable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};