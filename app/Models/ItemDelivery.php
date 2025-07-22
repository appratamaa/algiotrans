<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemDelivery extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_id',
        'item_description',
        'weight_kg',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'weight_kg' => 'decimal:2', // Mengonversi weight_kg menjadi desimal dengan 2 angka di belakang koma
    ];

    /**
     * Get the booking that owns the item delivery record.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}