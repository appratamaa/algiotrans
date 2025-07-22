<?php

use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BookingController::class, 'index'])->name('home');

// Rute POST untuk memproses formulir pencarian (akan mengalihkan)
Route::post('/search-schedules', [BookingController::class, 'searchSchedules'])->name('search.schedules');

// Rute GET untuk menampilkan hasil pencarian (INI YANG HILANG DAN SUDAH DITAMBAHKAN)
Route::get('/schedules/results', [BookingController::class, 'showSearchResults'])->name('schedules.show-results');

Route::get('/booking/detail/{schedule}', [BookingController::class, 'showBookingDetail'])->name('booking.detail');
Route::post('/booking/process', [BookingController::class, 'processBooking'])->name('booking.process');
Route::get('/booking/seats/{booking}', [BookingController::class, 'selectSeats'])->name('booking.seats'); // Akan menggunakan ULID
Route::post('/booking/save-seats/{booking}', [BookingController::class, 'saveSeats'])->name('booking.save-seats'); // Akan menggunakan ULID
Route::get('/booking/payment/{booking}', [BookingController::class, 'choosePaymentMethod'])->name('booking.payment'); // Akan menggunakan ULID
Route::post('/booking/pay/{booking}', [BookingController::class, 'processPayment'])->name('booking.pay'); // Akan menggunakan ULID
Route::get('/booking/confirmation/{booking}', [BookingController::class, 'confirmation'])->name('booking.confirmation'); // Akan menggunakan ULID

// Rute Admin Filament akan otomatis terdaftar