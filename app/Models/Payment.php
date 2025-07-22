<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // <-- Tambahkan ini
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- Tambahkan ini

class Payment extends Model
{
    use HasFactory; // <-- Tambahkan ini

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_id',       // Foreign key ke tabel bookings
        'payment_method',   // Metode pembayaran (contoh: 'Bank Transfer BCA', 'GoPay')
        'transaction_id',   // ID transaksi dari payment gateway (bisa null jika belum ada/simulasi)
        'amount',           // Jumlah pembayaran
        'payment_date',     // Tanggal dan waktu pembayaran
        'status',           // Status pembayaran ('pending', 'completed', 'failed')
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payment_date' => 'datetime', // Mengubah kolom payment_date menjadi objek Carbon
    ];


    /**
     * Get the booking that owns the payment.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}