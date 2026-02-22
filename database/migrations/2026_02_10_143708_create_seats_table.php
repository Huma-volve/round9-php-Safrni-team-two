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
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aircraft_id')->constrained('aircrafts')->cascadeOnDelete();

            $table->integer('row_number')->nullable();
            $table->string('column_letter', 1)->nullable();

            $table->enum('class_type', ['economy', 'business', 'first'])->default('economy');
            $table->enum('seat_position', ['window', 'middle', 'aisle'])->default('middle');

            $table->enum('status', ['active', 'blocked'])->default('active');

            $table->timestamps();

            $table->unique(['aircraft_id', 'row_number', 'column_letter']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
