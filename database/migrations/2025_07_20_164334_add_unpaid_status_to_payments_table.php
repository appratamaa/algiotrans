<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Don't forget this if you use DB::statement

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // For ENUM, you usually drop and re-add or use raw SQL
            // Simpler way if you have few existing records or can reset:
            $table->enum('status', ['pending', 'unpaid', 'completed', 'failed'])->change();

            // OR for raw SQL (if 'unpaid' needs to be inserted at a specific spot):
            // DB::statement("ALTER TABLE payments CHANGE COLUMN status status ENUM('pending', 'unpaid', 'completed', 'failed') NOT NULL DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Revert to the old ENUM values if needed
            $table->enum('status', ['pending', 'completed', 'failed'])->change();

            // OR
            // DB::statement("ALTER TABLE payments CHANGE COLUMN status status ENUM('pending', 'completed', 'failed') NOT NULL DEFAULT 'pending'");
        });
    }
};