<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    // --- Properti dan Method Tambahan untuk Bahasa Indonesia ---

    /**
     * Mengatur judul halaman "Buat Jadwal Baru".
     *
     * @var string|null
     */
    protected static ?string $title = 'Buat Jadwal Baru';

    /**
     * Mengatur label breadcrumb untuk halaman ini.
     *
     * @var string|null
     */
    protected static ?string $breadcrumb = 'Buat';

    /**
     * Mengatur label untuk tombol "Create" di bagian bawah form.
     *
     * @return \Filament\Actions\Action
     */
    protected function getCreateFormAction(): Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Buat'); // Mengubah teks tombol menjadi "Buat"
    }

    /**
     * Mengatur label untuk tombol "Cancel" di bagian bawah form.
     *
     * @return \Filament\Actions\Action
     */
    protected function getCancelFormAction(): Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Batal'); // Mengubah teks tombol menjadi "Batal"
    }

    // --- Akhir Properti dan Method Tambahan ---
}