<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Booking;
use App\Models\Schedule;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Tabs;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BookingResource\Pages;
use App\Models\Passenger; // Pastikan ini diimport

// Pastikan import ini ada dan benar
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Http; // Pastikan ini diimport
use App\Filament\Resources\BookingResource\RelationManagers\PaymentRelationManager;

use App\Filament\Resources\BookingResource\RelationManagers\PassengersRelationManager;
use App\Filament\Resources\BookingResource\RelationManagers\ItemDeliveryRelationManager;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Manajemen Pesanan'; // Diterjemahkan

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('schedule_id')
                    ->label('Jadwal Perjalanan') // Diterjemahkan
                    ->relationship('schedule', 'id')
                    ->getOptionLabelFromRecordUsing(fn(Schedule $record) => "{$record->travelRoute->origin} - {$record->travelRoute->destination} pada {$record->departure_time->format('d M Y H:i')}")
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('booking_code')
                    ->label('Kode Pesanan') // Diterjemahkan
                    ->readOnly()
                    ->maxLength(255),
                Forms\Components\Select::make('booking_type')
                    ->label('Tipe Pesanan') // Diterjemahkan
                    ->options([
                        'passenger' => 'Penumpang',
                        'item_delivery' => 'Pengiriman Barang',
                    ])
                    ->required()
                    ->disabled(),
                Forms\Components\TextInput::make('customer_name')
                    ->label('Nama Pelanggan') // Diterjemahkan
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('customer_email')
                    ->label('Email Pelanggan') // Diterjemahkan
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('customer_phone')
                    ->label('Telepon Pelanggan') // Diterjemahkan
                    ->tel()
                    ->required()
                    ->maxLength(20),
                Forms\Components\Textarea::make('pickup_address')
                    ->label('Alamat Penjemputan') // Diterjemahkan
                    ->rows(2)
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('dropoff_address')
                    ->label('Alamat Pengantaran') // Diterjemahkan
                    ->rows(2)
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->label('Status Pesanan') // Diterjemahkan
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Dibayar',
                        'checked_in' => 'Check-in', // Status baru
                        'rejected' => 'Ditolak', // Status baru
                        'cancelled' => 'Dibatalkan',
                        'completed' => 'Selesai',
                    ])
                    ->required(),

                Tabs::make('Detail Pesanan') // Diterjemahkan
                    ->tabs([
                        Tabs\Tab::make('Detail Penumpang') // Diterjemahkan
                            ->schema([
                                Forms\Components\Repeater::make('passengers')
                                    ->label('Penumpang') // Diterjemahkan
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nama') // Diterjemahkan
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('id_number')
                                            ->label('Nomor Identitas') // Diterjemahkan
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('seat_number')
                                            ->label('Nomor Kursi')
                                            ->numeric()
                                            ->nullable()
                                            ->maxValue(7),
                                    ])
                                    ->visible(fn(Forms\Get $get) => $get('booking_type') === 'passenger')
                                    ->columns(3),
                                Forms\Components\TextInput::make('number_of_passengers')
                                    ->label('Jumlah Penumpang') // Diterjemahkan
                                    ->numeric()
                                    ->readOnly()
                                    ->visible(fn(Forms\Get $get) => $get('booking_type') === 'passenger'),
                            ]),
                        Tabs\Tab::make('Detail Barang Kiriman') // Diterjemahkan
                            ->schema([
                                Forms\Components\TextInput::make('itemDelivery.item_description')
                                    ->label('Deskripsi Barang') // Diterjemahkan
                                    ->maxLength(255)
                                    ->visible(fn(Forms\Get $get) => $get('booking_type') === 'item_delivery'),
                                Forms\Components\TextInput::make('itemDelivery.weight_kg')
                                    ->label('Berat (kg)') // Diterjemahkan
                                    ->numeric()
                                    ->visible(fn(Forms\Get $get) => $get('booking_type') === 'item_delivery'),
                                Forms\Components\TextInput::make('total_weight_kg')
                                    ->label('Total Berat (kg)') // Diterjemahkan
                                    ->numeric()
                                    ->readOnly()
                                    ->visible(fn(Forms\Get $get) => $get('booking_type') === 'item_delivery'),
                            ]),
                        Tabs\Tab::make('Detail Pembayaran') // Diterjemahkan
                            ->schema([
                                Forms\Components\TextInput::make('total_price')
                                    ->label('Total Harga') // Diterjemahkan
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->readOnly(),
                                Forms\Components\TextInput::make('payment.payment_method')
                                    ->label('Metode Pembayaran') // Diterjemahkan
                                    ->readOnly(),
                                Forms\Components\TextInput::make('payment.transaction_id')
                                    ->label('ID Transaksi') // Diterjemahkan
                                    ->readOnly(),
                                Forms\Components\Select::make('payment.status')
                                    ->options([
                                        'unpaid' => 'Belum Dibayar',
                                        'pending' => 'Menunggu Pembayaran',
                                        'completed' => 'Selesai',
                                        'failed' => 'Gagal',
                                    ])
                                    ->label('Status Pembayaran') // Diterjemahkan
                                    ->disabled(),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // Urutkan data customer (booking) dari yang terbaru ke lama
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('booking_code')
                    ->label('Kode Pesanan') // Diterjemahkan
                    ->searchable(),
                Tables\Columns\TextColumn::make('booking_type')
                    ->label('Tipe Pesanan') // Diterjemahkan
                    ->formatStateUsing(fn(string $state): string => ucfirst(str_replace('_', ' ', $state)))
                    ->badge()
                    ->colors([
                        'primary' => 'passenger',
                        'success' => 'item_delivery',
                    ]),
                Tables\Columns\TextColumn::make('schedule.travelRoute.origin')
                    ->label('Asal') // Diterjemahkan
                    ->sortable(),
                Tables\Columns\TextColumn::make('schedule.travelRoute.destination')
                    ->label('Tujuan') // Diterjemahkan
                    ->sortable(),
                Tables\Columns\TextColumn::make('schedule.departure_time')
                    ->label('Waktu Berangkat') // Diterjemahkan
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Nama Pelanggan') // Diterjemahkan
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total Harga') // Diterjemahkan
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status Pesanan') // Diterjemahkan
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'primary' => 'checked_in', // Warna untuk status baru
                        'danger' => 'cancelled',
                        'gray' => 'rejected', // Warna untuk status baru
                        'info' => 'completed',
                    ]),
                Tables\Columns\TextColumn::make('payment.status')
                    ->label('Status Bayar') // Diterjemahkan
                    ->badge()
                    ->colors([
                        'info' => 'unpaid',
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'failed',
                    ])
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
                Tables\Filters\SelectFilter::make('booking_type')
                    ->label('Tipe Pesanan') // Diterjemahkan
                    ->options([
                        'passenger' => 'Penumpang',
                        'item_delivery' => 'Pengiriman Barang',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Pesanan') // Diterjemahkan
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Dibayar',
                        'checked_in' => 'Check-in', // Tambahkan opsi baru
                        'rejected' => 'Ditolak', // Tambahkan opsi baru
                        'cancelled' => 'Dibatalkan',
                        'completed' => 'Selesai',
                    ]),
               
                Tables\Filters\SelectFilter::make('schedule_id')
                    ->label('Jadwal') // Diterjemahkan
                    ->relationship('schedule', 'id')
                    ->getOptionLabelFromRecordUsing(fn(Schedule $record) => "{$record->travelRoute->origin} - {$record->travelRoute->destination} pada {$record->departure_time->format('d M Y H:i')}"),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat'), // Diterjemahkan
                Tables\Actions\EditAction::make()
                    ->label('Edit'), // Diterjemahkan

                // --- Aksi Check-in ---
                Tables\Actions\Action::make('checkIn')
                    ->label('Check-in')
                    ->icon('heroicon-o-check-circle')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Check-in')
                    ->modalDescription('Apakah Anda yakin ingin melakukan check-in untuk pesanan ini? Status pesanan akan diubah menjadi "Check-in" dan detail akan dikirim ke driver.')
                    ->visible(function (Booking $record) {
                        return $record->status !== 'checked_in';
                    })
                    ->action(function (Booking $record) {
                        try {
                            // Update status booking
                            $record->update(['status' => 'checked_in']);

                            // Send WhatsApp notification to driver
                            static::sendWhatsAppDriverNotification($record);

                            Notification::make()
                                ->title('Check-in Berhasil!')
                                ->body("Pesanan {$record->booking_code} berhasil di-check-in. Detail telah dikirim ke driver.")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Terjadi Kesalahan')
                                ->body("Gagal check-in untuk pesanan {$record->booking_code}: " . $e->getMessage())
                                ->danger()
                                ->send();
                            Log::error("Failed to check-in booking: " . $e->getMessage(), ['booking_id' => $record->id]);
                        }
                    }),

                // --- Aksi Tolak ---
                Tables\Actions\Action::make('rejectBooking')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Penolakan Pesanan')
                    ->modalDescription('Apakah Anda yakin ingin MENOLAK pesanan ini? Status pesanan akan diubah menjadi "Ditolak".')
                    ->visible(function (Booking $record) {
                        return !in_array($record->status, ['checked_in', 'completed', 'cancelled', 'rejected']);
                    })
                    ->action(function (Booking $record) {
                        try {
                            // Only decrement seats if the booking was previously 'paid' and is now being rejected.
                            // This prevents double decrementing if a booking is rejected, then paid, then rejected again.
                            if ($record->booking_type === 'passenger' && $record->number_of_passengers > 0 && $record->status === 'paid') {
                                $schedule = $record->schedule;
                                if ($schedule) {
                                    $schedule->increment('available_seats', $record->number_of_passengers);
                                    Notification::make()
                                        ->title('Kursi Dikembalikan!')
                                        ->body("Jumlah kursi untuk jadwal {$schedule->id} telah dikembalikan sebanyak {$record->number_of_passengers} kursi karena pesanan ditolak.")
                                        ->success()
                                        ->send();
                                }
                            }

                            $record->update(['status' => 'rejected']);
                            Notification::make()
                                ->title('Pesanan Ditolak!')
                                ->body("Pesanan {$record->booking_code} berhasil ditolak.")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Terjadi Kesalahan')
                                ->body("Gagal menolak pesanan {$record->booking_code}: " . $e->getMessage())
                                ->danger()
                                ->send();
                            Log::error("Failed to reject booking: " . $e->getMessage(), ['booking_id' => $record->id]);
                        }
                    }),

                // === Aksi untuk mengubah status pembayaran manual ===
                Tables\Actions\Action::make('markAsPaid')
                    ->label('Tandai Lunas')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pembayaran Lunas')
                    ->modalDescription('Apakah Anda yakin ingin menandai pemesanan ini sebagai LUNAS? Status pembayaran akan diperbarui dan kursi akan dikurangi (jika pesanan penumpang).')
                    ->visible(function (Booking $record) {
                        return $record->payment && $record->payment->status !== 'completed';
                    })
                    ->action(function (Booking $record) {
                        try {
                            DB::transaction(function () use ($record) {
                                if ($record->booking_type === 'passenger') {
                                    $schedule = Schedule::find($record->schedule_id);

                                    if (!$schedule) {
                                        throw new \Exception('Jadwal tidak ditemukan.');
                                    }

                                    // Get seats for this booking
                                    $selectedSeatsForThisBooking = $record->passengers->pluck('seat_number')->filter()->toArray();

                                    // Get currently occupied seats for this schedule from *other* paid/checked_in bookings
                                    $occupiedSeats = Passenger::whereHas('booking', function ($query) use ($schedule, $record) {
                                        $query->where('schedule_id', $schedule->id)
                                              ->whereIn('status', ['paid', 'checked_in'])
                                              ->where('id', '!=', $record->id); // Exclude current booking
                                    })->whereNotNull('seat_number')->pluck('seat_number')->toArray();

                                    // Check for conflicts
                                    $conflictingSeats = array_intersect($selectedSeatsForThisBooking, $occupiedSeats);

                                    if (!empty($conflictingSeats)) {
                                        // Send WhatsApp notification to customer about unavailable seats
                                        static::sendWhatsAppCustomerNotification($record->customer_phone, $record->customer_name, $record->booking_code, $conflictingSeats);
                                        throw new \Exception('Beberapa kursi yang dipilih (' . implode(', ', $conflictingSeats) . ') sudah tidak tersedia. Notifikasi ke pelanggan telah dikirimkan.');
                                    }

                                    // Double check total available seats just in case
                                    if ($schedule->available_seats < $record->number_of_passengers) {
                                        static::sendWhatsAppCustomerNotification($record->customer_phone, $record->customer_name, $record->booking_code, []); // Send general not available
                                        throw new \Exception('Jumlah kursi yang diperlukan tidak lagi tersedia. Notifikasi ke pelanggan telah dikirimkan.');
                                    }

                                    // Decrement seats if everything is fine and not already marked paid
                                    if ($record->status !== 'paid' && $record->status !== 'checked_in') {
                                        $schedule->decrement('available_seats', $record->number_of_passengers);
                                    }
                                }

                                // Update payment and booking status
                                $record->payment->update(['status' => 'completed']);
                                $record->update(['status' => 'paid']);
                            });

                            // Send invoice after transaction is successful
                            static::sendInvoiceWhatsApp($record);

                            Notification::make()
                                ->title('Pembayaran Dikonfirmasi!')
                                ->body("Pesanan {$record->booking_code} berhasil ditandai lunas dan diverifikasi. Invoice telah dikirim.")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Log::error("Failed to mark booking as paid: " . $e->getMessage(), ['booking_id' => $record->id]);
                            Notification::make()
                                ->title('Terjadi Kesalahan')
                                ->body("Gagal menandai lunas untuk pesanan {$record->booking_code}: " . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
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
            BookingResource\RelationManagers\PassengersRelationManager::class,
            BookingResource\RelationManagers\ItemDeliveryRelationManager::class,
            BookingResource\RelationManagers\PaymentRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
            'view' => Pages\ViewBooking::route('/{record}'),
        ];
    }

    // Tambahkan metode ini untuk terjemahan global label model
    public static function getModelLabel(): string
    {
        return 'Pesanan'; // Label tunggal
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pesanan'; // Label jamak
    }

    public static function getNavigationLabel(): string
    {
        return 'Pesanan'; // Label di navigasi sidebar
    }

    /**
     * Mengirim pesan tunggal via Ultramsg API.
     * @param string $to Nomor telepon tujuan (tanpa +, dengan kode negara)
     * @param string $message Isi pesan
     */
    protected static function sendWhatsAppMessage($phone, $message)
    {
        $url = env('ULTRAMSG_BASE_URL') . '/messages/chat';

        $response = Http::asForm()->post($url, [
            'token' => env('ULTRAMSG_API_TOKEN'),
            'to' => $phone,
            'body' => $message,
            'priority' => 10,
            'referenceId' => 'booking-' . Str::random(6),
        ]);

        Log::info('WhatsApp Response from Admin to ' . $phone . ': ' . $response->body());

        return $response->successful();
    }

    /**
     * Mengirim notifikasi WhatsApp ke customer jika kursi tidak tersedia.
     * @param string $customerPhone
     * @param string $customerName
     * @param string $bookingCode
     * @param array $conflictingSeats
     */
    protected static function sendWhatsAppCustomerNotification($customerPhone, $customerName, $bookingCode, array $conflictingSeats)
    {
        $adminNumber = env('ADMIN_WHATSAPP_NUMBER');
        $message = "Halo {$customerName},\n\n";
        $message .= "Mohon maaf, pembayaran Anda untuk Kode Booking: *{$bookingCode}* tidak dapat kami proses saat ini.\n";

        if (!empty($conflictingSeats)) {
            $message .= "Hal ini dikarenakan kursi nomor *" . implode(', ', $conflictingSeats) . "* yang Anda pilih sudah tidak tersedia (telah dipesan oleh orang lain).\n";
        } else {
            $message .= "Jumlah kursi yang Anda butuhkan tidak lagi tersedia.\n";
        }
        $message .= "Mohon hubungi admin kami di WhatsApp *{$adminNumber}* untuk bantuan lebih lanjut (misalnya: memilih kursi lain, membatalkan pesanan, atau pengembalian dana).\n\n";
        $message .= "Terima kasih atas pengertiannya.";

        static::sendWhatsAppMessage($customerPhone, $message);
        Log::warning("WhatsApp notification sent to customer about unavailable seats for booking {$bookingCode}.");
    }

    /**
     * Mengirim invoice via WhatsApp ke customer dan admin.
     * @param \App\Models\Booking $booking
     */
    protected static function sendInvoiceWhatsApp(Booking $booking)
    {
        $instanceId = env('ULTRAMSG_INSTANCE_ID');
        $token = env('ULTRAMSG_API_TOKEN');
        $baseUrl = env('ULTRAMSG_BASE_URL');
        $adminNumber = env('ADMIN_WHATSAPP_NUMBER');

        if (!$instanceId || !$token || !$baseUrl || !$adminNumber) {
            Log::error('ULTRAMSG API credentials or ADMIN_WHATSAPP_NUMBER not set for invoice.');
            return;
        }

        // Generate booking confirmation link using ULID
        $confirmationLink = route('booking.confirmation', ['booking' => $booking->ulid]);

        // --- Format Pesan Invoice untuk Customer ---
        $invoiceCustomerMessage = "*INVOICE PEMBAYARAN - Algio Trans*\n\n";
        $invoiceCustomerMessage .= "Halo {$booking->customer_name},\n";
        $invoiceCustomerMessage .= "Pembayaran Anda untuk Kode Booking: *{$booking->booking_code}* telah berhasil dikonfirmasi dan status pesanan LUNAS.\n\n";
        $invoiceCustomerMessage .= "*Detail Pemesanan:*\n";
        $invoiceCustomerMessage .= "Kode Booking: *{$booking->booking_code}*\n";
        $invoiceCustomerMessage .= "Jadwal: " . $booking->schedule->departure_time->translatedFormat('l, d F Y') . " pukul " . $booking->schedule->departure_time->format('H:i') . " WIB\n";
        $invoiceCustomerMessage .= "Rute: " . $booking->schedule->travelRoute->origin . " → " . $booking->schedule->travelRoute->destination . "\n";
        $invoiceCustomerMessage .= "Total Harga: *Rp " . number_format($booking->total_price, 0, ',', '.') . "*\n";
        $invoiceCustomerMessage .= "Status Pembayaran: *LUNAS*\n\n";

        if ($booking->booking_type === 'passenger') {
            $invoiceCustomerMessage .= "*Detail Penumpang:*\n";
            foreach ($booking->passengers as $index => $p) {
                $invoiceCustomerMessage .= ($index + 1) . ". {$p->name}";
                if ($p->seat_number) {
                    $invoiceCustomerMessage .= " (Kursi: {$p->seat_number})";
                }
                $invoiceCustomerMessage .= "\n";
            }
        } else {
            $item = $booking->itemDelivery;
            $invoiceCustomerMessage .= "*Detail Barang:*\n";
            $invoiceCustomerMessage .= "Deskripsi: {$item->item_description}\n";
            $invoiceCustomerMessage .= "Berat: {$item->weight_kg} kg\n";
        }

        $invoiceCustomerMessage .= "\nAnda dapat mengunduh tiket Anda di sini: {$confirmationLink}\n"; // Added link
        $invoiceCustomerMessage .= "\nTerima kasih telah menggunakan layanan Algio Trans. Sampai jumpa di perjalanan Anda!";
        // Kirim ke customer
        static::sendWhatsAppMessage($booking->customer_phone, $invoiceCustomerMessage);


        // --- Format Pesan Invoice untuk Admin ---
        $invoiceAdminMessage = "*INVOICE DITERBITKAN - Algio Trans*\n\n";
        $invoiceAdminMessage .= "Pembayaran untuk Kode Booking: *{$booking->booking_code}* telah *LUNAS*.\n\n";
        $invoiceAdminMessage .= "*Detail Pemesanan:*\n";
        $invoiceAdminMessage .= "Nama Pelanggan: {$booking->customer_name}\n";
        $invoiceAdminMessage .= "Telepon Pelanggan: {$booking->customer_phone}\n";
        $invoiceAdminMessage .= "Jadwal: " . $booking->schedule->departure_time->format('d M Y H:i') . "\n";
        $invoiceAdminMessage .= "Rute: " . $booking->schedule->travelRoute->origin . " → " . $booking->schedule->travelRoute->destination . "\n";
        $invoiceAdminMessage .= "Total Harga: Rp " . number_format($booking->total_price, 0, ',', '.') . "\n";
        $invoiceAdminMessage .= "Status Pembayaran: *LUNAS*\n";

        if ($booking->booking_type === 'passenger') {
            $invoiceAdminMessage .= "Kursi yang dialokasikan: ";
            $seats = $booking->passengers->pluck('seat_number')->filter()->implode(', ');
            $invoiceAdminMessage .= ($seats ?: 'Belum memilih kursi') . "\n";
            // Get the schedule again to ensure freshest available_seats count
            $freshSchedule = Schedule::find($booking->schedule_id);
            if ($freshSchedule) {
                $invoiceAdminMessage .= "Kursi tersedia tersisa untuk jadwal ini: " . $freshSchedule->available_seats . "\n";
            }
        } else {
             $itemDescription = $booking->itemDelivery ? $booking->itemDelivery->item_description : '';
             $itemWeight = $booking->itemDelivery ? $booking->itemDelivery->weight_kg : '';
             $invoiceAdminMessage .= "Detail Barang: {$itemDescription} ({$itemWeight} kg)\n";
        }
        $invoiceAdminMessage .= "\nLink Konfirmasi Customer: {$confirmationLink}\n"; // Added link for admin too

        // Kirim ke admin
        static::sendWhatsAppMessage($adminNumber, $invoiceAdminMessage);
    }

    /**
     * Mengirim notifikasi WhatsApp ke driver.
     * @param \App\Models\Booking $booking
     */
    protected static function sendWhatsAppDriverNotification(Booking $booking)
    {
        $driverNumber = env('DRIVER_WHATSAPP_NUMBER');

        if (!$driverNumber) {
            Log::error('DRIVER_WHATSAPP_NUMBER not set.');
            return;
        }

        // Format alamat untuk Google Maps Link
        // Gunakan str_replace untuk mengganti spasi dengan + untuk format URL yang lebih baik
        $pickupAddressFormatted = str_replace(' ', '+', $booking->pickup_address);
        $dropoffAddressFormatted = str_replace(' ', '+', $booking->dropoff_address);

        $pickupAddressLink = "https://www.google.com/maps/search/?api=1&query=" . $pickupAddressFormatted;
        $dropoffAddressLink = "https://www.google.com/maps/search/?api=1&query=" . $dropoffAddressFormatted;


        // Nomor telepon customer untuk link WhatsApp (format 62xxxxxxxxxx)
        $customerPhoneForWhatsAppLink = preg_replace('/^08/', '628', $booking->customer_phone);
        // Jika nomor sudah diawali +62 atau 62, biarkan saja
        if (!preg_match('/^(62|\\+62)/', $customerPhoneForWhatsAppLink)) {
            $customerPhoneForWhatsAppLink = '62' . ltrim($customerPhoneForWhatsAppLink, '0');
        }

        $message = "*CUSTOMER CHECK-IN - JANGAN DIBALAS SECARA OTOMATIS*\n\n";
        $message .= "Hallo Driver Algio Trans!\n";
        $message .= "Ada pesanan baru yang siap dijemput:\n\n";
        $message .= "*Kode Booking:* {$booking->booking_code}\n";
        $message .= "*Nama Pelanggan:* {$booking->customer_name}\n";
        $message .= "*Telepon Pelanggan:* https://wa.me/{$customerPhoneForWhatsAppLink}\n";
        $message .= "*Tipe Booking:* " . ($booking->booking_type === 'passenger' ? 'Penumpang' : 'Pengiriman Barang') . "\n";
        $message .= "*Jadwal:* " . $booking->schedule->departure_time->translatedFormat('l, d F Y') . " pukul " . $booking->schedule->departure_time->format('H:i') . " WIB\n";
        $message .= "*Rute:* {$booking->schedule->travelRoute->origin} → {$booking->schedule->travelRoute->destination}\n";
        
        if ($booking->booking_type === 'passenger') {
            $seats = $booking->passengers->pluck('seat_number')->filter()->implode(', ');
            $message .= "*Kursi Terpilih:* " . ($seats ?: 'Belum memilih kursi') . "\n";
            $message .= "*Jumlah Penumpang:* {$booking->number_of_passengers} Orang\n";
        } else {
            $item = $booking->itemDelivery;
            $message .= "*Deskripsi Barang:* {$item->item_description}\n";
            $message .= "*Berat Barang:* {$item->weight_kg} kg\n";
        }
        
        $message .= "*Alamat Penjemputan:* {$pickupAddressLink}\n";
        $message .= "*Alamat Tujuan:* {$dropoffAddressLink}\n\n";
        $message .= "Mohon koordinasikan dengan pelanggan untuk penjemputan.\n";
        $message .= "_Ini adalah pesan otomatis, mohon tidak dibalas._";

        static::sendWhatsAppMessage($driverNumber, $message);
        Log::info("WhatsApp notification sent to driver for booking {$booking->booking_code}.");
    }
}