<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_route_id',
        'departure_time',
        'available_seats',
        'price_per_kg',
        'car_layout_image', // <<< PASTIKAN BARIS INI ADA!
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        // Jika Anda ingin secara eksplisit menentukan tipe string untuk 'car_layout_image',
        // Anda bisa menambahkannya di sini, tapi umumnya tidak wajib untuk string.
        // 'car_layout_image' => 'string',
    ];

    /**
     * Get the travel route that owns the schedule.
     */
    public function travelRoute(): BelongsTo
    {
        return $this->belongsTo(TravelRoute::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}