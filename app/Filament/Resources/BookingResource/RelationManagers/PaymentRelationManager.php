<?php

namespace App\Filament\Resources\BookingResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentRelationManager extends RelationManager
{
    // Mendefinisikan nama relasi di model Booking (public function payment())
    protected static string $relationship = 'payment';

    // Mendefinisikan form untuk membuat atau mengedit detail pembayaran
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('payment_method')
                    ->required() // Metode pembayaran wajib diisi
                    ->maxLength(255) // Batas panjang 255 karakter
                    ->label('Metode Pembayaran'), // Label di UI
                Forms\Components\TextInput::make('transaction_id')
                    ->maxLength(255) // Batas panjang 255 karakter
                    ->nullable() // Boleh kosong
                    ->label('ID Transaksi'), // Label di UI
                Forms\Components\TextInput::make('amount')
                    ->required() // Jumlah wajib diisi
                    ->numeric() // Hanya angka
                    ->prefix('Rp') // Prefix 'Rp' di depan input
                    ->label('Jumlah Pembayaran'), // Label di UI
                Forms\Components\DateTimePicker::make('payment_date')
                    ->required() // Tanggal pembayaran wajib diisi
                    ->label('Tanggal Pembayaran'), // Label di UI
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Selesai',
                        'failed' => 'Gagal',
                    ])
                    ->required() // Status wajib diisi
                    ->label('Status Pembayaran'), // Label di UI
            ]);
    }

    // Mendefinisikan tabel untuk menampilkan daftar pembayaran
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('payment_method') // Menggunakan 'payment_method' sebagai judul record
            ->columns([
                Tables\Columns\TextColumn::make('payment_method')
                    ->searchable() // Bisa dicari
                    ->label('Metode Pembayaran'), // Label di UI
                Tables\Columns\TextColumn::make('transaction_id')
                    ->searchable() // Bisa dicari
                    ->label('ID Transaksi'), // Label di UI
                Tables\Columns\TextColumn::make('amount')
                    ->money('IDR') // Format mata uang Rupiah
                    ->sortable() // Bisa diurutkan
                    ->label('Jumlah'), // Label di UI
                Tables\Columns\TextColumn::make('payment_date')
                    ->dateTime() // Format tanggal dan waktu
                    ->sortable() // Bisa diurutkan
                    ->label('Tanggal'), // Label di UI
                Tables\Columns\TextColumn::make('status')
                    ->badge() // Tampilan badge
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'failed',
                    ])
                    ->label('Status'), // Label di UI
            ])
            ->filters([
                // Filter bisa ditambahkan di sini jika diperlukan
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(), // Tombol untuk menambah pembayaran baru
            ])
            ->actions([
                Tables\Actions\EditAction::make(), // Tombol untuk mengedit pembayaran
                Tables\Actions\DeleteAction::make(), // Tombol untuk menghapus pembayaran
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(), // Aksi massal untuk menghapus
                ]),
            ]);
    }
}
