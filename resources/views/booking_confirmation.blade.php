@extends('layouts.app')

@section('title', 'Konfirmasi Pemesanan')

@section('content')
<div id="loading-overlay" class="fixed inset-0 bg-white bg-opacity-90 flex items-center justify-center z-[9999] transition-opacity duration-300">
    <div class="flex flex-col items-center">
        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-sky-500"></div>
        <p class="mt-4 text-lg font-semibold text-sky-700">Memuat konfirmasi...</p>
    </div>
</div>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl lg:max-w-6xl mx-auto bg-white p-6 md:p-8 rounded-3xl shadow-2xl border-2 border-sky-300">
        <div class="text-center mb-6">
            {{-- Conditional check mark or hourglass based on payment status --}}
            @if($booking->payment && $booking->payment->status === 'pending')
                <i class="fas fa-hourglass-half text-orange-500 text-5xl md:text-6xl mb-3 animate-pulse"></i>
                <h2 class="text-3xl md:text-4xl font-extrabold text-orange-600 algiotrans-title-text mb-2">Pemesanan Menunggu Pembayaran!</h2>
                <p class="text-base md:text-lg text-gray-700">Mohon segera lakukan pembayaran untuk mengkonfirmasi pesanan Anda.</p>
            @else
                <i class="fas fa-check-circle text-sky-600 text-5xl md:text-6xl mb-3 animate-scale-in"></i>
                <h2 class="text-3xl md:text-4xl font-extrabold text-sky-700 algiotrans-title-text mb-2">Pemesanan Berhasil!</h2>
                <p class="text-base md:text-lg text-gray-700">Terima kasih telah memesan dengan Algio Trans.</p>
            @endif

            {{-- Informasi Menunggu Verifikasi (hanya tampil jika status payment 'pending') --}}
            @if($booking->payment && $booking->payment->status === 'pending')
                <div class="mt-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded-lg shadow-sm">
                    <p class="font-semibold text-lg flex items-center justify-center"><i class="fas fa-info-circle mr-2"></i> Pembayaran Menunggu Verifikasi</p>
                    <p class="mt-2 text-base">Pesanan Anda telah kami terima dan menunggu pembayaran Anda diverifikasi oleh admin. Kami akan mengirimkan konfirmasi lebih lanjut setelah pembayaran Anda lunas.</p>
                    <p class="mt-2 text-sm text-yellow-700"><b>Estimasi waktu verifikasi beberapa menit, halaman jangan ditutup dan harap refresh ketika menerima notifikasi pembayaran berhasil.</b></p>
                    <p class="mt-3 text-sm text-yellow-800">
                        Anda juga dapat mengirimkan bukti transfer ke nomor WhatsApp kami untuk mempercepat proses verifikasi:
                        <a href="https://wa.me/{{ env('WHATSAPP_CONFIRMATION_NUMBER') }}" target="_blank" class="font-bold text-green-700 hover:underline flex items-center justify-center mt-1">
                            <i class="fab fa-whatsapp mr-1"></i> {{ env('WHATSAPP_CONFIRMATION_NUMBER_DISPLAY') ?? env('WHATSAPP_CONFIRMATION_NUMBER') }}
                        </a>
                    </p>
                </div>
            @endif
        </div>

        {{-- Action Button (Unduh Tiket) --}}
        <div class="mb-6 flex justify-center">
            @if($booking->payment && $booking->payment->status === 'completed')
                <a href="#" id="download-ticket-button" class="inline-flex items-center bg-sky-500 hover:bg-sky-600 text-white font-bold py-2 px-6 rounded-lg text-base transition duration-300 shadow-lg transform hover:scale-105">
                    <i class="fas fa-download mr-2"></i> Unduh Tiket
                </a>
            @else
                <span class="inline-flex items-center bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg text-base cursor-not-allowed opacity-75">
                    <i class="fas fa-download mr-2"></i> Tiket Tersedia Setelah Pembayaran Lunas
                </span>
            @endif
        </div>

        {{-- Main Content - Responsive Layout --}}
        <div class="flex flex-col md:flex-row md:space-x-6 space-y-6 md:space-y-0">

            {{-- Left Column: Detail Pemesanan Anda --}}
            <div class="w-full md:w-1/2 p-5 bg-sky-50 rounded-xl border-2 border-dashed border-sky-300 shadow-sm">
                <h3 class="text-xl font-semibold mb-3 text-sky-800 flex items-center">
                    <i class="fas fa-ticket-alt mr-2 text-sky-600"></i> Detail Pemesanan Anda
                </h3>

                <div class="space-y-2 text-base text-gray-700 mb-4">
                    <p><strong>Kode Booking:</strong> <span class="text-sky-800 font-bold">{{ $booking->booking_code }}</span></p>
                    <p><strong>Tipe Booking:</strong> <span class="font-medium text-gray-800">{{ $booking->booking_type === 'passenger' ? 'Penumpang' : 'Pengiriman Barang' }}</span></p>
                    <p><strong>Rute:</strong> <span class="font-medium text-gray-800">{{ $booking->schedule->travelRoute->origin }} - {{ $booking->schedule->travelRoute->destination }}</span></p>
                    <p><strong>Waktu Keberangkatan/Pengiriman:</strong> <span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($booking->schedule->departure_time)->format('d F Y H:i') }}</span></p>

                    <div class="py-2 px-3 bg-sky-100 rounded-lg border border-sky-200 flex justify-between items-center mt-3">
                        <p class="text-lg font-bold text-sky-800">Total Harga:</p>
                        <p class="text-2xl font-extrabold text-sky-700">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                    </div>

                    <p><strong>Metode Pembayaran:</strong> <span class="font-medium text-gray-800">{{ $booking->payment->payment_method ?? 'N/A' }}</span></p>
                    <p><strong>Status Pembayaran:</strong>
                        <span class="font-semibold
                            @if($booking->payment && $booking->payment->status === 'completed') text-sky-600
                            @elseif($booking->payment && $booking->payment->status === 'pending') text-orange-500
                            @else text-gray-500 @endif">
                            {{ ucfirst($booking->payment->status ?? 'N/A') }}
                        </span>
                    </p>
                </div>

                <p class="text-gray-600 text-sm border-t border-sky-200 pt-3">
                    Detail tiket dan instruksi lebih lanjut akan dikirimkan ke email/WhatsApp Anda dalam beberapa saat. Mohon periksa folder spam Anda jika tidak menemukannya.
                </p>
            </div>

            {{-- Right Column: Informasi Kontak & Detail Penumpang/Barang --}}
            <div class="w-full md:w-1/2 space-y-6">
                <div class="p-5 bg-sky-50 rounded-xl border-2 border-dashed border-sky-300 shadow-sm">
                    <h4 class="text-xl font-semibold mb-3 text-sky-800 flex items-center">
                        <i class="fas fa-user-circle mr-2 text-sky-600"></i> Informasi Kontak
                    </h4>
                    <div class="space-y-2 text-base text-gray-700">
                        <p><strong>Nama Pemesan:</strong> <span class="font-medium text-gray-800">{{ $booking->customer_name }}</span></p>
                        <p><strong>Email:</strong> <span class="font-medium text-gray-800">{{ $booking->customer_email }}</span></p>
                        <p><strong>Telepon:</strong> <span class="font-medium text-gray-800">{{ $booking->customer_phone }}</span></p>
                        <p><strong>Alamat Penjemputan:</strong> <span class="font-medium text-gray-800">{{ $booking->pickup_address }}</span></p>
                        <p><strong>Alamat Tujuan:</strong> <span class="font-medium text-gray-800">{{ $booking->dropoff_address }}</span></p>
                    </div>
                </div>

                <div class="p-5 bg-sky-50 rounded-xl border-2 border-dashed border-sky-300 shadow-sm">
                    @if($booking->booking_type === 'passenger')
                        <h4 class="text-xl font-semibold mb-3 text-sky-800 flex items-center">
                            <i class="fas fa-users mr-2 text-sky-600"></i> Detail Penumpang
                        </h4>
                        <ul class="list-disc list-inside ml-3 text-gray-700 space-y-1 text-base">
                            @foreach($booking->passengers as $passenger)
                                <li><span class="font-medium">{{ $passenger->name }}</span> (Kursi: <span class="font-bold">{{ $passenger->seat_number ?? 'Belum dipilih' }}</span>)</li>
                            @endforeach
                        </ul>
                    @else
                        <h4 class="text-xl font-semibold mb-3 text-sky-800 flex items-center">
                            <i class="fas fa-box mr-2 text-sky-600"></i> Detail Barang Kiriman
                        </h4>
                        <p class="text-base text-gray-700"><span class="font-medium">{{ $booking->itemDelivery->item_description ?? '' }}</span> (<span class="font-bold">{{ $booking->itemDelivery->weight_kg ?? '' }} kg</span>)</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Action Buttons (Kembali ke Beranda) --}}
        <div class="mt-8 flex flex-col md:flex-row justify-center items-center space-y-3 md:space-y-0 md:space-x-4">
            <a href="{{ route('home') }}" class="inline-flex items-center bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg text-base transition duration-300 shadow-md transform hover:scale-105">
                <i class="fas fa-home mr-2"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

