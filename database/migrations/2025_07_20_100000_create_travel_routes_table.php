<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('travel_routes', function (Blueprint $table) {
            $table->id();
            $table->string('origin');
            $table->string('destination');
            $table->decimal('price_per_person', 10, 2)->nullable(); // Harga per orang untuk penumpang
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_routes');
    }
};