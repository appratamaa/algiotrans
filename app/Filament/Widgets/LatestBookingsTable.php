<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestBookingsTable extends BaseWidget
{
    protected static ?int $sort = 2; // Urutan widget di dashboard
    protected int | string | array $columnSpan = 'full'; // Mengambil seluruh lebar

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Booking::latest()->limit(5) // Ambil 5 booking terbaru
            )
            ->columns([
                Tables\Columns\TextColumn::make('booking_code')
                    ->label('Kode Booking'),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Nama Pelanggan'),
                Tables\Columns\TextColumn::make('schedule.travelRoute.origin')
                    ->label('Asal'),
                Tables\Columns\TextColumn::make('schedule.travelRoute.destination')
                    ->label('Tujuan'),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total Harga')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'cancelled',
                        'info' => 'completed',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime(),
            ]);
    }
}