{{-- Hidden div for ticket content to be printed --}}
<div id="ticket-content" class="hidden" style="width: 80mm; font-family: 'Arial', sans-serif; font-size: 10px; line-height: 1.4;">
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .ticket-container {
                border: 1px dashed #A0D8EF; /* Biru pastel */
                padding: 10mm;
                margin: 5mm auto;
                background-color: #F0F8FF; /* Biru sangat muda */
            }
            .ticket-header {
                text-align: center;
                margin-bottom: 5mm;
            }
            .ticket-title {
                font-size: 14px; /* Disesuaikan untuk cetak */
                font-weight: bold;
                color: #2B6CB0; /* Biru gelap */
            }
            .ticket-subtitle {
                font-size: 10px; /* Disesuaikan untuk cetak */
                color: #4A5568;
            }
            .ticket-section-title {
                font-size: 11px; /* Disesuaikan untuk cetak */
                font-weight: bold;
                color: #2C5282; /* Biru sedikit gelap */
                margin-top: 4mm;
                margin-bottom: 2mm;
                border-bottom: 1px dashed #A0D8EF;
                padding-bottom: 1mm;
            }
            .ticket-detail {
                margin-bottom: 1mm;
                font-size: 9px; /* Disesuaikan untuk cetak */
            }
            .ticket-detail strong {
                font-weight: bold;
                color: #1A202C;
            }
            .ticket-highlight {
                font-size: 12px; /* Disesuaikan untuk cetak */
                font-weight: bold;
                color: #3182CE; /* Biru terang */
                text-align: center;
                margin-top: 5mm;
                padding: 3mm;
                background-color: #EBF8FF;
                border: 1px solid #90CDF4;
                border-radius: 3px;
            }
            .ticket-footer {
                text-align: center;
                margin-top: 5mm;
                font-size: 7px; /* Disesuaikan untuk cetak */
                color: #718096;
            }
            .passenger-list li {
                list-style-type: disc;
                margin-left: 5mm;
            }
        }
    </style>
    <div class="ticket-container">
        <div class="ticket-header">
            <h1 class="ticket-title">TIKET ALGIO TRANS</h1>
            <p class="ticket-subtitle">Booking ID: {{ $booking->booking_code }}</p>
        </div>

        <div class="ticket-highlight">
            Total Harga: Rp {{ number_format($booking->total_price, 0, ',', '.') }}
        </div>

        <div class="ticket-section-title">Informasi Perjalanan</div>
        <p class="ticket-detail"><strong>Rute:</strong> {{ $booking->schedule->travelRoute->origin }} - {{ $booking->schedule->travelRoute->destination }}</p>
        <p class="ticket-detail"><strong>Waktu Keberangkatan/Pengiriman:</strong> {{ \Carbon\Carbon::parse($booking->schedule->departure_time)->format('d F Y H:i') }}</p>
        <p class="ticket-detail"><strong>Metode Pembayaran:</strong> {{ $booking->payment->payment_method ?? 'N/A' }}</p>
        <p class="ticket-detail"><strong>Status Pembayaran:</strong> {{ ucfirst($booking->status) }}</p>

        <div class="ticket-section-title">Informasi Kontak Pemesan</div>
        <p class="ticket-detail"><strong>Nama:</strong> {{ $booking->customer_name }}</p>
        <p class="ticket-detail"><strong>Email:</strong> {{ $booking->customer_email }}</p>
        <p class="ticket-detail"><strong>Telepon:</strong> {{ $booking->customer_phone }}</p>
        <p class="ticket-detail"><strong>Penjemputan:</strong> {{ $booking->pickup_address }}</p>
        <p class="ticket-detail"><strong>Tujuan:</strong> {{ $booking->dropoff_address }}</p>

        @if($booking->booking_type === 'passenger')
            <div class="ticket-section-title">Detail Penumpang ({{ $booking->number_of_passengers }} Orang)</div>
            <ul class="passenger-list">
                @foreach($booking->passengers as $passenger)
                    <li>{{ $passenger->name }} (Kursi: {{ $passenger->seat_number ?? 'Belum dipilih' }})</li>
                @endforeach
            </ul>
        @else
            <div class="ticket-section-title">Detail Barang Kiriman</div>
            <p class="ticket-detail"><strong>Deskripsi:</strong> {{ $booking->itemDelivery->item_description ?? '' }}</p>
            <p class="ticket-detail"><strong>Berat:</strong> {{ $booking->itemDelivery->weight_kg ?? '' }} kg</p>
        @endif

        <div class="ticket-footer">
            <p>Harap tunjukkan tiket ini saat keberangkatan/pengambilan barang.</p>
            <p>Terima kasih telah menggunakan layanan Algio Trans.</p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- Loading Overlay Logic ---
        const loadingOverlay = document.getElementById('loading-overlay');
        loadingOverlay.style.opacity = '0'; // Start fading out immediately
        setTimeout(() => {
            loadingOverlay.style.display = 'none'; // Hide completely after transition
        }, 300); // Matches CSS transition duration

        // --- Prevent Back Navigation ---
        // Pushing a new state to history to prevent direct back button press
        // and replacing the current state so 'back' always goes to home.
        history.replaceState(null, null, location.href); // Replace current state
        window.addEventListener('popstate', function () {
            // Redirect to home page if user tries to go back
            window.location.replace("{{ route('home') }}");
        });

        // --- Ticket Download Logic ---
        const downloadButton = document.getElementById('download-ticket-button');
        // Check if the button exists and if the booking payment status is 'completed'
        if (downloadButton && ( "{{ $booking->payment->status ?? '' }}" === 'completed' ) ) { // Use payment->status for accuracy
            downloadButton.addEventListener('click', (e) => {
                e.preventDefault(); // Prevent default link behavior

                const ticketContent = document.getElementById('ticket-content');
                if (ticketContent) {
                    const printWindow = window.open('', '_blank');
                    printWindow.document.write('<html><head><title>Tiket Pemesanan - Algio Trans</title>');
                    // Embed print styles directly
                    printWindow.document.write('<style>');
                    printWindow.document.write(ticketContent.querySelector('style').innerHTML); // Copy the style block
                    printWindow.document.write('</style>');
                    printWindow.document.write('</head><body>');
                    printWindow.document.write(ticketContent.innerHTML); // Copy the content
                    printWindow.document.write('</body></html>');
                    printWindow.document.close();
                    printWindow.print();
                } else {
                    console.error('Ticket content element not found!');
                }
            });
        }
    });
</script>

<style>
    /* Gradient text untuk judul utama */
    .algiotrans-title-text {
        background: linear-gradient(45deg, #87CEEB, #A0D8EF, #B2EBF2); /* Biru pastel ke biru pastel yang lebih muda */
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-family: 'Montserrat', sans-serif;
        font-weight: 800;
        line-height: 1.2;
    }

    /* Animation for check-circle */
    @keyframes scale-in {
        0% {
            transform: scale(0);
            opacity: 0;
        }
        70% {
            transform: scale(1.1);
            opacity: 1;
        }
        100% {
            transform: scale(1);
        }
    }
    .animate-scale-in {
        animation: scale-in 0.6s ease-out forwards;
    }

    /* New animation for pending status */
    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.05); opacity: 0.8; }
        100% { transform: scale(1); opacity: 1; }
    }
    .animate-pulse {
        animation: pulse 1.5s infinite ease-in-out;
    }
</style>
@endsection