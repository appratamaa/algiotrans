<?php // <<< PASTIKAN INI DI BARIS PERTAMA FILE API.PHP

use App\Http\Controllers\Api\ScheduleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route untuk mendapatkan sisa kursi jadwal
Route::get('/schedule-seats/{schedule}', [ScheduleController::class, 'getAvailableSeats']);

// Anda mungkin punya route lain di sini, seperti:
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });