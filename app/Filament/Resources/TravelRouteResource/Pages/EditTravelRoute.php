<?php

namespace App\Filament\Resources\TravelRouteResource\Pages;

use App\Filament\Resources\TravelRouteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTravelRoute extends EditRecord
{
    protected static string $resource = TravelRouteResource::class;

    // Judul halaman edit
    protected static ?string $title = 'Edit Rute Travel'; // Diterjemahkan

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Hapus'), // Diterjemahkan
        ];
    }
}