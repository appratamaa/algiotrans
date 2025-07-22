@extends('layouts.app')

@section('title', 'Rincian Pembayaran')

@section('content')
<div id="loading-overlay" class="fixed inset-0 bg-white bg-opacity-90 flex items-center justify-center z-[9999] transition-opacity duration-300">
    <div class="flex flex-col items-center">
        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-sky-500"></div>
        <p class="mt-4 text-lg font-semibold text-sky-700">Memuat rincian...</p>
    </div>
</div>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl lg:max-w-6xl mx-auto bg-white p-6 md:p-8 rounded-3xl shadow-2xl border-2 border-sky-300">
        <h2 class="text-3xl md:text-4xl font-extrabold text-center mb-6 text-sky-700 algiotrans-title-text">Rincian Pembayaran Anda</h2>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Oops!</strong>
                <span class="block sm:inline">{{ $errors->first() }}</span>
            </div>
        @endif

        <div class="flex flex-col md:flex-row md:space-x-6 space-y-6 md:space-y-0">

            {{-- Left Column: Ringkasan Pemesanan --}}
            <div class="w-full md:w-1/2 p-5 bg-sky-50 rounded-xl border-2 border-dashed border-sky-300 shadow-sm">
                <h3 class="text-xl font-semibold mb-3 text-sky-800 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-sky-600"></i> Ringkasan Pemesanan
                </h3>
                <div class="space-y-2 text-base text-gray-700">
                    <p class="flex items-center">
                        <strong>Kode Booking:</strong>
                        <span id="booking-code-text" class="font-medium text-gray-800 ml-1 mr-2">{{ $booking->booking_code }}</span>
                        <i class="fas fa-copy text-sky-500 cursor-pointer hover:text-sky-700 transition-colors duration-200 text-base"
                           onclick="copyToClipboard('{{ $booking->booking_code }}', 'Kode Booking berhasil disalin!')"
                           title="Salin Kode Booking"></i>
                    </p>
                    @if($booking->booking_type == 'passenger')
                        <p><strong>Jumlah Penumpang:</strong> <span class="font-medium text-gray-800">{{ $booking->number_of_passengers }} Orang</span></p>
                        <p><strong>Kursi Terpilih:</strong>
                            <span class="font-medium text-gray-800">
                                @php
                                    $selectedSeats = $booking->passengers->pluck('seat_number')->filter()->sort()->implode(', ');
                                @endphp
                                {{ $selectedSeats ?: 'Belum memilih kursi' }}
                            </span>
                        </p>
                    @else
                        <p><strong>Berat Barang:</strong> <span class="font-medium text-gray-800">{{ $booking->total_weight_kg }} Kg</span></p>
                        <p><strong>Deskripsi:</strong> <span class="font-medium text-gray-800">{{ $booking->itemDelivery->item_description ?? '-' }}</span></p>
                    @endif
                    <p><strong>Rute:</strong> <span class="font-medium text-gray-800">{{ $booking->schedule->travelRoute->origin }} - {{ $booking->schedule->travelRoute->destination }}</span></p>
                    <p><strong>Keberangkatan:</strong> <span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($booking->schedule->departure_time)->format('d F Y H:i') }}</span></p>
                </div>
            </div>

            {{-- Right Column: Total Pembayaran & Metode Pembayaran --}}
            <div class="w-full md:w-1/2 space-y-6">
                {{-- Total Pembayaran --}}
                <div class="text-center p-5 bg-sky-100 rounded-xl border-2 border-dashed border-sky-300 shadow-md">
                    <p class="text-base text-gray-700 mb-2">Total yang harus dibayar:</p>
                    <div class="flex items-center justify-center">
                        <p id="total-price-text" class="text-4xl md:text-5xl font-extrabold text-sky-700 mr-3">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                        <i class="fas fa-copy text-sky-500 cursor-pointer hover:text-sky-700 transition-colors duration-200 text-2xl"
                           onclick="copyToClipboard('{{ $booking->total_price }}', 'Harga berhasil disalin!')"
                           title="Salin Harga Total"></i>
                    </div>
                </div>

                {{-- Metode Pembayaran Form --}}
                <form id="payment-form" action="{{ route('booking.pay', $booking->ulid) }}" method="POST" class="space-y-5">
                    @csrf
                    <h3 class="text-xl font-semibold mb-3 text-sky-800 flex items-center">
                        <i class="fas fa-wallet mr-2 text-sky-600"></i> Pilih Metode Pembayaran
                    </h3>
                    <div class="space-y-3">
                        {{-- Opsi Pembayaran Transfer Bank BRI --}}
                        <div class="p-4 border-2 border-sky-300 rounded-xl bg-sky-50 hover:bg-sky-100 transition duration-200">
                            <label for="bank_transfer" class="flex items-center cursor-pointer">
                                <input type="radio" id="bank_transfer" name="payment_method" value="Transfer Bank BRI" class="form-radio h-5 w-5 text-sky-600 focus:ring-sky-500" checked required>
                                <span class="ml-3 text-lg font-semibold text-sky-800 flex items-center">
                                    <i class="fas fa-bank mr-2 text-sky-600 text-xl"></i> Transfer Bank BRI
                                </span>
                            </label>
                            <div class="mt-4 border-t border-sky-200 pt-4 space-y-2">
                                <p class="text-sm text-gray-600">Silakan transfer ke rekening berikut:</p>
                                <div class="flex items-center">
                                    <strong class="text-gray-800 text-lg">Bank BRI:</strong>
                                    <span id="bank-account-number" class="ml-2 font-mono text-xl text-sky-700 font-bold mr-2">{{ env('BANK_BRI_ACCOUNT_NUMBER') }}</span>
                                    <i class="fas fa-copy text-sky-500 cursor-pointer hover:text-sky-700 transition-colors duration-200 text-base"
                                       onclick="copyToClipboard('{{ env('BANK_BRI_ACCOUNT_NUMBER') }}', 'Nomor rekening berhasil disalin!')"
                                       title="Salin Nomor Rekening"></i>
                                </div>
                                <p class="text-base text-gray-700">A.N: <strong class="text-gray-800">{{ env('BANK_BRI_ACCOUNT_NAME') }}</strong></p>
                                <p class="text-sm text-red-500">
                                    <i class="fas fa-exclamation-circle mr-1"></i> Penting: Pastikan jumlah transfer sama persis dengan total yang harus dibayar.
                                </p>
                            </div>
                        </div>

                        {{-- Informasi Metode Pembayaran Lain dalam Pemeliharaan --}}
                        <div class="p-4 border-2 border-dashed border-yellow-300 rounded-xl bg-yellow-50 text-yellow-800 flex items-center space-x-3">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
                            <div>
                                <h4 class="font-bold text-lg">Metode Pembayaran Lain</h4>
                                <p class="text-base">Saat ini, opsi Pembayaran Tunai dan metode pembayaran online lainnya (seperti e-wallet atau bank lain) sedang dalam pemeliharaan. Mohon gunakan opsi <b>Transfer Bank BRI</b> yang tersedia di atas.</p>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-6">
                        <button type="submit" id="pay-now-button" class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-3 px-6 rounded-lg text-lg transition duration-300 shadow-lg transform hover:scale-105 flex items-center justify-center">
                            <span id="pay-button-text">Bayar Sekarang</span>
                            <svg id="pay-loading-spinner" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Custom Toast Notification HTML (for copy functionality) --}}
