<?php

namespace App\Filament\Resources\BookingResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PassengersRelationManager extends RelationManager
{
    // Mendefinisikan nama relasi di model Booking (public function passengers())
    protected static string $relationship = 'passengers';

    // Mendefinisikan form untuk membuat atau mengedit penumpang
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required() // Nama wajib diisi
                    ->maxLength(255) // Batas panjang 255 karakter
                    ->label('Nama Penumpang'), // Label di UI
                Forms\Components\TextInput::make('id_number')
                    ->maxLength(255) // Batas panjang 255 karakter
                    ->nullable() // Boleh kosong
                    ->label('Nomor Identitas (NIK/Paspor)'), // Label di UI
                Forms\Components\TextInput::make('seat_number')
                    ->numeric() // Hanya angka
                    ->maxValue(7) // Maksimal 7 (sesuai jumlah kursi mobil travel)
                    ->nullable() // Boleh kosong (kursi bisa dipilih nanti)
                    ->label('Nomor Kursi'), // Label di UI
            ]);
    }

    // Mendefinisikan tabel untuk menampilkan daftar penumpang
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name') // Menggunakan 'name' sebagai judul record
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable() // Bisa dicari
                    ->sortable() // Bisa diurutkan
                    ->label('Nama Penumpang'), // Label di UI
                Tables\Columns\TextColumn::make('id_number')
                    ->searchable() // Bisa dicari
                    ->label('Nomor Identitas'), // Label di UI
                Tables\Columns\TextColumn::make('seat_number')
                    ->sortable() // Bisa diurutkan
                    ->label('Kursi'), // Label di UI
            ])
            ->filters([
                // Filter bisa ditambahkan di sini jika diperlukan
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(), // Tombol untuk menambah penumpang baru
            ])
            ->actions([
                Tables\Actions\EditAction::make(), // Tombol untuk mengedit penumpang
                Tables\Actions\DeleteAction::make(), // Tombol untuk menghapus penumpang
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(), // Aksi massal untuk menghapus
                ]),
            ]);
    }
}
