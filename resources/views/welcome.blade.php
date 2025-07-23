@extends('layouts.app')

@section('title', 'Pesan Tiket & Kirim Paket | Algio Trans')

@section('content')
    {{-- Skeleton Loader Overlay (Add your skeleton elements here) --}}
    <div id="skeleton-overlay" class="fixed inset-0 bg-white z-50 flex items-center justify-center transition-opacity duration-700 opacity-0 hidden">
        <div class="space-y-6 w-3/4 max-w-2xl">
            <div class="h-10 bg-gray-200 rounded animate-pulse"></div>
            <div class="h-6 bg-gray-200 rounded w-5/6 animate-pulse"></div>
            <div class="grid grid-cols-3 gap-4">
                <div class="h-24 bg-gray-200 rounded animate-pulse"></div>
                <div class="h-24 bg-gray-200 rounded animate-pulse"></div>
                <div class="h-24 bg-gray-200 rounded animate-pulse"></div>
            </div>
            <div class="h-12 bg-gray-200 rounded w-1/2 mx-auto animate-pulse"></div>
        </div>
    </div>

    <div class="relative min-h-screen">
        <div class="container mx-auto px-6 lg:px-8 py-16 relative z-10">
            {{-- Hero Section --}}
            <section class="text-center py-16 mb-12 transform transition-all duration-700 ease-in-out">
                <h1 class="text-4xl font-extrabold mb-0 leading-tight animate-fade-in-up">
                    {{-- Adjusted text size for mobile (sm:text-5xl) and ensured inline-block for typed text --}}
                    <span id="typed-text" class="block text-center whitespace-pre-wrap text-3xl sm:text-4xl md:text-5xl" style="min-height: 1.2em; display: inline-block; vertical-align: top;"></span>
                    <span class="block text-blue-400 text-center text-3xl sm:text-4xl md:text-5xl">Mudah Bersama Algio Trans</span>
                </h1>
                <p class="text-xl md:text-2xl mt-4 mb-10 text-gray-600 animate-fade-in delay-200 leading-relaxed">
                    Pesan tiket travel atau kirim paket dengan <b class="text-gray-700">cepat, aman, dan tanpa repot.</b>
                </p>

                {{-- Bagian Layanan dengan Ikon (Carousel) --}}
                <div class="mb-10 animate-fade-in delay-300 overflow-hidden relative">
                    <p class="text-xl md:text-2xl font-semibold mb-6 text-gray-700">Kami melayani:</p>
                    <div class="flex flex-nowrap carousel-container" id="services-carousel">
                        @php
                            $services = [
                                ['icon' => 'fas fa-route', 'color' => 'text-blue-400', 'name' => 'Door-to-Door'],
                                ['icon' => 'fas fa-car', 'color' => 'text-orange-500', 'name' => 'Carter Drop'],
                                ['icon' => 'fas fa-store', 'color' => 'text-purple-500', 'name' => 'Offline'],
                                ['icon' => 'fab fa-uber', 'color' => 'text-yellow-500', 'name' => 'GrabCar/GoCar'],
                                ['icon' => 'fas fa-box-tissue', 'color' => 'text-teal-500', 'name' => 'Titip Paket'],
                                ['icon' => 'fas fa-car-side', 'color' => 'text-red-500', 'name' => 'Rental + Driver'],
                            ];
                        @endphp
                        {{-- Duplicate services for seamless loop --}}
                        @foreach (array_merge($services, $services) as $service)
                            <div class="flex-none w-48 mx-2 p-4 border-2 border-dashed border-blue-200 rounded-xl flex flex-col items-center text-center text-gray-700 text-lg transition-transform duration-300 ease-in-out service-card">
                                <i class="{{ $service['icon'] }} text-4xl mb-2 {{ $service['color'] }}"></i>
                                <span class="font-bold">{{ $service['name'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <a href="#booking-form" id="hero-cta"
                    class="inline-block bg-blue-400 text-white font-bold py-4 px-10 rounded-full text-xl
                           hover:bg-blue-500 transform hover:scale-105 transition duration-300 ease-in-out
                           animate-bounce-once">
                    Pesan Sekarang!
                </a>
            </section>

            {{-- Booking Form Section --}}
            <section id="booking-form"
                class="bg-white p-8 md:p-12 rounded-2xl mb-16 relative
                                            transform transition-all duration-500 ease-in-out
                                            border-2 border-dashed border-blue-300 opacity-0 translate-y-10"
                data-aos="fade-up">
                <h2 class="text-4xl font-bold text-center mb-10 text-blue-400">Pesan Tiket atau Kirim Paket</h2>

                <form action="{{ route('search.schedules') }}" method="POST" class="space-y-8 relative" id="booking-form-element">
                    @csrf

                    <div class="flex flex-col md:flex-row justify-center gap-6 mb-8">
                        <button type="button" id="btn-passenger"
                            class="flex-1 px-8 py-4 rounded-xl text-xl font-semibold border-2 border-blue-300
                                   text-blue-400 hover:bg-blue-400 hover:text-white transition duration-300
                                   focus:outline-none focus:ring-4 focus:ring-blue-200 active:scale-98">
                            <i class="fas fa-user-friends mr-3"></i> Penumpang
                        </button>
                        <button type="button" id="btn-item-delivery"
                            class="flex-1 px-8 py-4 rounded-xl text-xl font-semibold border-2 border-blue-300
                                   text-blue-400 hover:bg-blue-400 hover:text-white transition duration-300
                                   focus:outline-none focus:ring-4 focus:ring-blue-200 active:scale-98">
                            <i class="fas fa-box-open mr-3"></i> Barang
                        </button>
                        <input type="hidden" name="booking_type" id="booking_type" value="">
                    </div>

                    {{-- Form Fields Wrapper --}}
                    <div id="form-fields-wrapper" class="relative transition-all duration-500 ease-out">
                        {{-- Passenger Fields --}}
                        <div id="passenger-fields"
                            class="grid grid-cols-1 md:grid-cols-3 gap-8 absolute w-full transition-all duration-500 ease-out top-0 left-0 p-0">
                            <div>
                                <label for="route_id_passenger" class="block text-gray-700 text-lg font-bold mb-3">
                                    <i class="fas fa-map-marker-alt mr-2 text-blue-400"></i> Tujuan:
                                </label>
                                <select name="route_id" id="route_id_passenger"
                                    class="w-full py-3 px-5 border border-gray-300 rounded-lg text-gray-800 leading-tight
                                           focus:outline-none">
                                    <option value="">Pilih Tujuan</option>
                                    @foreach ($routes as $route)
                                        <option value="{{ $route->id }}">{{ $route->origin }} - {{ $route->destination }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="departure_date_passenger" class="block text-gray-700 text-lg font-bold mb-3">
                                    <i class="fas fa-calendar-alt mr-2 text-blue-400"></i> Tanggal Keberangkatan:
                                </label>
                                <input type="date" name="departure_date" id="departure_date_passenger"
                                    class="w-full py-3 px-5 border border-gray-300 rounded-lg text-gray-800 leading-tight
                                           focus:outline-none">
                            </div>
                            <div>
                                <label for="num_passengers" class="block text-gray-700 text-lg font-bold mb-3">
                                    <i class="fas fa-users mr-2 text-blue-400"></i> Jumlah Penumpang:
                                </label>
                                <input type="number" name="num_passengers" id="num_passengers" min="1"
                                    max="7"
                                    class="w-full py-3 px-5 border border-gray-300 rounded-lg text-gray-800 leading-tight
                                           focus:outline-none">
                            </div>
                        </div>

                        {{-- Item Delivery Fields --}}
                        <div id="item-delivery-fields"
                            class="grid grid-cols-1 md:grid-cols-2 gap-8 absolute w-full transition-all duration-500 ease-out top-0 left-full p-0 opacity-0 pointer-events-none">
                            <div>
                                <label for="route_id_item_delivery" class="block text-gray-700 text-lg font-bold mb-3">
                                    <i class="fas fa-map-marker-alt mr-2 text-blue-400"></i> Tujuan Pengiriman:
                                </label>
                                <select name="route_id" id="route_id_item_delivery"
                                    class="w-full py-3 px-5 border border-gray-300 rounded-lg text-gray-800 leading-tight
                                           focus:outline-none">
                                    <option value="">Pilih Tujuan</option>
                                    @foreach ($routes as $route)
                                        <option value="{{ $route->id }}">{{ $route->origin }} - {{ $route->destination }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="departure_date_item_delivery"
                                    class="block text-gray-700 text-lg font-bold mb-3">
                                    <i class="fas fa-calendar-alt mr-2 text-blue-400"></i> Tanggal Pengiriman:
                                </label>
                                <input type="date" name="departure_date" id="departure_date_item_delivery"
                                    class="w-full py-3 px-5 border border-gray-300 rounded-lg text-gray-800 leading-tight
                                           focus:outline-none"
                                    min="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div> {{-- End Form Fields Wrapper --}}


                    <div class="text-center mt-10">
                        <button type="submit" id="search-schedule-btn"
                            class="p-1 inline-block bg-gradient-to-r from-blue-400 to-blue-600 text-white font-bold py-4 px-12
                                   rounded-full text-xl shadow-lg hover:from-blue-500 hover:to-blue-700
                                   transform hover:scale-105 transition duration-300 ease-in-out
                                   focus:outline-none focus:ring-4 focus:ring-blue-200 animate-pulse-once-on-hover
                                   relative z-20">
                            Cari Jadwal <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </form>
            </section>

            {{-- Key Features Section --}}
            <section class="mb-16 opacity-0 translate-y-10" data-aos="fade-up">
                <h2 class="text-4xl font-bold text-center mb-10 text-blue-400">
                    Kenapa Memilih Algio Trans?
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <div
                        class="bg-white p-8 rounded-2xl text-center border-2 border-dashed border-blue-200
                                transform hover:translate-y-[-5px] transition duration-300 ease-in-out">
                        <div class="text-5xl text-blue-400 mb-5">
                            <i class="fas fa-map-marked-alt animate-float"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-4 text-gray-800">Rute Terlengkap</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Jelajahi berbagai pilihan rute perjalanan dan pengiriman barang ke seluruh tujuan favorit Anda.
                        </p>
                    </div>
                    <div
                        class="bg-white p-8 rounded-2xl text-center border-2 border-dashed border-blue-200
                                transform hover:translate-y-[-5px] transition duration-300 ease-in-out">
                        <div class="text-5xl text-blue-400 mb-5">
                            <i class="fas fa-calendar-check animate-wiggle"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-4 text-gray-800">Jadwal Fleksibel</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Pilih jadwal keberangkatan atau pengiriman yang paling sesuai dengan kebutuhan Anda.
                        </p>
                    </div>
                    <div
                        class="bg-white p-8 rounded-2xl text-center border-2 border-dashed border-blue-200
                                transform hover:translate-y-[-5px] transition duration-300 ease-in-out">
                        <div class="text-5xl text-blue-400 mb-5">
                            <i class="fas fa-tags animate-spin-slow"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-4 text-gray-800">Harga Terbaik</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Dapatkan penawaran harga paling kompetitif untuk perjalanan dan pengiriman Anda.
                        </p>
                    </div>
                </div>
            </section>

            {{-- How It Works Section --}}
            <section
                class="mb-16 bg-white p-10 rounded-2xl
                            opacity-0 translate-y-10"
                data-aos="fade-up">
                <h2 class="text-4xl font-bold text-center mb-10 text-blue-400">Bagaimana Cara Kerjanya?</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                    <div class="flex flex-col items-center">
                        <div
                            class="relative w-24 h-24 flex items-center justify-center bg-blue-300 rounded-full mb-6
                                    text-white text-4xl font-bold shadow-md animate-pop-in">
                            1
                            <div class="absolute inset-0 border-4 border-blue-400 rounded-full animate-ping-once"></div>
                        </div>
                        <h3 class="text-2xl font-semibold mb-3 text-gray-800">Pilih Layanan Anda</h3>
                        <p class="text-gray-600">Pilih apakah Anda ingin memesan tiket penumpang atau mengirim paket.</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <div
                            class="relative w-24 h-24 flex items-center justify-center bg-blue-300 rounded-full mb-6
                                    text-white text-4xl font-bold shadow-md animate-pop-in delay-100">
                            2
                            <div
                                class="absolute inset-0 border-4 border-blue-400 rounded-full animate-ping-once delay-100">
                            </div>
                        </div>
                        <h3 class="text-2xl font-semibold mb-3 text-gray-800">Isi Detail Perjalanan/Pengiriman</h3>
                        <p class="text-gray-600">Masukkan tujuan dan tanggal keberangkatan/pengiriman Anda.</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <div
                            class="relative w-24 h-24 flex items-center justify-center bg-blue-300 rounded-full mb-6
                                    text-white text-4xl font-bold shadow-md animate-pop-in delay-200">
                            3
                            <div
                                class="absolute inset-0 border-4 border-4 border-blue-400 rounded-full animate-ping-once delay-200">
                            </div>
                        </div>
                        <h3 class="text-2xl font-semibold mb-3 text-gray-800">Temukan & Pesan!</h3>
                        <p class="text-gray-600">Cari jadwal yang tersedia dan selesaikan pemesanan Anda dengan mudah.</p>
                    </div>
                </div>
            </section>

            {{-- About Us Section (Moved from app.blade) --}}
            <section id="about-us" class="container mx-auto px-4 py-16 bg-white">
                <hr class="border-t-2 border-dashed border-blue-300 mx-auto max-w-7xl mb-8">
                <div class="text-center mb-12" data-aos="fade-up">
                    <h2 class="text-4xl font-extrabold section-title mb-4">Tentang Kami</h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Algio Trans hadir sebagai solusi perjalanan dan pengiriman barang yang aman, nyaman, dan terpercaya.
                        Kami berkomitmen untuk memberikan pelayanan terbaik bagi setiap pelanggan.
                    </p>
                </div>
                <div class="text-center grid grid-cols-1 md:grid-cols-2 gap-10 items-center max-w-5xl mx-auto mb-12">
                    <div>
                        <h3 class="text-3xl font-bold section-title mb-4">Misi Kami</h3>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            Menjadi pilihan utama dalam layanan transportasi dan logistik dengan mengedepankan keamanan,
                            ketepatan waktu, dan kepuasan pelanggan. Kami terus berinovasi untuk memenuhi kebutuhan pasar
                            yang dinamis.
                        </p>
                        <h3 class="text-3xl font-bold section-title mb-4">Visi Kami</h3>
                        <p class="text-gray-700 leading-relaxed">
                            Mewujudkan mobilitas yang efisien dan pengiriman yang andal melalui pemanfaatan teknologi
                            terkini dan sumber daya manusia yang profesional.
                        </p>
                    </div>
                    {{-- <div class="swiper image-carousel" data-aos="fade-left" data-aos-delay="200">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <img src="{{ asset('image/Mobil1.jpeg') }}" alt="Algio Trans Image 1"
                                    class="rounded-xl shadow-md">
                            </div>
                            <div class="swiper-slide">
                                <img src="{{ asset('image/Mobil2.jpeg') }}" alt="Algio Trans Image 2"
                                    class="rounded-xl shadow-md">
                            </div>
                            <div class="swiper-slide">
                                <img src="{{ asset('image/Mobil3.jpeg') }}" alt="Algio Trans Image 3"
                                    class="rounded-xl shadow-md">
                            </div>
                            <div class="swiper-slide">
                                <img src="{{ asset('image/Mobil4.jpeg') }}" alt="Algio Trans Image 4"
                                    class="rounded-xl shadow-md">
                            </div>
                            <div class="swiper-slide">
                                <img src="{{ asset('image/Mobil5.jpeg') }}" alt="Algio Trans Image 5"
                                    class="rounded-xl shadow-md">
                            </div>
                            <div class="swiper-slide">
                                <img src="{{ asset('image/Mobil6.jpeg') }}" alt="Algio Trans Image 6"
                                    class="rounded-xl shadow-md">
                            </div>
                        </div>
                        <div class="swiper-pagination"></div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>
                    </div> --}}
                </div>
            </section>

            {{-- Services Section (Moved from app.blade) --}}
            <section id="services" class="container mx-auto px-4 py-16">
                <hr class="border-t-2 border-dashed border-blue-300 mx-auto max-w-7xl mb-8">
                <div class="text-center mb-12" data-aos="fade-up">
                    <h2 class="text-4xl font-extrabold section-title mb-4">Layanan Kami</h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Algio Trans menyediakan berbagai layanan untuk memenuhi kebutuhan mobilitas dan logistik Anda.
                    </p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                    <div class="p-6 card-dashed-border flex flex-col items-center text-center" data-aos="fade-up"
                        data-aos-delay="100">
                        <div class="p-4 bg-blue-100 rounded-full mb-4">
                            <i class="fas fa-bus text-blue-600 text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold section-title mb-2">Travel Penumpang</h3>
                        <p class="text-gray-600">
                            Nikmati perjalanan yang nyaman dan aman dengan armada modern dan driver berpengalaman. Tersedia
                            untuk berbagai rute.
                        </p>
                    </div>
                    <div class="p-6 card-dashed-border flex flex-col items-center text-center" data-aos="fade-up"
                        data-aos-delay="200">
                        <div class="p-4 bg-blue-100 rounded-full mb-4">
                            <i class="fas fa-box text-blue-600 text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold section-title mb-2">Pengiriman Barang</h3>
                        <p class="text-gray-600">
                            Kirim paket Anda dengan cepat dan terjamin keamanannya. Cocok untuk dokumen penting hingga
                            barang berukuran sedang.
                        </p>
                    </div>
                    <div class="p-6 card-dashed-border flex flex-col items-center text-center" data-aos="fade-up"
                        data-aos-delay="300">
                        <div class="p-4 bg-blue-100 rounded-full mb-4">
                            <i class="fas fa-headset text-blue-600 text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold section-title mb-2">Dukungan Pelanggan 24/7</h3>
                        <p class="text-gray-600">
                            Tim kami siap membantu Anda kapan saja untuk pertanyaan, pemesanan, atau kendala di perjalanan.
                        </p>
                    </div>
                </div>
            </section>

            {{-- FAQ Section (Moved from app.blade) --}}
            <section id="faq" class="container mx-auto px-4 py-16 bg-white">
                <hr class="border-t-2 border-dashed border-blue-300 mx-auto max-w-7xl mb-8">
                <div class="text-center mb-12" data-aos="fade-up">
                    <h2 class="text-4xl font-extrabold section-title mb-4">Pertanyaan Umum (FAQ)</h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Temukan jawaban atas pertanyaan yang sering diajukan seputar layanan kami.
                    </p>
                </div>
                <div class="max-w-3xl mx-auto space-y-6">
                    {{-- FAQ Item 1 --}}
                    <div class="faq-item" data-aos="fade-up" data-aos-delay="100">
                        <div class="faq-question">
                            <span>Bagaimana cara memesan travel?</span>
                            <i class="fas fa-chevron-down arrow-icon"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Anda dapat memesan langsung melalui halaman utama kami dengan memilih rute, tanggal, dan
                                jenis pemesanan (penumpang atau barang). Ikuti langkah-langkah mudah yang tersedia.</p>
                        </div>
                    </div>

                    {{-- FAQ Item 2 --}}
                    <div class="faq-item" data-aos="fade-up" data-aos-delay="200">
                        <div class="faq-question">
                            <span>Apakah saya bisa mengubah jadwal setelah memesan?</span>
                            <i class="fas fa-chevron-down arrow-icon"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Perubahan jadwal tergantung pada ketersediaan dan kebijakan pembatalan/perubahan. Mohon
                                hubungi layanan pelanggan kami secepatnya untuk bantuan.</p>
                        </div>
                    </div>

                    {{-- FAQ Item 3 --}}
                    <div class="faq-item" data-aos="fade-up" data-aos-delay="300">
                        <div class="faq-question">
                            <span>Bagaimana jika barang saya hilang atau rusak saat pengiriman?</span>
                            <i class="fas fa-chevron-down arrow-icon"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Kami sangat berhati-hati dalam setiap pengiriman. Namun, jika terjadi hal yang tidak
                                diinginkan, mohon segera laporkan kepada kami dengan bukti yang cukup untuk proses klaim
                                sesuai syarat dan ketentuan.</p>
                        </div>
                    </div>

                    {{-- FAQ Item 4 --}}
                    <div class="faq-item" data-aos="fade-up" data-aos-delay="400">
                        <div class="faq-question">
                            <span>Metode pembayaran apa saja yang diterima?</span>
                            <i class="fas fa-chevron-down arrow-icon"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Kami menerima pembayaran melalui transfer bank dan metode pembayaran digital lainnya. Detail
                                akan tersedia saat proses checkout.</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Contact Section (Moved from app.blade) --}}
            <section id="contact" class="container mx-auto px-4 py-16">
                <hr class="border-t-2 border-dashed border-blue-300 mx-auto max-w-7xl mb-8">
                <div class="text-center mb-12" data-aos="fade-up">
                    <h2 class="text-4xl font-extrabold section-title mb-4">Hubungi Kami</h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Ada pertanyaan atau butuh bantuan? Jangan ragu untuk menghubungi tim kami.
                    </p>
                </div>
                <div class="max-w-xl mx-auto p-6 card-dashed-border text-center" data-aos="fade-up">
                    <h3 class="text-2xl font-bold section-title mb-4">Informasi Kontak Kami</h3>
                    <p class="flex items-center justify-center text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt mr-3 text-blue-500 text-xl"></i>
                        Jl. Jurago Dusun Ciguha RT 001 RW 001 Desa Campaka Cigugur-Pangandaran
                    </p>
                    <p class="flex items-center justify-center text-gray-700 mb-2">
                        <i class="fas fa-phone mr-3 text-blue-500 text-xl"></i>
                        082117999587
                    </p>
                    <p class="flex items-center justify-center text-gray-700 mb-2">
                        <i class="fab fa-whatsapp mr-3 text-blue-500 text-xl"></i>
                        082117999587
                    </p>
                    <p class="flex items-center justify-center text-gray-700 mb-4">
                        <i class="fas fa-envelope mr-3 text-blue-500 text-xl"></i>
                        dwiiirissa@gmail.com
                    </p>
                    <div class="flex justify-center space-x-6 mt-6">
                        <a href="https://facebook.com/AlgioTrans" target="_blank"
                            class="text-gray-600 text-3xl transition duration-200 social-icon-hover"><i
                                class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-gray-600 text-3xl transition duration-200 social-icon-hover"><i
                                class="fab fa-twitter"></i></a>
                        <a href="https://www.instagram.com/algiotrans" target="_blank"
                            class="text-gray-600 text-3xl transition duration-200 social-icon-hover"><i
                                class="fab fa-instagram"></i></a>
                        <a href="https://www.tiktok.com/@Algiotransfamilly23" target="_blank"
                            class="text-gray-600 text-3xl transition duration-200 social-icon-hover"><i
                                class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                {{-- CTA Section (Moved from app.blade) --}}
                <section
                    class="bg-gradient-to-br from-blue-300 to-blue-500 text-white text-center p-12 rounded-3xl
                                transform transition-all duration-500 ease-in-out hover:scale-[1.01]
                                opacity-0 translate-y-10 mt-16"
                    data-aos="fade-up">
                    <h2 class="text-4xl font-bold mb-6">Siap Untuk Perjalanan Anda Berikutnya?</h2>
                    <p class="text-xl mb-8">
                        Jangan tunda lagi! Pesan tiket atau kirim paket Anda sekarang dan rasakan kemudahan bersama Algio
                        Trans.
                    </p>
                    <a href="#booking-form" id="cta-button"
                        class="inline-block bg-white text-blue-400 font-bold py-4 px-10 rounded-full text-xl
                               hover:bg-gray-100 transform hover:scale-105 transition duration-300 ease-in-out
                               animate-bounce-once">
                        Mulai Sekarang! <i class="fas fa-arrow-alt-circle-right ml-2"></i>
                    </a>
                </section>
            </section>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- Font Awesome for Icons --}}
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    {{-- AOS Library for Scroll Animations --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    {{-- Swiper JS --}}
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    {{-- SweetAlert2 CSS and JS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize AOS
            AOS.init({
                duration: 1000,
                once: true,
            });

            // Initialize Swiper Carousel for About Us
            var swiper = new Swiper('.image-carousel', {
                effect: 'coverflow', // Or 'slide', 'fade', 'cube', 'flip', 'cards'
                grabCursor: true,
                centeredSlides: true,
                slidesPerView: 'auto', // Adjust based on how many slides you want visible
                loop: true, // Make it loop continuously
                coverflowEffect: {
                    rotate: 50,
                    stretch: 0,
                    depth: 100,
                    modifier: 1,
                    slideShadows: true,
                },
                autoplay: {
                    delay: 3000, // 3 seconds
                    disableOnInteraction: false, // Continue autoplay even after user interaction
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });

            const btnPassenger = document.getElementById('btn-passenger');
            const btnItemDelivery = document.getElementById('btn-item-delivery');
            const passengerFields = document.getElementById('passenger-fields');
            const itemDeliveryFields = document.getElementById('item-delivery-fields');
            const bookingTypeInput = document.getElementById('booking_type');
            const formFieldsWrapper = document.getElementById('form-fields-wrapper');

            // Ambil semua input yang relevan di kedua bagian form
            const passengerRouteSelect = document.getElementById('route_id_passenger');
            const passengerDateInput = document.getElementById('departure_date_passenger');
            const passengerNumInput = document.getElementById('num_passengers');

            const itemDeliveryRouteSelect = document.getElementById('route_id_item_delivery');
            const itemDeliveryDateInput = document.getElementById('departure_date_item_delivery');

            // Form validation elements
            const searchScheduleBtn = document.getElementById('search-schedule-btn');
            const bookingFormElement = document.getElementById('booking-form-element');

            // SweetAlert2 function to show errors
            function showSweetAlertError(messages) {
                let htmlMessage = '<ul>';
                messages.forEach(msg => {
                    htmlMessage += `<li>${msg}</li>`;
                });
                htmlMessage += '</ul>';

                Swal.fire({
                    icon: 'error',
                    title: 'Oops! Ada Masalah',
                    html: htmlMessage,
                    confirmButtonText: 'Mengerti',
                    customClass: {
                        popup: 'swal2-responsive-popup', // Add a custom class for responsiveness if needed
                        confirmButton: 'swal2-confirm-button-custom' // Add a custom class for button styling
                    }
                });
            }

            // Set initial height for the wrapper based on the longer form
            function setFormWrapperHeight() {
                // Temporarily make children display: block and static to measure
                passengerFields.style.position = 'static';
                itemDeliveryFields.style.position = 'static';
                passengerFields.classList.remove('hidden');
                itemDeliveryFields.classList.remove('hidden');

                const passengerHeight = passengerFields.offsetHeight;
                const itemDeliveryHeight = itemDeliveryFields.offsetHeight;

                // Set wrapper height to the maximum of both
                formFieldsWrapper.style.height = Math.max(passengerHeight, itemDeliveryHeight) + 'px';

                // Restore absolute positioning and hidden state
                passengerFields.style.position = 'absolute';
                itemDeliveryFields.style.position = 'absolute';
                if (bookingTypeInput.value === 'passenger') {
                    itemDeliveryFields.classList.add('hidden');
                } else {
                    passengerFields.classList.add('hidden');
                }

                // Reset positions to ensure smooth animation start next time
                passengerFields.style.left = '0';
                itemDeliveryFields.style.left = '100%'; // Initially off-screen right
                itemDeliveryFields.classList.add('opacity-0', 'pointer-events-none');

                formFieldsWrapper.style.overflow = 'hidden'; // Tetap hidden untuk transisi internal
            }

            // Call on load and on window resize
            window.addEventListener('load', setFormWrapperHeight);
            window.addEventListener('resize', setFormWrapperHeight);


            function toggleForm(type) {
                formFieldsWrapper.style.overflow = 'hidden'; // Tetap hidden untuk transisi internal

                // Remove all animation/visibility classes first to ensure clean state
                passengerFields.classList.remove('animate-slide-in-right', 'animate-slide-out-left', 'opacity-0',
                    'opacity-100');
                itemDeliveryFields.classList.remove('animate-slide-in-right', 'animate-slide-out-left', 'opacity-0',
                    'opacity-100');
                passengerFields.classList.add('hidden', 'pointer-events-none');
                itemDeliveryFields.classList.add('hidden', 'pointer-events-none');
                passengerFields.style.left = '0';
                itemDeliveryFields.style.left = '0';


                if (type === 'passenger') {
                    // Animate item delivery out if it was visible
                    if (!itemDeliveryFields.classList.contains('hidden')) {
                        itemDeliveryFields.classList.add('animate-slide-out-left');
                        itemDeliveryFields.classList.remove('pointer-events-auto');
                        setTimeout(() => {
                            itemDeliveryFields.classList.add('hidden', 'opacity-0');
                            itemDeliveryFields.classList.remove('animate-slide-out-left');
                            itemDeliveryFields.style.left = '100%';
                        }, 500);
                    }

                    // Animate passenger in
                    passengerFields.classList.remove('hidden', 'pointer-events-none');
                    passengerFields.classList.add('opacity-100', 'pointer-events-auto', 'animate-slide-in-right');
                    bookingTypeInput.value = 'passenger';

                    // Enable/disable fields
                    passengerRouteSelect.disabled = false;
                    passengerDateInput.disabled = false;
                    passengerNumInput.disabled = false;
                    itemDeliveryRouteSelect.disabled = true;
                    itemDeliveryDateInput.disabled = true;

                    // Button styling (ubah ke biru)
                    btnPassenger.classList.add('bg-blue-400', 'text-white');
                    btnPassenger.classList.remove('text-blue-400', 'border-blue-300', 'hover:bg-blue-50');
                    btnItemDelivery.classList.remove('bg-blue-400', 'text-white');
                    btnItemDelivery.classList.add('text-blue-400', 'border-blue-300', 'hover:bg-blue-50');

                    // Reset values of the hidden form
                    itemDeliveryRouteSelect.value = '';
                    itemDeliveryDateInput.value = '';

                } else if (type === 'item_delivery') {
                    // Animate passenger out if it was visible
                    if (!passengerFields.classList.contains('hidden')) {
                        passengerFields.classList.add('animate-slide-out-left');
                        passengerFields.classList.remove('pointer-events-auto');
                        setTimeout(() => {
                            passengerFields.classList.add('hidden', 'opacity-0');
                            passengerFields.classList.remove('animate-slide-out-left');
                            passengerFields.style.left = '100%';
                        }, 500);
                    }

                    // Animate item delivery in
                    itemDeliveryFields.classList.remove('hidden', 'pointer-events-none');
                    itemDeliveryFields.classList.add('opacity-100', 'pointer-events-auto',
                        'animate-slide-in-right');
                    bookingTypeInput.value = 'item_delivery';

                    // Enable/disable fields
                    itemDeliveryRouteSelect.disabled = false;
                    itemDeliveryDateInput.disabled = false;
                    passengerRouteSelect.disabled = true;
                    passengerDateInput.disabled = true;
                    passengerNumInput.disabled = true;

                    // Button styling (ubah ke biru)
                    btnItemDelivery.classList.add('bg-blue-400', 'text-white');
                    btnItemDelivery.classList.remove('text-blue-400', 'border-blue-300', 'hover:bg-blue-50');
                    btnPassenger.classList.remove('bg-blue-400', 'text-white');
                    btnPassenger.classList.add('text-blue-400', 'border-blue-300', 'hover:bg-blue-50');

                    // Reset values of the hidden form
                    passengerRouteSelect.value = '';
                    passengerDateInput.value = '';
                    passengerNumInput.value = '';
                }
            }

            btnPassenger.addEventListener('click', () => toggleForm('passenger'));
            btnItemDelivery.addEventListener('click', () => toggleForm('item_delivery'));

            // Set default to passenger on page load and remove immediate animation
            toggleForm('passenger');
            // Remove initial slide-in animation class immediately after it's applied on first load
            setTimeout(() => {
                passengerFields.classList.remove('animate-slide-in-right');
            }, 600);


            // Dynamic date min attribute for Safari/iOS compatibility
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            const minDate = `${year}-${month}-${day}`;
            document.getElementById('departure_date_passenger').min = minDate;
            document.getElementById('departure_date_item_delivery').min = minDate;


            // Smooth Scroll Logic
            const heroCtaButton = document.getElementById('hero-cta');
            const ctaButton = document.getElementById('cta-button');
            const bookingFormSection = document.getElementById('booking-form');

            if (heroCtaButton) {
                heroCtaButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    bookingFormSection.scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            }

            if (ctaButton) {
                ctaButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    bookingFormSection.scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            }

            // Typed text animation
            const typedTextSpan = document.getElementById('typed-text');
            const textToType = "Rencanakan Perjalanan Anda,";
            let index = 0;
            const typingSpeed = 100;
            const eraseSpeed = 50;
            const delayBeforeErase = 1500;
            const delayBeforeType = 500;


            function typeText() {
                if (index < textToType.length) {
                    typedTextSpan.textContent += textToType.charAt(index);
                    index++;
                    setTimeout(typeText, typingSpeed);
                } else {
                    setTimeout(eraseText, delayBeforeErase);
                }
            }

            function eraseText() {
                if (index > 0) {
                    typedTextSpan.textContent = textToType.substring(0, index - 1);
                    index--;
                    setTimeout(eraseText, eraseSpeed);
                } else {
                    setTimeout(typeText, delayBeforeType);
                }
            }

            typeText();

            // Client-side validation for the form
            bookingFormElement.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent default form submission

                let isValid = true;
                const errors = [];

                if (bookingTypeInput.value === 'passenger') {
                    if (!passengerRouteSelect.value) {
                        errors.push("Tujuan penumpang harus diisi.");
                        isValid = false;
                    }
                    if (!passengerDateInput.value) {
                        errors.push("Tanggal keberangkatan penumpang harus diisi.");
                        isValid = false;
                    }
                    if (!passengerNumInput.value || parseInt(passengerNumInput.value) < 1) {
                        errors.push("Jumlah penumpang minimal 1.");
                        isValid = false;
                    }
                } else if (bookingTypeInput.value === 'item_delivery') {
                    if (!itemDeliveryRouteSelect.value) {
                        errors.push("Tujuan pengiriman harus diisi.");
                        isValid = false;
                    }
                    if (!itemDeliveryDateInput.value) {
                        errors.push("Tanggal pengiriman harus diisi.");
                        isValid = false;
                    }
                } else {
                     errors.push("Silakan pilih jenis pemesanan (Untuk Penumpang atau Untuk Pengiriman Barang).");
                     isValid = false;
                }

                if (!isValid) {
                    showSweetAlertError(errors); // Call our new SweetAlert2 function
                } else {
                    this.submit(); // If valid, submit the form
                }
            });

            // Carousel Logic
            const carousel = document.getElementById('services-carousel');
            if (carousel) {
                const carouselWidth = carousel.scrollWidth / 2; // Since we duplicated the content
                const scrollSpeed = 0.5; // pixels per frame, adjust as needed

                let currentScroll = 0;

                function animateCarousel() {
                    currentScroll += scrollSpeed;
                    if (currentScroll >= carouselWidth) {
                        currentScroll = 0; // Reset scroll position to create loop
                    }
                    carousel.scrollLeft = currentScroll;
                    requestAnimationFrame(animateCarousel);
                }

                animateCarousel();
            }

            // --- FAQ Dropdown Logic (Moved from app.blade) ---
            document.querySelectorAll('.faq-item').forEach(item => {
                const question = item.querySelector('.faq-question');
                const answer = item.querySelector('.faq-answer');
                const arrowIcon = item.querySelector('.arrow-icon');

                question.addEventListener('click', () => {
                    const isOpen = answer.classList.contains('open');

                    document.querySelectorAll('.faq-item').forEach(otherItem => {
                        const otherAnswer = otherItem.querySelector('.faq-answer');
                        const otherArrow = otherItem.querySelector('.arrow-icon');
                        if (otherAnswer !== answer && otherAnswer.classList.contains('open')) {
                            otherAnswer.classList.remove('open');
                            otherArrow.classList.remove('rotate');
                        }
                    });

                    if (isOpen) {
                        answer.classList.remove('open');
                        arrowIcon.classList.remove('rotate');
                    } else {
                        answer.classList.add('open');
                        arrowIcon.classList.add('rotate');
                    }
                });
            });

            // --- Modal Logic (Moved from app.blade) ---
            const modalTriggers = document.querySelectorAll('[data-modal-target]');
            const modalCloseButtons = document.querySelectorAll('[data-modal-close]');

            modalTriggers.forEach(trigger => {
                trigger.addEventListener('click', (e) => {
                    e.preventDefault();
                    const modalId = trigger.dataset.modalTarget;
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        modal.classList.add('open');
                        document.body.classList.add('overflow-hidden');
                    }
                });
            });

            modalCloseButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    const modalId = button.dataset.modalClose;
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        modal.classList.remove('open');
                        document.body.classList.remove('overflow-hidden');
                    }
                });
            });

            document.querySelectorAll('.modal-overlay').forEach(overlay => {
                overlay.addEventListener('click', (e) => {
                    if (e.target === overlay) {
                        overlay.classList.remove('open');
                        document.body.classList.remove('overflow-hidden');
                    }
                });
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.modal-overlay.open').forEach(modal => {
                        modal.classList.remove('open');
                        document.body.classList.remove('overflow-hidden');
                    });
                }
            });
        });

        // Skeleton Loader Logic
        const skeletonOverlay = document.getElementById('skeleton-overlay');

        skeletonOverlay.classList.remove('hidden');
        skeletonOverlay.classList.add('opacity-100');

        window.addEventListener('load', () => {
            skeletonOverlay.classList.remove('opacity-100');
            skeletonOverlay.classList.add('opacity-0');
            setTimeout(() => {
                skeletonOverlay.classList.add('hidden');
            }, 700);
        });
    </script>

    {{-- Tailwind CSS Custom Animations (add these to your main CSS file or a <style> tag in layouts.app) --}}
    <style>
        /* Custom Select Styles */
        .custom-select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%236B7280'%3E%3Cpath fill-rule='evenodd' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' clip-rule='evenodd'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1.5em 1.5em;
        }

        /* Keyframe Animations */
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInLeft { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes fadeInRight { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes bounceOnce { 0%, 100% { transform: translateY(0); } 20% { transform: translateY(-8px); } 40% { transform: translateY(0); } 60% { transform: translateY(-4px); } 80% { transform: translateY(0); } }
        @keyframes pulseSlow { 0%, 100% { opacity: 0.8; } 50% { opacity: 1; } }
        @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-10px); } 100% { transform: translateY(0px); } }
        @keyframes wiggle { 0%, 7% { transform: rotateZ(0); } 15% { transform: rotateZ(-15deg); } 20% { transform: rotateZ(10deg); } 25% { transform: rotateZ(-10deg); } 30% { transform: rotateZ(6deg); } 35% { transform: rotateZ(-4deg); } 40%, 100% { transform: rotateZ(0); } }
        @keyframes spinSlow { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        @keyframes popIn { 0% { transform: scale(0); opacity: 0; } 80% { transform: scale(1.1); opacity: 1; } 100% { transform: scale(1); } }
        @keyframes pingOnce { 0% { transform: scale(0.5); opacity: 0.5; } 100% { transform: scale(1.5); opacity: 0; } }
        @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes slideOutLeft { from { transform: translateX(0); opacity: 1; } to { transform: translateX(-100%); opacity: 0; } } /* opacity: 0 to ensure hidden */

        /* Apply Animations */
        .animate-fade-in { animation: fadeIn 0.8s ease-out forwards; }
        .animate-fade-in-up { animation: fadeInUp 0.8s ease-out forwards; }
        .animate-fade-in-down { animation: fadeInDown 0.8s ease-out forwards; }
        .animate-fade-in-left { animation: fadeInLeft 0.8s ease-out forwards; }
        .animate-fade-in-right { animation: fadeInRight 0.8s ease-out forwards; }
        .animate-bounce-once { animation: bounceOnce 1.5s ease-in-out; }
        .animate-pulse-slow { animation: pulseSlow 4s infinite ease-in-out; }
        .animate-float { animation: float 3s infinite ease-in-out; }
        .animate-wiggle { animation: wiggle 2s infinite ease-in-out; }
        .animate-spin-slow { animation: spinSlow 5s infinite linear; }
        .animate-pop-in { animation: popIn 0.6s cubic-bezier(0.68, -0.55, 0.27, 1.55) forwards; }
        .animate-ping-once { animation: pingOnce 1.5s ease-out forwards; }
        .animate-slide-in-right { animation: slideInRight 0.5s ease-out forwards; }
        .animate-slide-out-left { animation: slideOutLeft 0.5s ease-out forwards; }

        /* Pulsing animation on hover for buttons */
        .animate-pulse-once-on-hover:hover { animation: pulseOnce 1s ease-in-out; }
        @keyframes pulseOnce { 0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); } }

        /* Custom SweetAlert2 styling (optional, but good for consistency) */
        .swal2-confirm-button-custom {
            background-color: #60A5FA !important; /* blue-400 */
            color: white !important;
            border-radius: 9999px !important; /* full rounded */
            padding: 0.75rem 2rem !important; /* py-3 px-8 */
            font-size: 1.125rem !important; /* text-lg */
            font-weight: 700 !important; /* font-bold */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important; /* shadow-md */
            transition: all 0.15s ease-in-out !important;
        }

        .swal2-confirm-button-custom:hover {
            background-color: #3B82F6 !important; /* blue-500 */
            transform: scale(1.05);
        }

        .swal2-responsive-popup {
            width: 90% !important; /* Smaller on mobile */
            max-width: 400px !important; /* Max width */
        }
        @media (min-width: 640px) { /* sm breakpoint */
            .swal2-responsive-popup {
                width: auto !important;
            }
        }

        /* Carousel specific styles */
        .carousel-container {
            overflow-x: hidden; /* Hide scrollbar but allow content to move */
            white-space: nowrap; /* Keep items in a single line */
            -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS */
            scroll-behavior: auto; /* Allow direct JS control */
            will-change: scroll-position; /* Optimize for smooth animation */
            padding-bottom: 20px; /* Menambah padding agar batas bawah tidak terlihat */
            margin-bottom: -20px; /* Mengurangi margin agar padding tidak menambah tinggi total */
        }

        .service-card {
            min-width: 170px; /* Minimum width for each card, adjust as needed */
            box-sizing: border-box; /* Include padding and border in the width */
            border-style: dashed; /* Mengubah garis putus-putus */
            box-shadow: none; /* Menghilangkan bayangan jika tidak diinginkan */
        }
    </style>
@endsection