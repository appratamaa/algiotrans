<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // <-- Tambahkan ini
use Illuminate\Database\Eloquent\Model;

class TravelRoute extends Model
{
    use HasFactory; // <-- Tambahkan ini

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'origin',           // Kolom untuk kota asal
        'destination',      // Kolom untuk kota tujuan
        'price_per_person', // Kolom untuk harga per orang di rute ini
    ];

    /**
     * Get the schedules for the travel route.
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
