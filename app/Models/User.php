<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\HasName; // Opsional, tapi bagus untuk admin panel
use Filament\Models\Contracts\FilamentUser; // TAMBAHKAN INI
use Filament\Panel; // TAMBAHKAN INI (untuk Filament v3+)

// Jika Anda menggunakan Spatie Roles & Permissions, uncomment baris ini:
// use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasName, FilamentUser // TAMBAHKAN FilamentUser di sini
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    // Jika Anda menggunakan Spatie Roles & Permissions, uncomment HasRoles:
    use HasFactory, Notifiable; // atau: use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Jika Anda menambahkan HasName, tambahkan method ini
    public function getFilamentName(): string
    {
        return $this->name;
    }

    // =========================================================
    // BARIS YANG DITAMBAHKAN UNTUK FILAMENTUSER
    // Ini adalah logika untuk menentukan siapa yang bisa akses panel admin
    // =========================================================
    public function canAccessPanel(Panel $panel): bool
    {
        // === PILIH SALAH SATU LOGIKA DI BAWAH INI YANG SESUAI DENGAN APLIKASI ANDA ===

        // Opsi 1: Berdasarkan Email Tertentu (sangat sederhana, untuk demo/debugging)
        // return $this->email === 'admin@algiotrans.my.id';

        // Opsi 2: Berdasarkan User ID Tertentu (misal, user pertama yang dibuat)
        // return $this->id === 1;

        // Opsi 3: Menggunakan Kolom 'is_admin' di tabel 'users' (jika Anda menambahkannya)
        // return (bool) $this->is_admin;

        // Opsi 4: Menggunakan Spatie Roles & Permissions (Paling Fleksibel & Direkomendasikan untuk produksi)
        // Anda harus sudah menginstal dan mengkonfigurasi Spatie Permission.
        // return $this->hasRole('admin'); // atau $this->hasAnyRole(['admin', 'super_admin']);

        // Opsi 5: Jika tidak ada kriteria khusus (hanya untuk debugging awal), tapi harus diubah!
        return true; // HANYA UNTUK DEBUGGING! Ini mengizinkan SEMUA user login ke admin panel!
    }
    // =========================================================
}