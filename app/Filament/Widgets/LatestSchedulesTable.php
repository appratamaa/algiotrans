<?php

namespace App\Filament\Widgets;

use App\Models\Schedule;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Carbon\Carbon; // Pastikan Carbon diimport

class LatestSchedulesTable extends BaseWidget
{
    protected static ?int $sort = 3; // Urutan widget
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Schedule::where('departure_time', '>=', Carbon::now()) // Jadwal yang akan datang
                    ->orderBy('departure_time', 'asc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('travelRoute.origin')
                    ->label('Asal'),
                Tables\Columns\TextColumn::make('travelRoute.destination')
                    ->label('Tujuan'),
                Tables\Columns\TextColumn::make('departure_time')
                    ->label('Waktu Berangkat')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('available_seats')
                    ->label('Kursi Tersedia'),
                Tables\Columns\TextColumn::make('price_per_kg')
                    ->label('Harga per Kg')
                    ->money('IDR'),
            ]);
    }
}