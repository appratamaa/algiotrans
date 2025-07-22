<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBooking extends ViewRecord
{
    protected static string $resource = BookingResource::class;

    // Ini akan menampilkan header "Lihat Pesanan" karena sudah disetel di BookingResource
    protected static ?string $title = 'Detail Pesanan'; // Opsional: Judul spesifik untuk halaman ini

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Edit'), // Label tombol "Edit" di header
        ];
    }
}