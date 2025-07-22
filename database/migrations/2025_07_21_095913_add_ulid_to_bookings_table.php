<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Tambahkan kolom 'ulid' yang unik dan nullable dulu
            // Atau, Anda bisa membuat kolom ini wajib dan mengisi semua yang sudah ada
            $table->ulid('ulid')->unique()->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('ulid');
        });
    }
};