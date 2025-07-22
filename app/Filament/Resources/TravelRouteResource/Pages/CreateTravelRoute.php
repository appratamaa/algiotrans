<?php

namespace App\Filament\Resources\TravelRouteResource\Pages;

use App\Filament\Resources\TravelRouteResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTravelRoute extends CreateRecord
{
    protected static string $resource = TravelRouteResource::class;

    // Judul halaman buat baru
    protected static ?string $title = 'Buat Rute Travel Baru'; // Diterjemahkan
}