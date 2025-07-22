<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSchedules extends ListRecords
{
    protected static string $resource = ScheduleResource::class;

    protected static ?string $title = 'Daftar Jadwal'; // Ini yang Anda tambahkan sebelumnya, boleh tetap ada

    // Hapus baris ini jika sebelumnya ada:
    // protected static string $view = 'filament.resources.schedule-resources.pages.list-schedule';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Buat Jadwal Baru'), // Ini juga yang Anda tambahkan sebelumnya
        ];
    }

    // Hapus atau komentari metode ini jika sebelumnya ada
    // protected function getHeaderWidgets(): array
    // {
    //     return [
    //         // \App\Livewire\ViewCarLayoutModal::class, // Pastikan ini tidak ada lagi
    //     ];
    // }
}