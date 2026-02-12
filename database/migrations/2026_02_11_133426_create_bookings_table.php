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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('category', ['tour', 'hotel', 'fligth', 'car']);
            $table->unsignedBigInteger('item_id');

            $table->enum('status', [
                'pending',
                'confirmed',
                'cancelled',
                'expired'
            ])->default('pending');

            $table->enum('payment_status', [
                'unpaid',
                'paid',
                'failed'
            ])->default('unpaid');

            $table->decimal('total_price', 10, 2);

            $table->timestamp('expires_at')->nullable();

            $table->timestamps();

            $table->index(['category', 'item_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
