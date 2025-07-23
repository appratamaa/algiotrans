<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Algio Trans - @yield('title', 'Travel & Paket')</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    {{-- Font Awesome for Icons (gunakan CDN langsung untuk menghindari masalah CORS kit.fontawesome.com) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    {{-- AOS Library for Scroll Animations --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    {{-- Swiper CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <meta property="og:type" content="website">
    <meta property="og:url" content="https://algiotrans.my.id/">
    <meta property="og:title" content="Algio Trans - Solusi Travel Antar Kota & Pengiriman Barang Terbaik">
    <meta property="og:description" content="Pesan travel atau kirim barang dengan Algio Trans! Layanan cepat, nyaman, dan terjangkau untuk perjalanan Anda di Tasikmalaya dan sekitarnya. Booking sekarang!">
    <meta property="og:image" content="https://algiotrans.my.id/images/algiotrans_og_image.jpg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="Algio Trans">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://algiotrans.my.id/">
    <meta name="twitter:title" content="Algio Trans - Solusi Travel Antar Kota & Pengiriman Barang Terbaik">
    <meta name="twitter:description" content="Pesan travel atau kirim barang dengan Algio Trans! Layanan cepat, nyaman, dan terjangkau untuk perjalanan Anda di Tasikmalaya dan sekitarnya. Booking sekarang!">
    <meta name="twitter:image" content="https://algiotrans.my.id/images/algiotrans_og_image.jpg">

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            scroll-behavior: smooth;
            /* Penting untuk smooth scroll */
            background-color: #FFFFFF;
            /* Mengubah background menjadi putih total */
            position: relative;
            overflow-x: hidden;
        }

        /* Hapus background dengan gambar pesawat kertas dan awan */
        body::before {
            content: none;
            /* Menghilangkan pseudo-element untuk background */
        }

        /* Custom styles for mobile menu */
        .mobile-menu {
            transform: translateX(100%);
            transition: transform 0.3s ease-out;
        }

        .mobile-menu.active {
            transform: translateX(0);
        }

        /* Overlay for mobile menu */
        .mobile-menu-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            opacity: 0;
            transition: opacity 0.3s ease-out;
            pointer-events: none;
            /* Allows clicks through when hidden */
        }

        .mobile-menu-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        /* Warna solid untuk Algio Trans (blue-400) */
        .algiotrans-title {
            color: #60A5FA;
            /* blue-400 */
            font-family: 'Pacifico', cursive;
            font-weight: normal;
            line-height: 1;
            padding-bottom: 12px;
            display: inline-block;
            vertical-align: bottom;
        }

        /* Tombol Back to Top */
        #back-to-top {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Fixed/sticky position */
            bottom: 20px;
            /* Place the button at the bottom of the page */
            right: 30px;
            /* Place the button 30px from the right */
            z-index: 99;
            /* Make sure it does not overlap */
            border: none;
            outline: none;
            background-color: #60A5FA;
            /* blue-400 */
            color: white;
            /* Text color */
            cursor: pointer;
            /* Add a mouse pointer on hover */
            width: 50px;
            /* Ukuran tombol */
            height: 50px;
            /* Ukuran tombol */
            border-radius: 50%;
            /* Membuat tombol sangat bulat */
            font-size: 18px;
            /* Increase font size */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            /* Tambahkan sedikit bayangan */
            transition: background-color 0.3s, transform 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #back-to-top:hover {
            background-color: #3B82F6;
            /* blue-500 */
            transform: translateY(-3px);
        }

        /* Tombol Floating WhatsApp */
        .whatsapp-float {
            position: fixed;
            width: 50px;
            /* Samakan dengan back-to-top */
            height: 50px;
            /* Samakan dengan back-to-top */
            bottom: 20px;
            left: 30px;
            /* Pindah ke kiri */
            background-color: #25D366;
            /* Warna WhatsApp hijau */
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 24px;
            /* Sesuaikan ukuran ikon */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            /* Tambahkan bayangan */
            border: none;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .whatsapp-float:hover {
            background-color: #1DA851;
            /* Sedikit lebih gelap saat hover */
            transform: translateY(-3px);
        }

        /* Navbar Hamburger/Menu button style */
        #mobile-menu-button {
            border: none;
            border-radius: 8px;
            padding: 8px;
            color: #60A5FA;
            /* Mengubah warna ikon hamburger menjadi blue-400 */
        }

        /* Mobile Menu Sidebar */
        #mobile-menu-sidebar {
            border-left: none;
            background-color: #60A5FA;
            /* Mengubah background sidebar menjadi blue-400 */
        }

        #mobile-menu-sidebar a {
            border-bottom: none;
        }

        /* Gaya baru untuk menu aktif */
        .nav-link.active {
            color: #3B82F6;
            /* blue-500 */
            font-weight: bold;
            /* Opsional: membuat teks lebih tebal */
        }

        /* Gaya baru untuk menu aktif di mobile */
        .mobile-nav-link.active {
            background-color: #3B82F6;
            /* blue-500 */
        }

        /* Gaya untuk card dengan border dashed */
        .card-dashed-border {
            border: 2px dashed #93C5FD;
            /* blue-300, untuk konsistensi dengan welcome */
            border-radius: 0.75rem;
            /* rounded-xl */
            background-color: #FFFFFF;
            /* bg-white */
        }

        /* FAQ Dropdown Styles */
        .faq-item {
            border: 2px dashed #93C5FD;
            /* blue-300, untuk konsistensi */
            border-radius: 0.75rem;
            background-color: #FFFFFF;
            overflow: hidden;
            /* Penting untuk transisi */
        }

        .faq-question {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            /* p-6 */
            font-size: 1.25rem;
            /* text-xl */
            font-weight: bold;
            color: #60A5FA;
            /* blue-400 - Changed for all titles */
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .faq-question:hover {
            background-color: #DBEAFE;
            /* blue-100 */
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease-out, padding 0.5s ease-out;
            padding: 0 1.5rem;
            /* p-6 */
            color: #4B5563;
            /* gray-700 */
        }

        .faq-answer.open {
            max-height: 500px;
            /* Sesuaikan dengan tinggi maksimum konten */
            padding-bottom: 1.5rem;
        }

        .faq-question .arrow-icon {
            transition: transform 0.3s ease;
        }

        .faq-question .arrow-icon.rotate {
            transform: rotate(180deg);
        }

        /* Perbaikan hover sosial media */
        .social-icon-hover:hover {
            color: #60A5FA !important;
            /* blue-400 */
        }

        /* Warna judul utama section */
        .section-title {
            color: #60A5FA;
            /* blue-400 - Changed for all titles */
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .modal-overlay.open {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background-color: #FFFFFF;
            /* White background */
            padding: 2.5rem;
            /* p-10 */
            border-radius: 1rem;
            /* rounded-2xl */
            max-width: 90%;
            max-height: 90%;
            overflow-y: auto;
            position: relative;
            transform: translateY(20px);
            opacity: 0;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .modal-overlay.open .modal-content {
            transform: translateY(0);
            opacity: 1;
        }

        .modal-close-button {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #6B7280;
            /* gray-500 */
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .modal-close-button:hover {
            color: #EF4444;
            /* red-500 */
        }

        /* Swiper Custom Styles for "About Us" Carousel */
        .image-carousel {
            width: 100%;
            max-width: 450px;
            /* Adjust max width of the carousel */
            height: 300px;
            /* Adjust height of the carousel */
            margin: 0 auto;
            /* Center the carousel */
        }

        .image-carousel .swiper-slide {
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            transform-style: preserve-3d;
            /* Enable 3D transforms */
            transition: transform 0.5s ease-out;
        }

        .image-carousel .swiper-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 0.75rem;
        }

        /* Optional: Add some overlapping effect for the carousel if desired, e.g., using swiper-slide-active, swiper-slide-prev, swiper-slide-next */
        .image-carousel .swiper-slide-active {
            z-index: 10;
        }

        .image-carousel .swiper-slide-prev,
        .image-carousel .swiper-slide-next {
            opacity: 0.7;
            /* Slightly dim the surrounding slides */
            transform: scale(0.9);
            /* Make them slightly smaller */
        }

        /* Style for Swiper Pagination */
        .swiper-pagination-bullet {
            background-color: #60A5FA !important;
            /* blue-400 */
            opacity: 0.7 !important;
        }

        .swiper-pagination-bullet-active {
            background-color: #3B82F6 !important;
            /* blue-500 */
            opacity: 1 !important;
        }

        /* Style for Swiper Navigation Buttons */
        .swiper-button-next,
        .swiper-button-prev {
            color: #60A5FA !important;
            /* blue-400 */
        }

        .swiper-button-next::after,
        .swiper-button-prev::after {
            font-size: 1.5rem !important;
        }
    </style>
</head>

<body class="text-gray-900">

    {{-- Floating Navbar --}}
    <nav class="fixed top-4 z-50 px-4 w-full md:w-auto md:max-w-screen-xl md:left-1/2 md:-translate-x-1/2">
        <div class="bg-white p-4 rounded-full shadow-lg flex justify-between items-center">
            <a href="{{ route('home') }}"
                class="text-3xl font-extrabold whitespace-nowrap mr-6 md:mr-10 algiotrans-title">Algio Trans</a>

            {{-- Desktop Navigation Links --}}
            <div class="hidden md:flex items-center space-x-6">
                <a href="#about-us"
                    class="nav-link text-gray-700 font-semibold hover:text-blue-400 transition duration-200">Tentang
                    Kami</a>
                <a href="#services"
                    class="nav-link text-gray-700 font-semibold hover:text-blue-400 transition duration-200">Layanan</a>
                <a href="#faq"
                    class="nav-link text-gray-700 font-semibold hover:text-blue-400 transition duration-200">FAQ</a>
                <a href="#contact"
                    class="nav-link text-gray-700 font-semibold hover:text-blue-400 transition duration-200">Kontak</a>
            </div>

            {{-- Mobile Menu Button (Hamburger) --}}
            <button id="mobile-menu-button" class="md:hidden text-2xl focus:outline-none">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    {{-- Mobile Menu Overlay --}}
    <div id="mobile-menu-overlay" class="fixed inset-0 z-40 mobile-menu-overlay"></div>

    {{-- Mobile Menu Sidebar (warna sudah diatur di style) --}}
    <div id="mobile-menu-sidebar"
        class="fixed top-0 right-0 h-full w-64 text-white z-50 shadow-lg mobile-menu md:hidden">
        <div class="p-6 flex justify-end">
            <button id="close-mobile-menu" class="text-white text-2xl focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <nav class="flex flex-col items-center space-y-6 text-lg">
            <a href="#about-us"
                class="mobile-nav-link w-full text-center py-3 hover:bg-blue-500 transition duration-200">Tentang
                Kami</a>
            <a href="#services"
                class="mobile-nav-link w-full text-center py-3 hover:bg-blue-500 transition duration-200">Layanan</a>
            <a href="#faq"
                class="mobile-nav-link w-full text-center py-3 hover:bg-blue-500 transition duration-200">FAQ</a>
            <a href="#contact"
                class="mobile-nav-link w-full text-center py-3 hover:bg-blue-500 transition duration-200">Kontak</a>
        </nav>
    </div>

    <main class="pt-20 pb-8">
        {{-- Yield content from specific pages, or add default content here --}}
        @yield('content')
    </main>

    {{-- Batas Footer dengan Garis Putus-putus --}}
    <hr class="border-t-2 border-dashed border-blue-300 mx-auto max-w-7xl mb-8">

    {{-- Footer Section --}}
    <footer class="bg-white text-gray-800 py-12">
        <div class="container mx-auto px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 md:gap-x-12">
            {{-- Company Info --}}
            <div class="md:text-left text-center">
                <h3 class="text-2xl font-extrabold mb-4 algiotrans-title">Algio Trans</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Solusi terdepan untuk perjalanan nyaman dan pengiriman paket aman. Kami hadir untuk memudahkan
                    setiap perjalanan Anda.
                </p>
            </div>

            {{-- Quick Links --}}
            <div class="md:text-left text-center">
                <h3 class="text-xl font-bold mb-4 text-blue-400">Tautan Cepat</h3>
                <ul class="space-y-2 text-gray-600">
                    <li><a href="#about-us" class="hover:text-blue-400 transition duration-200">Tentang Kami</a></li>
                    <li><a href="#services" class="hover:text-blue-400 transition duration-200">Layanan Kami</a></li>
                    <li><a href="#faq" class="hover:text-blue-400 transition duration-200">FAQ</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition duration-200"
                            data-modal-target="privacyPolicyModal">Kebijakan Privasi</a></li> {{-- Modal Trigger --}}
                    <li><a href="#" class="hover:text-blue-400 transition duration-200"
                            data-modal-target="termsConditionsModal">Syarat & Ketentuan</a></li> {{-- Modal Trigger --}}
                    <li><a href="#contact" class="hover:text-blue-400 transition duration-200">Kontak</a></li>
                </ul>
            </div>

            {{-- Contact Info --}}
            <div class="md:text-left text-center">
                <h3 class="text-xl font-bold mb-4 text-blue-400">Hubungi Kami</h3>
                <ul class="space-y-2 text-gray-600">
                    <li class="flex items-center justify-center md:justify-start">
                        <i class="fas fa-map-marker-alt mr-2 text-blue-400"></i>
                        Jl. Jurago Dusun Ciguha RT 001 RW 001 Desa Campaka Cigugur-Pangandaran
                    </li>
                    <li class="flex items-center justify-center md:justify-start">
                        <i class="fas fa-phone mr-2 text-blue-400"></i>
                        082117999587
                    </li>
                    <li class="flex items-center justify-center md:justify-start">
                        <i class="fab fa-whatsapp mr-2 text-blue-400"></i>
                        082117999587
                    </li>
                    <li class="flex items-center justify-center md:justify-start">
                        <i class="fas fa-envelope mr-2 text-blue-400"></i>
                        dwiiirissa@gmail.com
                    </li>
                </ul>
            </div>

            {{-- Social Media --}}
            <div class="md:text-left text-center">
                <h3 class="text-xl font-bold mb-4 text-blue-400">Ikuti Kami</h3>
                <div class="flex justify-center md:justify-start space-x-4">
                    <a href="https://facebook.com/AlgioTrans" target="_blank"
                        class="text-gray-600 text-2xl transition duration-200 social-icon-hover"><i
                            class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-gray-600 text-2xl transition duration-200 social-icon-hover"><i
                            class="fab fa-twitter"></i></a>
                    <a href="https://www.instagram.com/algiotrans" target="_blank"
                        class="text-gray-600 text-2xl transition duration-200 social-icon-hover"><i
                            class="fab fa-instagram"></i></a>
                    <a href="https://www.tiktok.com/@Algiotransfamilly23" target="_blank"
                        class="text-gray-600 text-2xl transition duration-200 social-icon-hover"><i
                            class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </div>

        <div
            class="border-t border-gray-200 mt-8 pt-6 text-center text-sm text-gray-500 flex flex-col md:flex-row justify-center items-center md:space-x-2">
            <span>&copy; {{ date('Y') }} Algio Trans. All rights reserved.</span>
            <span class="hidden md:inline">|</span>
            <span>Crafted with care by <a href="https://www.linkedin.com/in/andreputrap" target="_blank"
                    class="text-blue-500 hover:underline">Andre Putra Pratama</a></span>
        </div>
    </footer>

    {{-- Floating WhatsApp Button --}}
    <a href="https://wa.me/6282117999587?text={{ urlencode('Halo Algio Trans, saya ingin bertanya tentang...') }}"
        class="whatsapp-float" target="_blank" aria-label="WhatsApp Algio Trans">
        <i class="fab fa-whatsapp"></i>
    </a>

    {{-- Back to Top Button --}}
    <button onclick="topFunction()" id="back-to-top" title="Go to top">
        <i class="fas fa-arrow-up"></i>
    </button>

    {{-- Privacy Policy Modal --}}
    <div id="privacyPolicyModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close-button" data-modal-close="privacyPolicyModal">&times;</button>
            <h2 class="text-3xl font-bold section-title mb-6 text-center">Kebijakan Privasi</h2>
            <div class="prose max-w-none text-gray-700">
                <p><strong>Tanggal Efektif: 23 Juli 2025</strong></p>
                <p>Algio Trans berkomitmen untuk melindungi privasi pengguna kami. Kebijakan Privasi ini menjelaskan
                    bagaimana kami mengumpulkan, menggunakan, mengungkapkan, dan melindungi informasi pribadi Anda yang
                    kami peroleh melalui situs web ini.</p>

                <h3>1. Informasi yang Kami Kumpulkan</h3>
                <p>Kami dapat mengumpulkan informasi pribadi yang Anda berikan langsung kepada kami, seperti:</p>
                <ul>
                    <li>Nama lengkap</li>
                    <li>Alamat email</li>
                    <li>Nomor telepon (termasuk WhatsApp)</li>
                    <li>Alamat penjemputan dan pengantaran</li>
                    <li>Detail perjalanan (rute, tanggal, waktu, jumlah penumpang)</li>
                    <li>Informasi pembayaran </li>
                    <li>Informasi identitas (seperti nomor KTP, jika diperlukan untuk pemesanan)</li>
                    <li>Deskripsi barang kiriman (untuk layanan pengiriman barang)</li>
                </ul>

                <h3>2. Bagaimana Kami Menggunakan Informasi Anda</h3>
                <p>Kami menggunakan informasi yang kami kumpulkan untuk tujuan berikut:</p>
                <ul>
                    <li>Memproses dan mengelola pemesanan travel dan pengiriman barang Anda.</li>
                    <li>Mengirimkan konfirmasi pemesanan, tiket, dan pembaruan status melalui email atau WhatsApp.</li>
                    <li>Memberikan dukungan pelanggan dan merespons pertanyaan Anda.</li>
                    <li>Mengkoordinasikan layanan penjemputan dan pengantaran dengan driver.</li>
                    <li>Meningkatkan layanan dan pengalaman pengguna kami.</li>
                    <li>Menganalisis penggunaan situs web untuk tujuan statistik.</li>
                    <li>Mematuhi kewajiban hukum atau peraturan yang berlaku.</li>
                </ul>

                <h3>3. Pembagian Informasi</h3>
                <p>Kami tidak akan menjual, menyewakan, atau memperdagangkan informasi pribadi Anda kepada pihak ketiga
                    tanpa persetujuan Anda, kecuali dalam situasi berikut:</p>
                <ul>
                    <li>Kepada penyedia layanan pihak ketiga yang bekerja atas nama kami (misalnya, penyedia API
                        WhatsApp, gerbang pembayaran) untuk memfasilitasi layanan kami.</li>
                    <li>Kepada driver atau mitra pengiriman untuk tujuan pelaksanaan layanan yang Anda pesan.</li>
                    <li>Jika diwajibkan oleh hukum atau oleh perintah pengadilan.</li>
                    <li>Untuk melindungi hak, properti, atau keamanan Algio Trans atau pengguna lainnya.</li>
                </ul>

                <h3>4. Keamanan Data</h3>
                <p>Kami mengambil langkah-langkah keamanan yang wajar untuk melindungi informasi pribadi Anda dari akses
                    tidak sah, pengungkapan, perubahan, atau penghancuran. Namun, tidak ada metode transmisi data
                    melalui internet atau penyimpanan elektronik yang 100% aman.</p>

                <h3>5. Perubahan Kebijakan Privasi</h3>
                <p>Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu. Setiap perubahan akan diposting di
                    halaman ini, dan tanggal "Tanggal Efektif" di bagian atas akan diperbarui. Kami mendorong Anda untuk
                    meninjau Kebijakan Privasi ini secara berkala.</p>

                <h3>6. Hubungi Kami</h3>
                <p>Jika Anda memiliki pertanyaan atau kekhawatiran tentang Kebijakan Privasi ini atau praktik privasi
                    kami, silakan hubungi kami di:</p>
                <p>Email: dwiiirissa@gmail.com<br>WhatsApp: 082117999587</p>
            </div>
        </div>
    </div>

    {{-- Terms & Conditions Modal --}}
    <div id="termsConditionsModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close-button" data-modal-close="termsConditionsModal">&times;</button>
            <h2 class="text-3xl font-bold section-title mb-6 text-center">Syarat & Ketentuan</h2>
            <div class="prose max-w-none text-gray-700">
                <p><strong>Tanggal Efektif: 23 Juli 2025</strong></p>
                <p>Selamat datang di Algio Trans. Dengan menggunakan situs web dan layanan kami, Anda setuju untuk
                    terikat oleh Syarat & Ketentuan ini. Mohon baca dengan seksama.</p>

                <h3>1. Penggunaan Layanan</h3>
                <ul>
                    <li>Layanan Algio Trans tersedia untuk pemesanan travel penumpang dan pengiriman barang.</li>
                    <li>Anda bertanggung jawab untuk memberikan informasi yang akurat dan lengkap saat melakukan
                        pemesanan.</li>
                    <li>Algio Trans berhak menolak pemesanan yang melanggar hukum atau kebijakan kami.</li>
                </ul>

                <h3>2. Pemesanan dan Pembayaran</h3>
                <ul>
                    <li>Semua pemesanan harus dilakukan melalui platform Algio Trans.</li>
                    <li>Harga yang tertera adalah final kecuali ada perubahan yang disepakati bersama.</li>
                    <li>Pembayaran harus diselesaikan sesuai instruksi yang diberikan saat checkout. Pemesanan tidak
                        akan dikonfirmasi sampai pembayaran diterima sepenuhnya.</li>
                </ul>

                <h3>3. Pembatalan dan Perubahan Jadwal</h3>
                <ul>
                    <li>Pembatalan dan perubahan jadwal tunduk pada kebijakan pembatalan/perubahan yang berlaku, yang
                        mungkin melibatkan biaya pembatalan atau penyesuaian harga.</li>
                    <li>Pengembalian dana (refund) akan diproses sesuai dengan kebijakan refund kami.</li>
                </ul>

                <h3>4. Tanggung Jawab Penumpang/Pengirim</h3>
                <ul>
                    <li>Penumpang bertanggung jawab untuk tiba di titik penjemputan tepat waktu.</li>
                    <li>Pengirim barang bertanggung jawab untuk memastikan barang dikemas dengan aman dan isinya sesuai
                        deskripsi, serta tidak melanggar hukum (misalnya, bahan berbahaya, barang ilegal).</li>
                    <li>Algio Trans tidak bertanggung jawab atas barang-barang berharga yang tidak diasuransikan atau
                        dinyatakan nilainya.</li>
                </ul>

                <h3>5. Batasan Tanggung Jawab Algio Trans</h3>
                <ul>
                    <li>Algio Trans berusaha memberikan layanan terbaik, namun tidak bertanggung jawab atas
                        keterlambatan yang disebabkan oleh faktor di luar kendali kami (misalnya, kondisi lalu lintas,
                        bencana alam, kerusakan kendaraan mendadak).</li>
                    <li>Kami tidak bertanggung jawab atas kehilangan atau kerusakan barang atau bagasi pribadi yang
                        disebabkan oleh kelalaian penumpang/pengirim atau force majeure.</li>
                    <li>Algio Trans memiliki hak untuk mengubah rute atau jadwal demi alasan keamanan atau operasional.
                    </li>
                </ul>

                <h3>6. Perubahan Syarat & Ketentuan</h3>
                <p>Algio Trans berhak untuk mengubah Syarat & Ketentuan ini kapan saja. Setiap perubahan akan diposting
                    di situs web kami, dan penggunaan Anda secara terus-menerus atas layanan kami akan dianggap sebagai
                    penerimaan atas perubahan tersebut.</p>

                <h3>7. Hukum yang Berlaku</h3>
                <p>Syarat & Ketentuan ini diatur oleh dan ditafsirkan sesuai dengan hukum yang berlaku di Indonesia.</p>

                <h3>8. Hubungi Kami</h3>
                <p>Jika Anda memiliki pertanyaan tentang Syarat & Ketentuan ini, silakan hubungi kami:</p>
                <p>Email: dwiiirissa@gmail.com<br>WhatsApp: 082117999587</p>
            </div>
        </div>
    </div>


    @vite('resources/js/app.js')
    {{-- AOS Library JS --}}
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    {{-- Swiper JS --}}
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true,
        });

        // Mobile Menu Toggle Logic
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const closeMobileMenuButton = document.getElementById('close-mobile-menu');
        const mobileMenuSidebar = document.getElementById('mobile-menu-sidebar');
        const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');

        function toggleMobileMenu() {
            mobileMenuSidebar.classList.toggle('active');
            mobileMenuOverlay.classList.toggle('active');
            document.body.classList.toggle('overflow-hidden'); // Prevent scrolling body when menu is open
        }

        if (mobileMenuButton) {
            mobileMenuButton.addEventListener('click', toggleMobileMenu);
        }
        if (closeMobileMenuButton) {
            closeMobileMenuButton.addEventListener('click', toggleMobileMenu);
        }
        if (mobileMenuOverlay) {
            mobileMenuOverlay.addEventListener('click', toggleMobileMenu); // Close when clicking outside
        }

        // --- Navbar Link Active State Logic & Smooth Scroll ---
        document.addEventListener('DOMContentLoaded', () => {
            const desktopNavLinks = document.querySelectorAll('.nav-link');
            const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');

            function removeActiveClass(links) {
                links.forEach(link => {
                    link.classList.remove('active');
                    if (!link.classList.contains('mobile-nav-link')) {
                        link.classList.remove('text-blue-500', 'font-bold');
                        link.classList.add('text-gray-700', 'font-semibold');
                    } else {
                        link.classList.remove('bg-blue-500');
                        link.classList.add('hover:bg-blue-500');
                    }
                });
            }

            function addActiveClass(link, isMobile = false) {
                if (isMobile) {
                    link.classList.remove('hover:bg-blue-500');
                    link.classList.add('bg-blue-500');
                } else {
                    link.classList.remove('text-gray-700', 'font-semibold');
                    link.classList.add('text-blue-500', 'font-bold');
                }
                link.classList.add('active');
            }

            // Function to handle scroll and update active link
            function updateActiveNavLink() {
                const sections = document.querySelectorAll('main section[id]');
                let currentActive = null;
                const navbarHeight = document.querySelector('nav').offsetHeight + 20;

                sections.forEach(section => {
                    const sectionTop = section.offsetTop - navbarHeight;
                    const sectionBottom = sectionTop + section.offsetHeight;

                    if (window.scrollY >= sectionTop && window.scrollY < sectionBottom) {
                        currentActive = section.id;
                    }
                });

                desktopNavLinks.forEach(link => {
                    if (link.getAttribute('href').substring(1) === currentActive) {
                        removeActiveClass(desktopNavLinks);
                        addActiveClass(link);
                    }
                });
                mobileNavLinks.forEach(link => {
                    if (link.getAttribute('href').substring(1) === currentActive) {
                        removeActiveClass(mobileNavLinks);
                        addActiveClass(link, true);
                    }
                });
            }

            // For Desktop Navigation Links
            desktopNavLinks.forEach(link => {
                link.addEventListener('click', function(event) {
                    if (this.getAttribute('href').startsWith('#')) {
                        event.preventDefault();
                        const targetId = this.getAttribute('href').substring(1);
                        const targetElement = document.getElementById(targetId);
                        if (targetElement) {
                            const navbarHeight = document.querySelector('nav').offsetHeight + 20;
                            window.scrollTo({
                                top: targetElement.offsetTop - navbarHeight,
                                behavior: 'smooth'
                            });
                        }
                        removeActiveClass(desktopNavLinks);
                        addActiveClass(this);
                    }
                });
            });

            // For Mobile Navigation Links
            mobileNavLinks.forEach(link => {
                link.addEventListener('click', function(event) {
                    if (this.getAttribute('href').startsWith('#')) {
                        event.preventDefault();
                        const targetId = this.getAttribute('href').substring(1);
                        const targetElement = document.getElementById(targetId);
                        if (targetElement) {
                            const navbarHeight = document.querySelector('nav').offsetHeight + 20;
                            window.scrollTo({
                                top: targetElement.offsetTop - navbarHeight,
                                behavior: 'smooth'
                            });
                        }
                        removeActiveClass(mobileNavLinks);
                        addActiveClass(this, true);
                        toggleMobileMenu();
                    }
                });
            });

            window.addEventListener('scroll', updateActiveNavLink);
            updateActiveNavLink();
        });
        // --- End Navbar Link Active State Logic & Smooth Scroll ---


        // Back to Top Button Logic
        const backToTopButton = document.getElementById("back-to-top");

        window.onscroll = function() {
            scrollFunction()
        };

        function scrollFunction() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                backToTopButton.style.display = "flex";
            } else {
                backToTopButton.style.display = "none";
            }
        }

        function topFunction() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    </script>
    @yield('scripts')
</body>

</html>