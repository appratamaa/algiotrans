<?php

namespace App\Filament\Widgets;

use App\Models\TravelRoute;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestRoutesTable extends BaseWidget
{
    protected static ?int $sort = 4; // Urutan widget
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TravelRoute::latest()->limit(5) // Ambil 5 rute terbaru
            )
            ->columns([
                Tables\Columns\TextColumn::make('origin')
                    ->label('Asal'),
                Tables\Columns\TextColumn::make('destination')
                    ->label('Tujuan'),
                Tables\Columns\TextColumn::make('price_per_person')
                    ->label('Harga per Orang')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime(),
            ]);
    }
}