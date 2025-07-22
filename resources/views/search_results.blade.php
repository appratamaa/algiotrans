@extends('layouts.app')

@section('title', 'Jadwal Tersedia')

@section('content')
    <div class="container mx-auto px-4 py-8 relative z-10">
        <h2 class="text-3xl font-bold text-center mb-8 text-sky-700 algiotrans-title-text">Jadwal Tersedia untuk {{ $route->origin }} - {{ $route->destination }}</h2>

        @if (session('errors'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-lg relative mb-6 shadow-sm" role="alert">
                <strong class="font-bold">Oops!</strong>
                <span class="block sm:inline">{{ session('errors')->first('message') }}</span>
            </div>
        @endif

        {{-- Skeleton Loader Container --}}
        <div id="skeleton-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @for ($i = 0; $i < 4; $i++) {{-- Tampilkan beberapa skeleton card --}}
                <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-sky-200 animate-pulse">
                    <div class="mb-4 bg-gray-200 h-40 w-full rounded-lg"></div> {{-- Placeholder for image --}}
                    <div class="flex flex-col items-center mb-4">
                        <div class="h-6 bg-gray-200 rounded w-3/4 mb-2"></div> {{-- Placeholder for date --}}
                        <div class="h-5 bg-gray-200 rounded w-1/2"></div> {{-- Placeholder for time --}}
                    </div>
                    <hr class="border-t border-gray-200 my-4">
                    <div class="h-4 bg-gray-200 rounded w-5/6 mb-2"></div> {{-- Placeholder for route --}}
                    <div class="h-4 bg-gray-200 rounded w-2/3 mb-2"></div> {{-- Placeholder for seats/price --}}
                    <div class="h-4 bg-gray-200 rounded w-1/2 mb-4"></div> {{-- Placeholder for price per person/kg --}}
                    <div class="h-6 bg-gray-200 rounded w-3/4 mx-auto"></div> {{-- Placeholder for total price --}}
                    <div class="text-center mt-5">
                        <div class="h-10 bg-gray-200 rounded-full w-2/3 mx-auto"></div> {{-- Placeholder for button --}}
                    </div>
                </div>
            @endfor
        </div>

        {{-- Content Asli (Disembunyikan secara default) --}}
        <div id="content-container" class="hidden">
            @php
                // Filter jadwal berdasarkan ketersediaan kursi (hanya untuk tipe penumpang)
                $availableSchedules = $schedules->filter(function ($schedule) use ($request) {
                    if ($request->booking_type === 'passenger') {
                        return $schedule->available_seats > 0;
                    }
                    // Untuk item_delivery, asumsikan selalu tersedia jika ada jadwal
                    // Anda bisa menambahkan logika pengecekan ketersediaan slot barang di sini jika ada.
                    return true;
                });
            @endphp

            @if ($availableSchedules->isEmpty())
                {{-- Pesan Cantik untuk Jadwal Tidak Tersedia --}}
                <div id="no-schedules-message" class="text-center bg-white p-8 md:p-10 rounded-3xl shadow-lg border-2 border-sky-300 max-w-2xl mx-auto">
                    <i class="fas fa-box-open text-sky-400 text-6xl mb-4 animate-bounce-custom"></i>
                    <h3 class="text-3xl font-bold text-gray-800 mb-3">Mohon Maaf, Jadwal Tidak Tersedia</h3>
                    <p class="text-lg text-gray-600 mb-4">
                        Sepertinya belum ada perjalanan yang tersedia atau semua kursi/slot sudah terisi untuk rute <b>{{ $route->origin }} - {{ $route->destination }}</b> pada tanggal <b>{{ \Carbon\Carbon::parse($request->departure_date)->format('d F Y') }}</b>.
                    </p>
                    <p class="text-md text-gray-500">
                        Anda bisa mencoba mencari jadwal untuk tanggal atau rute lain.
                    </p>
                    <a href="{{ route('home') }}" class="mt-6 inline-block bg-sky-500 text-white font-semibold py-3 px-6 rounded-full hover:bg-sky-600 transition duration-300 ease-in-out shadow-md hover:shadow-lg">
                        <i class="fas fa-search mr-2"></i> Cari Jadwal Lain
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($availableSchedules as $schedule)
                        {{-- Tambahkan data-schedule-id dan data-schedule-url-id untuk JavaScript --}}
                        <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-sky-200 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
                             data-aos="fade-up"
                             data-schedule-id="{{ $schedule->id }}"
                             data-schedule-url-id="{{ $schedule->id }}" {{-- Gunakan ID untuk URL --}}
                             data-booking-type="{{ $request->booking_type ?? '' }}" {{-- Tambahkan tipe booking --}}
                             data-num-passengers="{{ $request->num_passengers ?? 0 }}" {{-- Tambahkan jumlah penumpang --}}
                             data-total-weight-kg="{{ $request->total_weight_kg ?? 0 }}"> {{-- Tambahkan total berat --}}

                            @php
                                // BARIS PERUBAHAN UTAMA: Sediakan gambar inisial jika car_layout_image kosong
                                $carLayoutImage = $schedule->car_layout_image
                                    ? asset('storage/' . $schedule->car_layout_image)
                                    : 'https://ui-avatars.com/api/?name=Algio+Trans&background=87CEEB&color=fff&size=256&font-size=0.5&bold=true';
                                    // URL untuk gambar inisial "AT" dari ui-avatars.com
                                    // name=Algio+Trans -> Akan mengambil inisial dari "Algio Trans" yaitu "AT"
                                    // background=87CEEB -> Warna background Sky Blue (sama dengan branding Anda)
                                    // color=fff -> Warna teks putih
                                    // size=256 -> Ukuran gambar (px)
                                    // font-size=0.5 -> Ukuran font relatif terhadap ukuran gambar (50%)
                                    // bold=true -> Teks tebal
                            @endphp
                            <div class="mb-4">
                                <img src="{{ $carLayoutImage }}" alt="Layout Mobil" class="w-full h-40 object-cover rounded-lg border-2 border-sky-300 shadow-md">
                            </div>

                            <div class="flex flex-col items-center mb-4">
                                <h3 class="text-xl font-bold text-gray-800 text-center">
                                    {{ \Carbon\Carbon::parse($schedule->departure_time)->format('d F Y') }}
                                </h3>
                                <p class="text-sky-600 font-semibold text-center text-lg">{{ \Carbon\Carbon::parse($schedule->departure_time)->format('H:i') }} WIB</p>
                            </div>
                            <hr class="border-t border-sky-100 my-4">
                            <p class="text-gray-700 mb-2 text-center text-sm">
                                <i class="fas fa-route text-sky-400 mr-2"></i> Rute: <span class="font-semibold">{{ $schedule->travelRoute->origin }}</span> <i class="fas fa-arrow-right text-sky-400 mx-1"></i> <span class="font-semibold">{{ $schedule->travelRoute->destination }}</span>
                            </p>

                            @if (isset($request) && $request->booking_type === 'passenger')
                                <p class="text-gray-700 mb-2 text-center text-sm">
                                    <i class="fas fa-users text-sky-400 mr-2"></i> Sisa Kursi: <span class="font-bold text-sky-600" id="available-seats-{{ $schedule->id }}">{{ $schedule->available_seats }}</span>
                                </p>
                                <p class="text-gray-700 mb-2 text-center text-sm">
                                    <i class="fas fa-tag text-sky-400 mr-2"></i> Harga per Orang: <span class="font-bold text-sky-600">Rp {{ number_format($schedule->travelRoute->price_per_person, 0, ',', '.') }}</span>
                                </p>
                                <p class="text-lg font-bold text-sky-700 text-center mt-4">
                                    <i class="fas fa-money-bill-wave text-sky-500 mr-2"></i> Total Perkiraan: Rp
                                    {{ number_format($schedule->estimated_total_price, 0, ',', '.') }}
                                </p>
                                <div class="text-center mt-5">
                                    <a href="{{ route('booking.detail', $schedule->id) }}?booking_type=passenger&num_passengers={{ $request->num_passengers }}"
                                        class="inline-block bg-sky-500 text-white font-semibold py-3 px-6 rounded-full hover:bg-sky-600 transition duration-300 ease-in-out shadow-md hover:shadow-lg action-button-{{ $schedule->id }}">
                                        <i class="fas fa-ticket-alt mr-2"></i> Pesan Tiket
                                    </a>
                                </div>
                            @else
                                <p class="text-gray-700 mb-2 text-center text-sm">
                                    <i class="fas fa-weight-hanging text-sky-400 mr-2"></i> Harga per Kg: <span class="font-bold text-sky-600">Rp {{ number_format($schedule->price_per_kg, 0, ',', '.') }}</span>
                                </p>
                                {{-- <p class="text-lg font-bold text-sky-700 text-center mt-4">
                                    <i class="fas fa-money-bill-wave text-sky-500 mr-2"></i> Total Perkiraan: Rp
                                    {{ number_format($schedule->estimated_total_price, 0, ',', '.') }}
                                </p> --}}
                                <div class="text-center mt-5">
                                    <a href="{{ route('booking.detail', $schedule->id) }}?booking_type=item_delivery&total_weight_kg={{ $request->total_weight_kg }}"
                                        class="inline-block bg-sky-500 text-white font-semibold py-3 px-6 rounded-full hover:bg-sky-600 transition duration-300 ease-in-out shadow-md hover:shadow-lg action-button-{{ $schedule->id }}">
                                        <i class="fas fa-truck-moving mr-2"></i> Kirim Barang
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 1000,
                once: true,
            });

            const skeletonContainer = document.getElementById('skeleton-container');
            const contentContainer = document.getElementById('content-container');
            const noSchedulesMessage = document.getElementById('no-schedules-message');


            // Simulate loading time for demonstration (remove in production if not needed)
            setTimeout(() => {
                if (skeletonContainer) {
                    skeletonContainer.classList.add('hidden');
                }
                if (contentContainer) {
                    contentContainer.classList.remove('hidden');
                }
            }, 500);

            // --- Realtime Polling for Available Seats ---
            const scheduleCards = document.querySelectorAll('[data-schedule-id]');
            const pollingInterval = 5000;

            if (scheduleCards.length > 0) {
                async function fetchAvailableSeats(scheduleId) {
                    try {
                        const response = await fetch(`/api/schedule-seats/${scheduleId}`);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        const data = await response.json();
                        return data.available_seats;
                    } catch (error) {
                        console.error('Gagal mengambil data kursi untuk jadwal ID:', scheduleId, error);
                        return null;
                    }
                }

                async function updateScheduleUI() {
                    let hasVisibleSchedules = false;
                    for (const card of scheduleCards) {
                        const scheduleId = card.dataset.scheduleId;
                        const scheduleUrlId = card.dataset.scheduleUrlId;
                        const bookingType = card.dataset.bookingType;
                        const numPassengers = card.dataset.numPassengers;
                        const totalWeightKg = card.dataset.totalWeightKg;

                        const availableSeatsSpan = card.querySelector(`#available-seats-${scheduleId}`);
                        const actionButton = card.querySelector(`.action-button-${scheduleId}`);

                        if (bookingType === 'passenger') {
                            const newAvailableSeats = await fetchAvailableSeats(scheduleId);

                            if (newAvailableSeats !== null) {
                                availableSeatsSpan.textContent = newAvailableSeats;

                                if (newAvailableSeats <= 0) {
                                    card.style.display = 'none';
                                } else {
                                    card.style.display = 'block';
                                    hasVisibleSchedules = true;
                                    if (actionButton) {
                                        actionButton.classList.remove('opacity-50', 'cursor-not-allowed');
                                        actionButton.innerHTML = `<i class="fas fa-ticket-alt mr-2"></i> Pesan Tiket`;
                                        actionButton.setAttribute('href', `/booking/detail/${scheduleUrlId}?booking_type=passenger&num_passengers=${numPassengers}`);
                                    }
                                    availableSeatsSpan.classList.add('text-sky-600');
                                    availableSeatsSpan.classList.remove('text-red-500');
                                }
                            }
                        } else {
                            card.style.display = 'block';
                            hasVisibleSchedules = true;
                        }
                    }

                    if (noSchedulesMessage) {
                        if (!hasVisibleSchedules) {
                            noSchedulesMessage.classList.remove('hidden');
                        } else {
                            noSchedulesMessage.classList.add('hidden');
                        }
                    }
                }

                updateScheduleUI();
                setInterval(updateScheduleUI, pollingInterval);
            } else {
                if (noSchedulesMessage) {
                    noSchedulesMessage.classList.remove('hidden');
                }
            }
        });
    </script>

    <style>
        .algiotrans-title-text {
            background: linear-gradient(45deg, #87CEEB, #A0D8EF, #B2EBF2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            line-height: 1.2;
        }

        /* Basic skeleton animation */
        @keyframes pulse {
            0% { opacity: 0.7; }
            50% { opacity: 1; }
            100% { opacity: 0.7; }
        }
        .animate-pulse {
            animation: pulse 1.5s infinite ease-in-out;
        }

        /* Custom bounce animation for "No schedules" icon */
        @keyframes bounce-custom {
            0%, 100% {
                transform: translateY(0);
                animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
            }
            50% {
                transform: translateY(-25%);
                animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
            }
        }
        .animate-bounce-custom {
            animation: bounce-custom 1s infinite;
        }
    </style>
@endsection