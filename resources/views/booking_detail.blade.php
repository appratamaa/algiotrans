@extends('layouts.app')

@section('title', 'Detail Pemesanan')

@section('content')
{{-- Loading Spinner Overlay --}}
<div id="loading-overlay" class="fixed inset-0 bg-white bg-opacity-90 flex items-center justify-center z-[9999] transition-opacity duration-300">
    <div class="flex flex-col items-center">
        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-sky-500"></div>
        <p class="mt-4 text-lg font-semibold text-sky-700">Memuat detail pemesanan...</p>
    </div>
</div>

<div class="container mx-auto px-4 py-12"> {{-- Main container for spacing --}}
    {{-- Judul utama dengan teks putih --}}
    <h2 class="text-4xl md:text-5xl font-extrabold text-center mb-10 text-sky-700 algiotrans-title-text ">Detail Pemesanan Anda</h2>

    @if (session('errors'))
        <div class="max-w-4xl mx-auto bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-lg relative mb-6 shadow-sm" role="alert">
            <strong class="font-bold">Oops!</strong>
            <span class="block sm:inline">{{ session('errors')->first('message') }}</span>
        </div>
    @endif

    <form id="booking-form" action="{{ route('booking.process') }}" method="POST">
        @csrf
        <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
        <input type="hidden" name="booking_type" value="{{ request('booking_type') }}">

        {{-- Main Content Area: Menggunakan flex untuk desktop dan tablet (lg ke atas), stack untuk mobile --}}
        <div class="flex flex-col lg:flex-row lg:space-x-8 space-y-10 lg:space-y-0 max-w-6xl mx-auto items-start">

            {{-- Kolom Kiri: Jadwal Perjalanan & Informasi Penumpang/Barang --}}
            <div class="lg:w-1/2 flex flex-col space-y-10"> {{-- Ambil setengah lebar di lg ke atas, jadikan flex column --}}

                {{-- Section: Jadwal Perjalanan (Background putih) --}}
                <div class="p-6 md:p-8 bg-white rounded-xl border-2 border-dashed border-sky-300">
                    <h3 class="text-2xl md:text-3xl font-bold mb-5 text-sky-800 flex items-center">
                        <i class="fas fa-calendar-alt mr-4 text-sky-600"></i> Jadwal Perjalanan
                    </h3>
                    <div class="space-y-4 text-lg text-gray-700">
                        <p><strong class="text-gray-800">Rute:</strong> <span class="font-semibold text-sky-700">{{ $schedule->travelRoute->origin }} - {{ $schedule->travelRoute->destination }}</span></p>
                        <p><strong class="text-gray-800">Waktu Keberangkatan:</strong> <span class="font-semibold text-sky-700">{{ \Carbon\Carbon::parse($schedule->departure_time)->format('d F Y H:i') }} WIB</span></p>
                        @if(request('booking_type') == 'passenger')
                            <p><strong class="text-gray-800">Harga per Orang:</strong> <span class="font-bold text-sky-600">Rp {{ number_format($schedule->travelRoute->price_per_person, 0, ',', '.') }}</span></p>
                        @else
                            <p><strong class="text-gray-800">Harga per Kg:</strong> <span class="font-bold text-sky-600">Rp {{ number_format($schedule->price_per_kg, 0, ',', '.') }}</span></p>
                        @endif
                    </div>
                </div>

                {{-- Section: Informasi Penumpang/Barang (Background putih) --}}
                <div class="p-6 md:p-8 bg-white rounded-xl border-2 border-dashed border-sky-300">
                    @if(request('booking_type') == 'passenger')
                        <input type="hidden" name="num_passengers" value="{{ request('num_passengers') }}">
                        <h3 class="text-2xl md:text-3xl font-bold mb-7 text-sky-800 flex items-center">
                            <i class="fas fa-user-friends mr-4 text-sky-600"></i> Informasi Penumpang ({{ request('num_passengers') }} orang)
                        </h3>
                        @for ($i = 0; $i < request('num_passengers'); $i++)
                            <div class="border border-dashed border-sky-200 rounded-lg p-5 mb-5 bg-white shadow-sm">
                                <h4 class="font-bold mb-4 text-xl text-sky-700 flex items-center">
                                    <i class="fas fa-user-circle mr-3 text-sky-500"></i> Penumpang {{ $i + 1 }}
                                </h4>
                                <div class="space-y-4">
                                    <div>
                                        <label for="passenger_name_{{ $i }}" class="block text-gray-700 text-base font-semibold mb-2">Nama Lengkap:</label>
                                        <input type="text" name="passengers[{{ $i }}][name]" id="passenger_name_{{ $i }}" class="w-full py-3 px-5 border border-dashed border-sky-300 rounded-lg text-gray-800 focus:ring-4 focus:ring-sky-200 focus:border-sky-500 transition duration-300 placeholder-gray-400" placeholder="Masukkan nama lengkap" required>
                                    </div>
                                    <div>
                                        <label for="passenger_id_number_{{ $i }}" class="block text-gray-700 text-base font-semibold mb-2">Nomor Identitas (KTP - 16 Digit Angka, Opsional):</label>
                                        <input type="text" name="passengers[{{ $i }}][id_number]" id="passenger_id_number_{{ $i }}" class="w-full py-3 px-5 border border-dashed border-sky-300 rounded-lg text-gray-800 focus:ring-4 focus:ring-sky-200 focus:border-sky-500 transition duration-300 placeholder-gray-400" placeholder="Contoh: 1234567890123456" pattern="[0-9]{16}" title="Nomor identitas harus 16 digit angka (contoh: KTP)">
                                        <p class="text-sm text-gray-500 mt-1">Harus 16 digit angka (misal: Nomor KTP).</p>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    @else
                        <h3 class="text-2xl md:text-3xl font-bold mb-7 text-sky-800 flex items-center">
                            <i class="fas fa-box-open mr-4 text-sky-600"></i> Informasi Barang Kiriman
                        </h3>
                        <div class="space-y-6">
                            <div>
                                <label for="item_description" class="block text-gray-700 text-base font-semibold mb-2">Deskripsi Barang:</label>
                                <textarea name="item_description" id="item_description" rows="4" class="w-full py-3 px-5 border border-dashed border-sky-300 rounded-lg text-gray-800 focus:ring-4 focus:ring-sky-200 focus:border-sky-500 transition duration-300 placeholder-gray-400" placeholder="Contoh: Dokumen penting, pakaian, alat elektronik" required></textarea>
                            </div>
                            <div>
                                <label for="item_weight_kg" class="block text-gray-700 text-base font-semibold mb-2">Berat Barang (kg):</label>
                                <input type="number" name="item_weight_kg" id="item_weight_kg" step="0.1" min="0.1" class="w-full py-3 px-5 border border-dashed border-sky-300 rounded-lg text-gray-800 focus:ring-4 focus:ring-sky-200 focus:border-sky-500 transition duration-300 placeholder-gray-400" placeholder="Contoh: 5.5" required>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Kolom Kanan: Informasi Kontak Anda --}}
            <div class="lg:w-1/2"> {{-- Ambil setengah lebar di lg ke atas --}}
                <div class="p-6 md:p-8 bg-white rounded-xl border-2 border-dashed border-sky-300">
                    <h3 class="text-2xl md:text-3xl font-bold mb-7 text-sky-800 flex items-center">
                        <i class="fas fa-address-book mr-4 text-sky-600"></i> Informasi Kontak Anda
                    </h3>
                    <div class="space-y-6">
                        <div>
                            <label for="customer_name" class="block text-gray-700 text-base font-semibold mb-2">Nama Lengkap:</label>
                            <input type="text" name="customer_name" id="customer_name" class="w-full py-3 px-5 border border-dashed border-sky-300 rounded-lg text-gray-800 focus:ring-4 focus:ring-sky-200 focus:border-sky-500 transition duration-300 placeholder-gray-400" placeholder="Masukkan nama lengkap Anda" required>
                        </div>
                        <div>
                            <label for="customer_email" class="block text-gray-700 text-base font-semibold mb-2">Email:</label>
                            <input type="email" name="customer_email" id="customer_email" class="w-full py-3 px-5 border border-dashed border-sky-300 rounded-lg text-gray-800 focus:ring-4 focus:ring-sky-200 focus:border-sky-500 transition duration-300 placeholder-gray-400" placeholder="Alamat email Anda" required>
                        </div>
                        <div>
                            <label for="customer_phone" class="block text-gray-700 text-base font-semibold mb-2">Nomor Telepon (WhatsApp):</label>
                            <input type="tel" name="customer_phone" id="customer_phone" class="w-full py-3 px-5 border border-dashed border-sky-300 rounded-lg text-gray-800 focus:ring-4 focus:ring-sky-200 focus:border-sky-500 transition duration-300 placeholder-gray-400" placeholder="Contoh: 081234567890" required>
                            <p class="text-sm text-gray-500 mt-1">Nomor akan otomatis diformat menjadi 62xxxx.</p> {{-- Tambahan helper text --}}
                        </div>
                        <div>
                            <label for="pickup_address" class="block text-gray-700 text-base font-semibold mb-2">Alamat Penjemputan:</label>
                            <textarea name="pickup_address" id="pickup_address" rows="3" class="w-full py-3 px-5 border border-dashed border-sky-300 rounded-lg text-gray-800 focus:ring-4 focus:ring-sky-200 focus:border-sky-500 transition duration-300 placeholder-gray-400" placeholder="Alamat lengkap penjemputan" required></textarea>
                            {{-- Keterangan baru --}}
                            <p class="text-sm text-gray-500 mt-1">
                                Mohon masukkan alamat penjemputan dengan lengkap dan jelas (termasuk nama jalan, nomor rumah/bangunan, dan kota/kabupaten) atau gunakan patokan yang mudah ditemukan di Google Maps.<br>
                                Contoh: <b>Alfamart Cigugur, Jl. Raya Cigugur No. 123, Pangandaran</b>
                            </p>
                        </div>
                        <div>
                            <label for="dropoff_address" class="block text-gray-700 text-base font-semibold mb-2">Alamat Tujuan/Pengantaran:</label>
                            <textarea name="dropoff_address" id="dropoff_address" rows="3" class="w-full py-3 px-5 border border-dashed border-sky-300 rounded-lg text-gray-800 focus:ring-4 focus:ring-sky-200 focus:border-sky-500 transition duration-300 placeholder-gray-400" placeholder="Alamat lengkap tujuan/pengantaran" required></textarea>
                            {{-- Keterangan baru --}}
                            <p class="text-sm text-gray-500 mt-1">
                                Mohon masukkan alamat tujuan dengan lengkap dan jelas (termasuk nama jalan, nomor rumah/bangunan, dan kota/kabupaten) atau gunakan patokan yang mudah ditemukan di Google Maps.<br>
                                Contoh: <b>Rumah Sakit Umum Daerah, Jl. Merdeka No. 45, Bandung</b>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit Button (diluar kolom, di tengah) --}}
        <div class="text-center mt-10 max-w-6xl mx-auto">
            <button type="submit" id="submit-button" class="bg-sky-500 hover:bg-sky-600 text-white font-extrabold py-4 px-10 rounded-full text-xl transition duration-300 ease-in-out shadow-lg transform hover:scale-105 flex items-center justify-center mx-auto space-x-3">
                <span id="button-text">Lanjutkan Pemesanan</span>
                <svg id="loading-spinner-btn" class="animate-spin h-6 w-6 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- Loading Overlay Logic (for initial page load) ---
        const loadingOverlay = document.getElementById('loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.style.opacity = '0';
            setTimeout(() => {
                loadingOverlay.style.display = 'none';
            }, 300);
        }

        const bookingForm = document.getElementById('booking-form');
        const submitButton = document.getElementById('submit-button');
        const buttonText = document.getElementById('button-text');
        const loadingSpinnerBtn = document.getElementById('loading-spinner-btn');

        if (bookingForm && submitButton) {
            bookingForm.addEventListener('submit', () => {
                buttonText.textContent = 'Memproses...';
                loadingSpinnerBtn.classList.remove('hidden');
                submitButton.disabled = true;
                submitButton.classList.add('opacity-75', 'cursor-not-allowed');
            });
        }

        // --- Nomor Telepon Formatting Logic ---
        const customerPhoneInput = document.getElementById('customer_phone');

        if (customerPhoneInput) {
            customerPhoneInput.addEventListener('input', function(event) {
                let phoneNumber = event.target.value;

                // 1. Hapus semua karakter non-digit
                phoneNumber = phoneNumber.replace(/\D/g, '');

                // 2. Tangani awalan +62 atau 08
                if (phoneNumber.startsWith('0')) {
                    phoneNumber = '62' + phoneNumber.substring(1); // Ganti 0 dengan 62
                } else if (phoneNumber.startsWith('+62')) {
                    phoneNumber = '62' + phoneNumber.substring(3); // Hapus +62
                } else if (!phoneNumber.startsWith('62') && phoneNumber.length > 0) {
                    // Jika tidak diawali 0, +62, atau 62, asumsikan dimulai dengan angka kedua dari 08
                    // Contoh: pengguna langsung ketik 812...
                    // Atau jika diawali dengan 62 tapi ada kesalahan penulisan, biarkan saja
                    // Ini adalah asumsi paling aman jika tidak ada 0 atau +62.
                    // Jika Anda ingin lebih ketat, bisa menambahkan validasi lebih lanjut.
                }

                event.target.value = phoneNumber;
            });

            // Tambahkan event listener untuk paste juga
            customerPhoneInput.addEventListener('paste', function(event) {
                // Berikan sedikit waktu agar nilai paste masuk ke input sebelum diproses
                setTimeout(() => {
                    let phoneNumber = event.target.value;
                    phoneNumber = phoneNumber.replace(/\D/g, ''); // Hapus semua non-digit
                    if (phoneNumber.startsWith('0')) {
                        phoneNumber = '62' + phoneNumber.substring(1);
                    } else if (phoneNumber.startsWith('+62')) {
                        phoneNumber = '62' + phoneNumber.substring(3);
                    }
                    event.target.value = phoneNumber;
                }, 0);
            });
        }
        // --- End Nomor Telepon Formatting Logic ---
    });
</script>

<style>
    /* Gradient text untuk judul utama */
    .algiotrans-title-text-white {
        background: linear-gradient(45deg, #FFFFFF, #E0F2F7, #C1E4EE);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-family: 'Montserrat', sans-serif;
        font-weight: 800;
        line-height: 1.2;
    }
    /* Jika Anda ingin warna teksnya benar-benar putih solid, gunakan ini: */
    /* .algiotrans-title-text-white {
        color: #FFFFFF;
        font-family: 'Montserrat', sans-serif;
        font-weight: 800;
        line-height: 1.2;
    } */

    /* Basic spinner animation (from konfirmasi.blade.php) */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .animate-spin {
        animation: spin 1s linear infinite;
    }
     .algiotrans-title-text {
            background: linear-gradient(45deg, #87CEEB, #A0D8EF, #B2EBF2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            line-height: 1.2;
        }
</style>
@endsection