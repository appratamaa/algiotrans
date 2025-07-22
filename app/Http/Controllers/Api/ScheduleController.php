<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function getAvailableSeats(Schedule $schedule)
    {
        // Pastikan Anda menangani kasus konkurensi dengan baik di produksi (contoh: database transaction)
        return response()->json([
            'available_seats' => $schedule->available_seats,
        ]);
    }
}