<?php

namespace App\Filament\Resources\BookingResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemDeliveryRelationManager extends RelationManager
{
    // Mendefinisikan nama relasi di model Booking (public function itemDelivery())
    protected static string $relationship = 'itemDelivery';

    // Mendefinisikan form untuk membuat atau mengedit detail barang kiriman
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('item_description')
                    ->required() // Deskripsi wajib diisi
                    ->maxLength(255) // Batas panjang 255 karakter
                    ->rows(3) // Tinggi textarea 3 baris
                    ->label('Deskripsi Barang'), // Label di UI
                Forms\Components\TextInput::make('weight_kg')
                    ->required() // Berat wajib diisi
                    ->numeric() // Hanya angka
                    ->suffix('kg') // Satuan 'kg' di samping input
                    ->label('Berat Barang (kg)'), // Label di UI
            ]);
    }

    // Mendefinisikan tabel untuk menampilkan daftar barang kiriman
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('item_description') // Menggunakan 'item_description' sebagai judul record
            ->columns([
                Tables\Columns\TextColumn::make('item_description')
                    ->searchable() // Bisa dicari
                    ->label('Deskripsi Barang'), // Label di UI
                Tables\Columns\TextColumn::make('weight_kg')
                    ->suffix(' kg') // Menambahkan ' kg' setelah nilai
                    ->sortable() // Bisa diurutkan
                    ->label('Berat'), // Label di UI
            ])
            ->filters([
                // Filter bisa ditambahkan di sini jika diperlukan
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(), // Tombol untuk menambah barang kiriman baru
            ])
            ->actions([
                Tables\Actions\EditAction::make(), // Tombol untuk mengedit barang kiriman
                Tables\Actions\DeleteAction::make(), // Tombol untuk menghapus barang kiriman
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(), // Aksi massal untuk menghapus
                ]),
            ]);
    }
}
