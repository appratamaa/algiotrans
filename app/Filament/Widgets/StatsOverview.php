<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\TravelRoute;
use App\Models\Schedule;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Pemesanan', Booking::count())
                ->description('Jumlah seluruh pemesanan')
                ->descriptionIcon('heroicon-o-ticket')
                ->color('primary'),
            Stat::make('Total Rute', TravelRoute::count())
                ->description('Jumlah rute perjalanan yang tersedia')
                ->descriptionIcon('heroicon-o-map')
                ->color('success'),
            Stat::make('Total Jadwal', Schedule::count())
                ->description('Jumlah jadwal yang aktif')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('info'),
            Stat::make('Pemesanan Pending', Booking::where('status', 'pending')->count())
                ->description('Pemesanan yang belum dibayar')
                ->descriptionIcon('heroicon-o-exclamation-circle')
                ->color('warning'),
        ];
    }
}