<div id="toast-notification" class="fixed top-4 right-4 z-[9999] p-4 rounded-lg shadow-lg flex items-center space-x-3
    transform translate-x-full transition-transform duration-300 ease-out">
    <i id="toast-icon" class="fas text-2xl"></i>
    <div class="flex-grow">
        <h4 id="toast-title" class="font-bold text-lg mb-1"></h4>
        <p id="toast-message" class="text-sm"></p>
    </div>
    <button id="toast-close-button" class="text-gray-600 hover:text-gray-800 focus:outline-none">
        <i class="fas fa-times"></i>
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Hide loading overlay when content is loaded
        const loadingOverlay = document.getElementById('loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.style.opacity = '0';
            setTimeout(() => {
                loadingOverlay.style.display = 'none';
            }, 300); // Duration matches CSS transition
        }

        // --- Form Submission Loading Logic ---
        const paymentForm = document.getElementById('payment-form');
        const payNowButton = document.getElementById('pay-now-button');
        const payButtonText = document.getElementById('pay-button-text');
        const payLoadingSpinner = document.getElementById('pay-loading-spinner');

        if (paymentForm && payNowButton) {
            paymentForm.addEventListener('submit', () => {
                // Show loading spinner and disable button
                payButtonText.textContent = 'Memproses Pembayaran...';
                payLoadingSpinner.classList.remove('hidden');
                payNowButton.disabled = true;
                payNowButton.classList.add('opacity-75', 'cursor-not-allowed');
            });
        }
        // --- End Form Submission Loading Logic ---

        // --- Toast Notification Logic (reused for copy success) ---
        const toastNotification = document.getElementById('toast-notification');
        const toastIcon = document.getElementById('toast-icon');
        const toastTitle = document.getElementById('toast-title');
        const toastMessage = document.getElementById('toast-message');
        const toastCloseButton = document.getElementById('toast-close-button');
        let toastTimeout;

        function showToast(type, title, message, duration = 3000) {
            clearTimeout(toastTimeout);

            toastTitle.textContent = title;
            toastMessage.textContent = message;

            toastNotification.className = 'fixed top-4 right-4 z-[9999] p-4 rounded-lg shadow-lg flex items-center space-x-3 transform transition-transform duration-300 ease-out';
            toastIcon.className = 'fas text-2xl';

            if (type === 'success') {
                toastNotification.classList.add('bg-sky-100', 'border-l-4', 'border-sky-500', 'text-sky-800');
                toastIcon.classList.add('fa-check-circle', 'text-sky-500');
            } else if (type === 'warning') {
                toastNotification.classList.add('bg-orange-100', 'border-l-4', 'border-orange-500', 'text-orange-800');
                toastIcon.classList.add('fa-exclamation-triangle', 'text-orange-500');
            } else if (type === 'error') {
                toastNotification.classList.add('bg-red-100', 'border-l-4', 'border-red-500', 'text-red-800');
                toastIcon.classList.add('fa-times-circle', 'text-red-500');
            } else { // Default to info
                toastNotification.classList.add('bg-sky-100', 'border-l-4', 'border-sky-500', 'text-sky-800');
                toastIcon.classList.add('fa-info-circle', 'text-sky-500');
            }

            toastNotification.classList.remove('translate-x-full');

            toastTimeout = setTimeout(() => {
                hideToast();
            }, duration);
        }

        function hideToast() {
            toastNotification.classList.add('translate-x-full');
        }

        toastCloseButton.addEventListener('click', () => {
            clearTimeout(toastTimeout);
            hideToast();
        });

        // --- Copy to Clipboard Function ---
        window.copyToClipboard = async (textToCopy, successMessage) => {
            try {
                await navigator.clipboard.writeText(textToCopy);
                showToast('success', 'Berhasil Disalin!', successMessage);
            } catch (err) {
                console.error('Gagal menyalin teks:', err);
                showToast('error', 'Gagal!', 'Gagal menyalin teks. Silakan coba lagi.');
            }
        };
    });
</script>

<style>
    /* Gradient text untuk judul utama */
    .algiotrans-title-text {
        background: linear-gradient(45deg, #87CEEB, #A0D8EF, #B2EBF2); /* Biru pastel ke biru pastel yang lebih muda */
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-family: 'Montserrat', sans-serif; /* Pastikan font ini tersedia atau ganti dengan font yang ada */
        font-weight: 800;
        line-height: 1.2;
    }

    /* Basic spinner animation (from your previous code) */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .animate-spin {
        animation: spin 1s linear infinite;
    }
</style>
@endsection