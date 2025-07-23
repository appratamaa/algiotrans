<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Schedule;
use App\Models\Passenger;
use App\Models\TravelRoute;
use Illuminate\Support\Str;
use App\Models\ItemDelivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class BookingController extends Controller
{
    public function index()
    {
        $routes = TravelRoute::all();
        return view('welcome', compact('routes'));
    }

    /**
     * Memproses permintaan pencarian jadwal (POST).
     * Menerima input dari formulir dan mengalihkan ke rute GET baru.
     */
    public function searchSchedules(Request $request)
    {
        $validatedData = $request->validate([
            'booking_type' => 'required|in:passenger,item_delivery',
            'route_id' => 'required|exists:travel_routes,id',
            'departure_date' => 'required|date',
            'num_passengers' => 'nullable|integer|min:1',
            'total_weight_kg' => 'nullable|numeric|min:0.1',
        ]);

        return redirect()->route('schedules.show-results', $validatedData);
    }

    /**
     * Menampilkan hasil pencarian jadwal (GET).
     * Menerima parameter dari URL query string setelah pengalihan.
     */
    public function showSearchResults(Request $request)
    {
        $route = TravelRoute::findOrFail($request->route_id);
        $departureDate = Carbon::parse($request->departure_date)->startOfDay();

        $schedules = Schedule::where('travel_route_id', $route->id)
            ->whereDate('departure_time', $departureDate)
            ->when($request->booking_type === 'passenger', function ($query) {
                return $query->where('available_seats', '>', 0);
            })
            ->with('travelRoute')
            ->get();

        foreach ($schedules as $schedule) {
            if ($request->booking_type === 'passenger') {
                $schedule->estimated_total_price = $route->price_per_person * $request->num_passengers;
            } else {
                $schedule->estimated_total_price = $schedule->price_per_kg * $request->total_weight_kg;
            }
        }

        return view('search_results', compact('schedules', 'request', 'route'));
    }

    public function showBookingDetail(Schedule $schedule)
    {
        return view('booking_detail', compact('schedule'));
    }

    public function processBooking(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'booking_type' => 'required|in:passenger,item_delivery',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'pickup_address' => 'required|string|max:255',
            'dropoff_address' => 'required|string|max:255',
            'passengers.*.name' => 'required_if:booking_type,passenger|string|max:255',
            'passengers.*.id_number' => 'nullable|string|max:255|regex:/^[0-9]{16}$/',
            'item_description' => 'required_if:booking_type,item_delivery|string|max:255',
            'item_weight_kg' => 'required_if:booking_type,item_delivery|numeric|min:0.1',
        ]);

        $schedule = Schedule::findOrFail($request->schedule_id);

        $totalPrice = 0;
        $numPassengers = 0;
        if ($request->booking_type === 'passenger') {
            $numPassengers = count($request->passengers);
            if ($numPassengers > $schedule->available_seats) {
                return back()->withErrors(['message' => 'Jumlah penumpang melebihi kursi yang tersedia.']);
            }
            $totalPrice = $schedule->travelRoute->price_per_person * $numPassengers;
        } else {
            $totalPrice = $schedule->price_per_kg * $request->item_weight_kg;
        }

        $booking = Booking::create([
            'booking_code' => 'ALG-' . Str::upper(Str::random(8)),
            'schedule_id' => $schedule->id,
            'booking_type' => $request->booking_type,
            'number_of_passengers' => $request->booking_type === 'passenger' ? $numPassengers : null,
            'total_weight_kg' => $request->booking_type === 'item_delivery' ? $request->item_weight_kg : null,
            'total_price' => $totalPrice,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'pickup_address' => $request->pickup_address,
            'dropoff_address' => $request->dropoff_address,
            'status' => 'pending',
        ]);

        if ($request->booking_type === 'passenger') {
            foreach ($request->passengers as $passengerData) {
                Passenger::create([
                    'booking_id' => $booking->id,
                    'name' => $passengerData['name'],
                    'id_number' => $passengerData['id_number'] ?? null,
                ]);
            }
        } else {
            ItemDelivery::create([
                'booking_id' => $booking->id,
                'item_description' => $request->item_description,
                'weight_kg' => $request->item_weight_kg,
            ]);
        }

        return redirect()->route('booking.seats', $booking->ulid);
    }

    public function selectSeats(Booking $booking)
    {
        if ($booking->booking_type === 'item_delivery') {
            return redirect()->route('booking.payment', $booking->ulid);
        }

        // Re-check available seats to handle concurrent bookings
        $schedule = Schedule::findOrFail($booking->schedule_id);

        // Get occupied seats from *paid* or *confirmed* bookings for the same schedule
        $occupiedSeats = Passenger::whereHas('booking', function ($query) use ($schedule) {
            $query->where('schedule_id', $schedule->id)
                ->whereIn('status', ['paid', 'checked_in']); // Pastikan ini sesuai dengan status "terisi"
        })->whereNotNull('seat_number')->pluck('seat_number')->toArray();

        // Get seats currently selected by the current booking (if any were saved previously)
        $currentBookingSelectedSeats = Passenger::where('booking_id', $booking->id)
            ->whereNotNull('seat_number')
            ->pluck('seat_number')
            ->toArray();

        // Combine all occupied seats, including those from other confirmed bookings and those already selected by the current booking.
        $allOccupiedSeats = array_unique(array_merge($occupiedSeats, $currentBookingSelectedSeats));

        $totalSeats = 7; // Assuming 7 total seats as per your blade file

        $availableSeats = [];
        for ($i = 1; $i <= $totalSeats; $i++) {
            if (!in_array($i, $allOccupiedSeats)) {
                $availableSeats[] = $i;
            }
        }

        // Check if enough seats are still available for the current booking
        if (count($availableSeats) < $booking->number_of_passengers - count($currentBookingSelectedSeats)) {
            // This means some previously available seats might have been taken by others
            // Or if the user hasn't selected any, there aren't enough
            $errorMessage = 'Maaf, jumlah kursi yang Anda butuhkan ('.$booking->number_of_passengers.') tidak lagi tersedia sepenuhnya. Beberapa kursi mungkin sudah dipesan orang lain. Silakan coba pilih kembali kursi yang tersedia.';
            return redirect()->route('booking.detail', $booking->schedule->ulid)->withErrors(['message' => $errorMessage]);
        }


        return view('select_seats', compact('booking', 'availableSeats', 'totalSeats'));
    }

    public function saveSeats(Request $request, Booking $booking)
    {
        $request->validate([
            'selected_seats' => 'required|array|size:' . $booking->number_of_passengers,
            'selected_seats.*' => 'required|numeric|min:1|max:7',
        ]);

        $schedule = Schedule::findOrFail($booking->schedule_id);

        // Re-check occupied seats *just before saving* to prevent race conditions
        $occupiedSeatsAtSave = Passenger::whereHas('booking', function ($query) use ($booking, $schedule) {
            $query->where('schedule_id', $schedule->id)
                  ->whereIn('status', ['paid', 'checked_in'])
                  ->where('id', '!=', $booking->id); // Exclude seats from the current booking if already saved
        })->whereNotNull('seat_number')->pluck('seat_number')->toArray();

        foreach ($request->selected_seats as $seatNumber) {
            if (in_array((int)$seatNumber, $occupiedSeatsAtSave)) {
                // If a selected seat is now occupied by another *paid/confirmed* booking
                return back()->withErrors(['message' => 'Maaf, kursi nomor ' . $seatNumber . ' sudah tidak tersedia. Silakan pilih ulang kursi Anda.'])->withInput();
            }
        }

        // Atomically update seats. You might need a more robust locking mechanism
        // for very high-traffic applications, but for this context, re-checking
        // at the point of save is a good step.
        DB::transaction(function () use ($request, $booking, $schedule) {
            // Detach existing seat numbers for this booking's passengers first
            // This prevents issues if a user changes their seat selection
            Passenger::where('booking_id', $booking->id)->update(['seat_number' => null]);

            $passengers = $booking->passengers->sortBy('id'); // Ensure consistent order
            foreach ($request->selected_seats as $index => $seatNumber) {
                if (isset($passengers[$index])) {
                    $passengers[$index]->update(['seat_number' => (int) $seatNumber]);
                }
            }
            // No decrement here, decrement happens when payment is marked as completed by admin
            // $schedule->decrement('available_seats', $booking->number_of_passengers); // This line should be moved to markAsPaid or similar
        });


        return redirect()->route('booking.payment', $booking->ulid);
    }

    public function choosePaymentMethod(Booking $booking)
    {
        return view('payment_method', compact('booking'));
    }

    public function processPayment(Request $request, Booking $booking)
    {
        $request->validate([
            'payment_method' => 'required|string|max:255',
        ]);

        // Check seat availability one last time before processing payment
        if ($booking->booking_type === 'passenger') {
            $schedule = Schedule::find($booking->schedule_id);
            if (!$schedule || $schedule->available_seats < $booking->number_of_passengers) {
                return back()->withErrors(['message' => 'Maaf, kursi yang Anda pilih tidak lagi tersedia. Silakan cek kembali ketersediaan kursi.'])->withInput();
            }
        }

        // Create the payment record with 'pending' status
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'payment_method' => $request->payment_method, // Will be 'Transfer Bank BJB'
            'transaction_id' => 'TRX-' . Str::upper(Str::random(10)),
            'amount' => $booking->total_price,
            'payment_date' => now(), // Initial payment date
            'status' => 'pending', // Initial status is pending payment
        ]);

        // Update booking status to 'pending' to reflect it's awaiting payment
        $booking->update(['status' => 'pending']);

        // --- Logic Pengiriman Notifikasi WhatsApp ---
        $this->sendWhatsAppNotifications($booking, $payment); // Pass payment object to include payment details
        // --- Akhir Logic Pengiriman Notifikasi WhatsApp ---

        return redirect()->route('booking.confirmation', $booking->ulid);
    }

    /**
     * Fungsi untuk mengirim notifikasi WhatsApp.
     * @param \App\Models\Booking $booking
     * @param \App\Models\Payment $payment (optional, for payment details)
     */
    protected function sendWhatsAppNotifications(Booking $booking, Payment $payment = null)
    {
        $instanceId = env('ULTRAMSG_INSTANCE_ID');
        $token = env('ULTRAMSG_API_TOKEN');
        $baseUrl = env('ULTRAMSG_BASE_URL');
        $adminNumber = env('ADMIN_WHATSAPP_NUMBER');
        $BJBAccountNumber = env('BANK_BJB_ACCOUNT_NUMBER');
        $BJBAccountName = env('BANK_BJB_ACCOUNT_NAME');


        if (!$instanceId || !$token || !$baseUrl || !$adminNumber || !$BJBAccountNumber || !$BJBAccountName) {
            Log::error('ULTRAMSG API credentials or Bank/Admin numbers not set.');
            session()->flash('error', 'Gagal mengirim notifikasi WhatsApp: Konfigurasi belum lengkap.');
            return;
        }

        // --- Format Pesan untuk Admin ---
        $adminMessage = "*Booking Baru Diterima (Menunggu Pembayaran)*\n";
        $adminMessage .= "Kode Booking: *{$booking->booking_code}*\n";
        $adminMessage .= "Nama: {$booking->customer_name}\n";
        $adminMessage .= "Telepon: {$booking->customer_phone}\n";
        $adminMessage .= "Email: {$booking->customer_email}\n";
        $adminMessage .= "Tipe Booking: *" . ($booking->booking_type === 'passenger' ? 'Penumpang' : 'Pengiriman Barang') . "*\n";
        $adminMessage .= "Jadwal: " . $booking->schedule->departure_time->format('d M Y H:i') . "\n";
        $adminMessage .= "Rute: " . $booking->schedule->travelRoute->origin . " → " . $booking->schedule->travelRoute->destination . "\n\n";

        if ($booking->booking_type === 'passenger') {
            $adminMessage .= "*Detail Penumpang:*\n";
            foreach ($booking->passengers as $index => $p) {
                $adminMessage .= ($index + 1) . ". {$p->name}";
                if ($p->seat_number) {
                    $adminMessage .= " (Kursi: {$p->seat_number})";
                }
                $adminMessage .= "\n";
            }
        } else {
            $item = $booking->itemDelivery;
            $adminMessage .= "*Detail Barang:*\n";
            $adminMessage .= "Deskripsi: {$item->item_description}\n";
            $adminMessage .= "Berat: {$item->weight_kg} kg\n";
        }

        $adminMessage .= "\nTotal Harga: Rp " . number_format($booking->total_price, 0, ',', '.') . "\n";
        $adminMessage .= "Alamat Jemput: {$booking->pickup_address}\n";
        $adminMessage .= "Alamat Tujuan: {$booking->dropoff_address}\n";
        $adminMessage .= "Metode Pembayaran: *Transfer Bank BJB*\n";
        $statusPembayaran = $payment && $payment->status ? $payment->status : $booking->status;
        $adminMessage .= "Status Pembayaran: *{$statusPembayaran}*";
        $adminMessage .= "\n\nMohon cek bukti transfer yang akan dikirim customer.";


        // Kirim ke admin
        $this->sendWhatsAppMessage($adminNumber, $adminMessage);

        // --- Format Pesan untuk Customer ---
        $customerMessage = "*Terima kasih! Booking Anda berhasil dibuat.*\n\n";
        $customerMessage .= "Kode Booking: *{$booking->booking_code}*\n";
        $customerMessage .= "Nama: {$booking->customer_name}\n";
        $customerMessage .= "Jadwal: " . $booking->schedule->departure_time->translatedFormat('l, d F Y') . " pukul " . $booking->schedule->departure_time->format('H:i') . " WIB\n";
        $customerMessage .= "Rute: " . $booking->schedule->travelRoute->origin . " → " . $booking->schedule->travelRoute->destination . "\n\n";

        if ($booking->booking_type === 'passenger') {
            $customerMessage .= "*Penumpang:*\n";
            foreach ($booking->passengers as $index => $p) {
                $customerMessage .= ($index + 1) . ". {$p->name}";
                if ($p->seat_number) {
                    $customerMessage .= " (Kursi: {$p->seat_number})";
                }
                $customerMessage .= "\n";
            }
        } else {
            $item = $booking->itemDelivery;
            $customerMessage .= "*Pengiriman Barang:*\n";
            $customerMessage .= "Deskripsi: {$item->item_description}\n";
            $customerMessage .= "Berat: {$item->weight_kg} kg\n";
        }

        $customerMessage .= "\n*Total yang harus dibayar: Rp " . number_format($booking->total_price, 0, ',', '.') . "*\n";
        $customerMessage .= "Alamat Jemput: {$booking->pickup_address}\n";
        $customerMessage .= "Alamat Tujuan: {$booking->dropoff_address}\n\n";
        $customerMessage .= "*Mohon segera lakukan pembayaran via Transfer Bank BJB ke:*\n";
        $customerMessage .= "Bank: *Bank BJB*\n";
        $customerMessage .= "Nomor Rekening: *{$BJBAccountNumber}*\n";
        $customerMessage .= "Atas Nama: *{$BJBAccountName}*\n\n";
        $customerMessage .= "Setelah transfer, *kirim bukti transfer (screenshot) ke nomor WhatsApp ini* ({$adminNumber}).\n";
        $customerMessage .= "Pemesanan Anda akan dikonfirmasi setelah pembayaran diverifikasi oleh admin.\n\n";
        $customerMessage .= "Mohon hadir 15 menit sebelum keberangkatan.\n";
        $customerMessage .= "Terima kasih.";

        // Kirim ke customer
        $this->sendWhatsAppMessage($booking->customer_phone, $customerMessage);
    }


    /**
     * Mengirim pesan tunggal via Ultramsg API.
     * @param string $to Nomor telepon tujuan (tanpa +, dengan kode negara)
     * @param string $message Isi pesan
     */
    protected function sendWhatsAppMessage($phone, $message)
    {
        $url = env('ULTRAMSG_BASE_URL') . '/messages/chat';

        $response = Http::asForm()->post($url, [
            'token' => env('ULTRAMSG_API_TOKEN'),
            'to' => $phone,
            'body' => $message,
            'priority' => 10,
            'referenceId' => 'booking-' . Str::random(6),
        ]);

        Log::info('WhatsApp Response to ' . $phone . ': ' . $response->body());

        return $response->successful();
    }


    public function confirmation(Booking $booking)
    {
        $booking->load('payment');
        return view('booking_confirmation', compact('booking'));
    }

    public function markAsPaid(Booking $booking)
    {
        // Add a check to prevent marking paid if seats are now unavailable for passenger type
        if ($booking->booking_type === 'passenger') {
            $schedule = Schedule::find($booking->schedule_id);
            if (!$schedule) {
                return back()->with('error', 'Jadwal tidak ditemukan.');
            }

            // Get currently occupied seats for this schedule (excluding this booking's previously selected seats if any)
            $occupiedSeats = Passenger::whereHas('booking', function ($query) use ($schedule, $booking) {
                $query->where('schedule_id', $schedule->id)
                      ->whereIn('status', ['paid', 'checked_in'])
                      ->where('id', '!=', $booking->id); // Exclude current booking if it already had seats assigned and paid status
            })->whereNotNull('seat_number')->pluck('seat_number')->toArray();

            // Get selected seats for THIS booking
            $selectedSeatsForThisBooking = $booking->passengers->pluck('seat_number')->filter()->toArray();

            // Check if any of the selected seats for this booking are now occupied by *other* paid/checked_in bookings
            foreach ($selectedSeatsForThisBooking as $seat) {
                if (in_array($seat, $occupiedSeats)) {
                    // Send WhatsApp notification to customer about unavailable seats
                    $customerNumber = $booking->customer_phone;
                    $adminNumber = env('ADMIN_WHATSAPP_NUMBER');
                    $customerMessage = "Halo {$booking->customer_name},\n\n";
                    $customerMessage .= "Maaf, kami tidak dapat mengonfirmasi pembayaran Anda untuk Kode Booking: *{$booking->booking_code}* karena kursi nomor *{$seat}* yang Anda pilih sudah tidak tersedia (telah dipesan oleh orang lain). \n";
                    $customerMessage .= "Mohon hubungi admin kami ({$adminNumber}) untuk bantuan lebih lanjut mengenai perubahan kursi atau pengembalian dana.\n\n";
                    $customerMessage .= "Terima kasih atas pengertiannya.";
                    $this->sendWhatsAppMessage($customerNumber, $customerMessage);

                    return back()->with('error', 'Kursi yang dipilih oleh pelanggan Anda (' . implode(', ', $selectedSeatsForThisBooking) . ') sudah tidak tersedia. Notifikasi ke pelanggan telah dikirimkan.');
                }
            }

            // Check if there are enough general available seats, even if specific ones are fine (fallback safety)
            // This is less critical if individual seat check is robust, but good for total count.
            if ($schedule->available_seats < $booking->number_of_passengers) {
                // If the total available seats is less than required, even if individual selected seats are currently free
                // This scenario means there's a logic flaw or extreme race condition if individual seats were free.
                // It's safer to block.
                $customerNumber = $booking->customer_phone;
                $adminNumber = env('ADMIN_WHATSAPP_NUMBER');
                $customerMessage = "Halo {$booking->customer_name},\n\n";
                $customerMessage .= "Maaf, kami tidak dapat mengonfirmasi pembayaran Anda untuk Kode Booking: *{$booking->booking_code}* karena jumlah kursi yang Anda pesan tidak lagi tersedia. \n";
                $customerMessage .= "Mohon hubungi admin kami ({$adminNumber}) untuk bantuan lebih lanjut mengenai perubahan kursi atau pengembalian dana.\n\n";
                $customerMessage .= "Terima kasih atas pengertiannya.";
                $this->sendWhatsAppMessage($customerNumber, $customerMessage);

                return back()->with('error', 'Jumlah kursi yang diperlukan oleh pelanggan tidak lagi tersedia. Notifikasi ke pelanggan telah dikirimkan.');
            }
        }


        if ($booking->payment && $booking->payment->status !== 'completed') {
            try {
                // Use a transaction for critical updates
                DB::transaction(function () use ($booking) {
                    $booking->payment->update(['status' => 'completed']);
                    $booking->update(['status' => 'paid']); // Update booking status to paid

                    // Decrement available seats ONLY if booking type is passenger and it hasn't been decremented yet
                    // The 'paid' status on booking can serve as a flag that seats have been allocated.
                    if ($booking->booking_type === 'passenger' && $booking->number_of_passengers > 0) {
                        $schedule = Schedule::findOrFail($booking->schedule_id);
                        if ($schedule->available_seats >= $booking->number_of_passengers) {
                            $schedule->decrement('available_seats', $booking->number_of_passengers);
                        } else {
                            // This should ideally be caught by the earlier checks, but as a fallback:
                            throw new \Exception("Kursi tidak cukup saat mengkonfirmasi pembayaran untuk booking_id: {$booking->id}.");
                        }
                    }
                });

                // Send invoice to customer and admin after successful mark as paid
                $this->sendInvoiceWhatsApp($booking);

                return back()->with('success', 'Pemesanan berhasil ditandai sebagai LUNAS dan kursi telah dikurangi (jika berlaku). Invoice telah dikirim.');
            } catch (\Exception $e) {
                Log::error("Failed to mark booking as paid: " . $e->getMessage(), ['booking_id' => $booking->id]);
                return back()->with('error', 'Gagal menandai pemesanan sebagai LUNAS: ' . $e->getMessage());
            }
        }
        return back()->with('error', 'Gagal menandai pemesanan sebagai LUNAS. Status pembayaran sudah selesai atau tidak ada data pembayaran.');
    }

    /**
     * Mengirim invoice via WhatsApp ke customer dan admin.
     * @param \App\Models\Booking $booking
     */
    protected function sendInvoiceWhatsApp(Booking $booking)
    {
        $instanceId = env('ULTRAMSG_INSTANCE_ID');
        $token = env('ULTRAMSG_API_TOKEN');
        $baseUrl = env('ULTRAMSG_BASE_URL');
        $adminNumber = env('ADMIN_WHATSAPP_NUMBER');

        if (!$instanceId || !$token || !$baseUrl || !$adminNumber) {
            Log::error('ULTRAMSG API credentials or ADMIN_WHATSAPP_NUMBER not set for invoice.');
            return;
        }

        // --- Format Pesan Invoice untuk Customer ---
        $invoiceCustomerMessage = "*INVOICE PEMBAYARAN - Algio Trans*\n\n";
        $invoiceCustomerMessage .= "Halo {$booking->customer_name},\n";
        $invoiceCustomerMessage .= "Pembayaran Anda untuk Kode Booking: *{$booking->booking_code}* telah berhasil dikonfirmasi.\n\n";
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

        $invoiceCustomerMessage .= "\nTerima kasih telah menggunakan layanan Algio Trans. Sampai jumpa di perjalanan Anda!";
        // Kirim ke customer
        $this->sendWhatsAppMessage($booking->customer_phone, $invoiceCustomerMessage);


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
            $invoiceAdminMessage .= "Kursi tersedia tersisa untuk jadwal ini: " . $booking->schedule->available_seats . "\n";
        } else {
             $itemDescription = $booking->itemDelivery ? $booking->itemDelivery->item_description : '';
             $itemWeight = $booking->itemDelivery ? $booking->itemDelivery->weight_kg : '';
             $invoiceAdminMessage .= "Detail Barang: {$itemDescription} ({$itemWeight} kg)\n";
        }

        // Kirim ke admin
        $this->sendWhatsAppMessage($adminNumber, $invoiceAdminMessage);
    }
}