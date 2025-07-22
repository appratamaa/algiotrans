<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_route_id')->constrained()->onDelete('cascade');
            $table->dateTime('departure_time');
            $table->integer('available_seats')->default(7); // Total kursi di mobil travel
            $table->decimal('price_per_kg', 10, 2)->nullable(); // Harga per kg untuk pengiriman barang
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};