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
        Schema::table('bookings', function (Blueprint $table) {
            // Ubah kolom 'status' menjadi VARCHAR dengan panjang yang lebih besar, misalnya 20 atau 30
            $table->string('status', 30)->change(); // Mengubah panjang kolom yang sudah ada
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Jika ingin mengembalikan ke panjang semula saat rollback
            // Pastikan panjang ini sesuai dengan definisi sebelumnya
            $table->string('status', 20)->change(); // Sesuaikan dengan panjang asli Anda jika tahu
        });
    }
};