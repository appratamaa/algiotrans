<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained()->onDelete('cascade');
            $table->string('booking_code')->unique();
            $table->enum('booking_type', ['passenger', 'item_delivery']);
            $table->integer('number_of_passengers')->nullable();
            $table->decimal('total_weight_kg', 8, 2)->nullable(); // Untuk pengiriman barang
            $table->decimal('total_price', 10, 2);
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->string('pickup_address')->nullable(); // Untuk penumpang atau barang
            $table->string('dropoff_address')->nullable(); // Untuk penumpang atau barang
            $table->enum('status', ['pending', 'paid', 'cancelled', 'completed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};