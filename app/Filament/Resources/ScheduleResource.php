<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Schedule;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\TravelRoute;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ScheduleResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ScheduleResource\RelationManagers;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Manajemen Travel';
    protected static ?string $label = 'Jadwal';
    protected static ?string $pluralLabel = 'Jadwal';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('travel_route_id')
                    ->relationship('travelRoute', 'destination', fn (Builder $query) => $query->orderBy('origin')->orderBy('destination'))
                    ->label('Rute Perjalanan')
                    ->getOptionLabelFromRecordUsing(fn (TravelRoute $record) => "{$record->origin} - {$record->destination}")
                    ->required()
                    ->placeholder('Pilih Rute'),
                Forms\Components\DateTimePicker::make('departure_time')
                    ->label('Waktu Keberangkatan')
                    ->required()
                    ->placeholder('Pilih Tanggal dan Waktu'),
                Forms\Components\TextInput::make('available_seats')
                    ->label('Kursi Tersedia')
                    ->numeric()
                    ->default(7)
                    ->maxValue(7)
                    ->required()
                    ->placeholder('Jumlah Kursi'),
                Forms\Components\TextInput::make('price_per_kg')
                    ->label('Harga per Kg Barang')
                    ->numeric()
                    ->inputMode('decimal')
                    ->placeholder('Masukkan Nominal')
                    ->required()
                    ->stripCharacters(',')
                    ->afterStateHydrated(function (Forms\Components\TextInput $component, $state) {
                        if ($state) {
                            $component->state(number_format($state, 0, ',', '.'));
                        }
                    })
                    ->dehydrateStateUsing(function ($state) {
                        return (float) str_replace(['Rp.', '.', ','], '', $state);
                    })
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Forms\Components\TextInput $component, $state) {
                        if ($state) {
                            $numericValue = (float) str_replace(['Rp.', '.', ','], '', $state);
                            $component->state(number_format($numericValue, 0, ',', '.'));
                        }
                    })
                    ->prefix('Rp'),

                // Field FileUpload untuk membuat jadwal baru (gambar wajib diunggah di sini)
                Forms\Components\FileUpload::make('car_layout_image')
                    ->label('Gambar Layout Mobil')
                    ->image()
                    ->disk('public')
                    ->directory('car-layouts')
                    ->nullable() // Tetap nullable di sini jika memang opsional saat buat baru
                    ->helperText('Unggah gambar denah atau layout interior mobil. Opsional.')
                    ->maxSize(2048)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->visibleOn('create') // Hanya terlihat saat membuat data baru
                    ->columnSpanFull(),

                // Placeholder untuk menampilkan gambar dan keterangan di halaman edit
                Forms\Components\Placeholder::make('car_layout_image_display')
                    ->label('Gambar Layout Mobil')
                    ->content(function (?Schedule $record) {
                        if ($record && $record->car_layout_image) {
                            $imageUrl = asset('storage/' . $record->car_layout_image);
                            return new \Illuminate\Support\HtmlString(
                                '<div class="flex flex-col items-start space-y-2">
                                    <img src="' . $imageUrl . '" class="h-20 w-20 object-cover rounded-md" alt="Car Layout Image">
                                    <a href="' . $imageUrl . '" target="_blank" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">Lihat Gambar</a>
                                    <p class="text-sm text-gray-500 mt-2"><strong>Jika ingin mengubah gambar ini, hapus rute perjalanan ini dan buat rute baru yang sama dengan gambar yang diperbarui.<br> Hati-hati menghapus rute akan menghapus data riwayat pelanggan.</strong></p>
                                </div>'
                            );
                        }
                        return 'Belum ada gambar yang diunggah untuk rute ini.';
                    })
                    ->hiddenOn('create') // Hanya terlihat saat mengedit data
                    ->visible(fn (?Schedule $record) => $record) // Tampilkan di edit, terlepas ada gambar atau tidak untuk pesan
                    ->columnSpanFull(),
            ]);
    }

    // Method mutateFormDataBeforeSave tidak lagi diperlukan untuk menghapus/mengganti gambar karena tidak ada fitur edit gambar langsung

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('travelRoute.origin')
                    ->label('Asal')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('travelRoute.destination')
                    ->label('Tujuan')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('departure_time')
                    ->label('Waktu Berangkat')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('available_seats')
                    ->label('Sisa Kursi')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_per_kg')
                    ->label('Harga/Kg Barang')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('travel_route_id')
                    ->relationship('travelRoute', 'destination')
                    ->label('Filter Rute')
                    ->options(TravelRoute::all()->mapWithKeys(fn ($route) => [$route->id => "{$route->origin} - {$route->destination}"]))
                    ->searchable()
                    ->placeholder('Semua Rute'),
                Tables\Filters\Filter::make('departure_time')
                    ->label('Filter Waktu Keberangkatan')
                    ->form([
                        Forms\Components\DatePicker::make('departure_from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('departure_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['departure_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('departure_time', '>=', $date),
                            )
                            ->when(
                                $data['departure_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('departure_time', '<=', $date),
                            );
                    })
                    ->columnSpanFull(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Ubah'),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Terpilih'),
                ])
                ->label('Tindakan Massal'),
            ])
            ->emptyStateHeading('Tidak ada jadwal ditemukan')
            ->emptyStateDescription('Buat jadwal baru untuk mulai mengelola.')
            ->defaultSort('departure_time', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedules::route('/'),
            'create' => Pages\CreateSchedule::route('/create'),
            'edit' => Pages\EditSchedule::route('/{record}/edit'),
        ];
    }

    public static function getPluralModelLabel(): string
    {
        return 'Jadwal';
    }

    public static function getModelLabel(): string
    {
        return 'Jadwal';
    }
}