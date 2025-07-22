<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TravelRouteResource\Pages;
use App\Filament\Resources\TravelRouteResource\RelationManagers;
use App\Models\TravelRoute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TravelRouteResource extends Resource
{
    protected static ?string $model = TravelRoute::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationGroup = 'Manajemen Travel'; // Diterjemahkan

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('origin')
                    ->label('Asal') // Diterjemahkan
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('destination')
                    ->label('Tujuan') // Diterjemahkan
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price_per_person')
                    ->label('Harga per Orang (Rp)') // Diterjemahkan
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('origin')
                    ->label('Asal') // Diterjemahkan
                    ->searchable(),
                Tables\Columns\TextColumn::make('destination')
                    ->label('Tujuan') // Diterjemahkan
                    ->searchable(),
                Tables\Columns\TextColumn::make('price_per_person')
                    ->label('Harga per Orang') // Diterjemahkan
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada') // Diterjemahkan
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada') // Diterjemahkan
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filter tidak ada, jadi tidak ada yang perlu diterjemahkan
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit'), // Diterjemahkan
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus'), // Diterjemahkan
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Terpilih'), // Diterjemahkan
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Tidak ada relasi, jadi tidak ada yang perlu diterjemahkan
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTravelRoutes::route('/'),
            'create' => Pages\CreateTravelRoute::route('/create'),
            'edit' => Pages\EditTravelRoute::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return 'Rute Travel'; // Diterjemahkan: Label tunggal untuk model
    }

    public static function getPluralModelLabel(): string
    {
        return 'Rute Travel'; // Diterjemahkan: Label jamak untuk model
    }

    public static function getNavigationLabel(): string
    {
        return 'Rute Travel'; // Diterjemahkan: Label di navigasi sidebar
    }
}