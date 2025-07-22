<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str; // Import class Str untuk ULID

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'ulid', // Tambahkan 'ulid' ke fillable
        'booking_code',
        'schedule_id',
        'booking_type',
        'number_of_passengers',
        'total_weight_kg',
        'total_price',
        'customer_name',
        'customer_email',
        'customer_phone',
        'pickup_address',
        'dropoff_address',
        'status',
        // Tambahkan kolom koordinat jika Anda sudah membuatnya di migrasi lain
        // 'pickup_latitude',
        // 'pickup_longitude',
        // 'dropoff_latitude',
        // 'dropoff_longitude',
    ];

    /**
     * Metode boot() akan dijalankan saat model dimuat.
     * Kita akan menggunakan ini untuk menghasilkan ULID sebelum booking disimpan.
     */
    protected static function boot()
    {
        parent::boot();

        // Saat model 'Booking' baru dibuat, secara otomatis buat ULID
        static::creating(function ($booking) {
            $booking->ulid = (string) Str::ulid();
        });
    }

    /**
     * Mengatur kunci rute model untuk Route Model Binding.
     * Laravel akan mencari booking berdasarkan kolom 'ulid' di URL, bukan 'id'.
     */
    public function getRouteKeyName()
    {
        return 'ulid';
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function passengers()
    {
        return $this->hasMany(Passenger::class);
    }

    public function itemDelivery()
    {
        return $this->hasOne(ItemDelivery::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}