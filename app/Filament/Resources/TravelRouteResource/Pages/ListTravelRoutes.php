<?php

namespace App\Filament\Resources\TravelRouteResource\Pages;

use App\Filament\Resources\TravelRouteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTravelRoutes extends ListRecords
{
    protected static string $resource = TravelRouteResource::class;

    // Judul halaman daftar
    protected static ?string $title = 'Daftar Rute Travel'; // Diterjemahkan

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Buat Rute Travel Baru'), // Diterjemahkan
        ];
    }
}