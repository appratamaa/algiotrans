<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // <-- Tambahkan ini
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- Tambahkan ini

class Passenger extends Model
{
    use HasFactory; // <-- Tambahkan ini

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_id',  // ID dari booking yang terkait
        'name',        // Nama penumpang
        'id_number',   // Nomor identitas penumpang (opsional)
        'seat_number', // Nomor kursi yang dipilih
    ];

    /**
     * Get the booking that owns the passenger.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}