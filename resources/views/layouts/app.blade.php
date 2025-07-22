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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    {{-- AOS Library for Scroll Animations --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            scroll-behavior: smooth;
            background-color: #FFFFFF; /* Mengubah background menjadi putih total */
            position: relative;
            overflow-x: hidden;
        }

        /* Hapus background dengan gambar pesawat kertas dan awan */
        body::before {
            content: none; /* Menghilangkan pseudo-element untuk background */
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
            pointer-events: none; /* Allows clicks through when hidden */
        }
        .mobile-menu-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }
        /* Warna solid untuk Algio Trans (blue-400) */
        .algiotrans-title {
            color: #60A5FA; /* blue-400 */
            font-family: 'Pacifico', cursive;
            font-weight: normal;
            line-height: 1;
            padding-bottom: 12px;
            display: inline-block;
            vertical-align: bottom;
        }

        /* Tombol Back to Top */
        #back-to-top {
            display: none; /* Hidden by default */
            position: fixed; /* Fixed/sticky position */
            bottom: 20px; /* Place the button at the bottom of the page */
            right: 30px; /* Place the button 30px from the right */
            z-index: 99; /* Make sure it does not overlap */
            border: none;
            outline: none;
            background-color: #60A5FA; /* blue-400 */
            color: white; /* Text color */
            cursor: pointer; /* Add a mouse pointer on hover */
            width: 50px; /* Ukuran tombol */
            height: 50px; /* Ukuran tombol */
            border-radius: 50%; /* Membuat tombol sangat bulat */
            font-size: 18px; /* Increase font size */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Tambahkan sedikit bayangan */
            transition: background-color 0.3s, transform 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #back-to-top:hover {
            background-color: #4299E1; /* Sedikit lebih gelap dari blue-400 untuk hover (blue-500) */
            transform: translateY(-3px);
        }

        /* Tombol Floating WhatsApp */
        .whatsapp-float {
            position: fixed;
            width: 50px; /* Samakan dengan back-to-top */
            height: 50px; /* Samakan dengan back-to-top */
            bottom: 20px;
            left: 30px; /* Pindah ke kiri */
            background-color: #25D366; /* Warna WhatsApp hijau */
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 24px; /* Sesuaikan ukuran ikon */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Tambahkan bayangan */
            border: none;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s, transform 0.3s;
        }

        .whatsapp-float:hover {
            background-color: #1DA851; /* Sedikit lebih gelap saat hover */
            transform: translateY(-3px);
        }

        /* Navbar Hamburger/Menu button style */
        #mobile-menu-button {
            border: none;
            border-radius: 8px;
            padding: 8px;
            color: #60A5FA; /* Mengubah warna ikon hamburger menjadi blue-400 */
        }

        /* Mobile Menu Sidebar */
        #mobile-menu-sidebar {
            border-left: none;
            background-color: #60A5FA; /* Mengubah background sidebar menjadi blue-400 */
        }

        #mobile-menu-sidebar a {
            border-bottom: none;
        }
    </style>
</head>
<body class="text-gray-900">

    {{-- Floating Navbar --}}
    <nav class="fixed top-4 z-50 px-4 w-full md:w-auto md:max-w-screen-xl md:left-1/2 md:-translate-x-1/2">
        <div class="bg-white p-4 rounded-full shadow-lg flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-3xl font-extrabold whitespace-nowrap mr-6 md:mr-10 algiotrans-title">Algio Trans</a>

            {{-- Desktop Navigation Links --}}
            <div class="hidden md:flex items-center space-x-6">
                <a href="#" class="text-gray-700 font-semibold hover:text-blue-400 transition duration-200">Tentang Kami</a>
                <a href="#" class="text-gray-700 font-semibold hover:text-blue-400 transition duration-200">Layanan</a>
                <a href="#" class="text-gray-700 font-semibold hover:text-blue-400 transition duration-200">FAQ</a>
                <a href="#" class="text-gray-700 font-semibold hover:text-blue-400 transition duration-200">Kontak</a>
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
    <div id="mobile-menu-sidebar" class="fixed top-0 right-0 h-full w-64 text-white z-50 shadow-lg mobile-menu md:hidden">
        <div class="p-6 flex justify-end">
            <button id="close-mobile-menu" class="text-white text-2xl focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <nav class="flex flex-col items-center space-y-6 text-lg">
            <a href="#" class="w-full text-center py-3 hover:bg-blue-500 transition duration-200">Tentang Kami</a>
            <a href="#" class="w-full text-center py-3 hover:bg-blue-500 transition duration-200">Layanan</a>
            <a href="#" class="w-full text-center py-3 hover:bg-blue-500 transition duration-200">FAQ</a>
            <a href="#" class="w-full text-center py-3 hover:bg-blue-500 transition duration-200">Kontak</a>
        </nav>
    </div>

    {{-- Mengurangi pt-24 menjadi pt-20 untuk mengurangi jarak --}}
    <main class="pt-20 pb-8">
        @yield('content')
    </main>

    {{-- Batas Footer dengan Garis Putus-putus --}}
    <hr class="border-t-2 border-dashed border-sky-300 mx-auto max-w-7xl mb-8">

    {{-- Footer Section --}}
    <footer class="bg-white text-gray-800 py-12">
        <div class="container mx-auto px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 md:gap-x-12">
            {{-- Company Info --}}
            <div class="md:text-left text-center">
                <h3 class="text-2xl font-extrabold mb-4 algiotrans-title">Algio Trans</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Solusi terdepan untuk perjalanan nyaman dan pengiriman paket aman. Kami hadir untuk memudahkan setiap perjalanan Anda.
                </p>
            </div>

            {{-- Quick Links --}}
            <div class="md:text-left text-center">
                <h3 class="text-xl font-bold mb-4 text-blue-400">Tautan Cepat</h3>
                <ul class="space-y-2 text-gray-600">
                    <li><a href="#" class="hover:text-blue-400 transition duration-200">Tentang Kami</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition duration-200">Layanan Kami</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition duration-200">Jadwal & Rute</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition duration-200">Kebijakan Privasi</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition duration-200">Syarat & Ketentuan</a></li>
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
                        info@algiotrans.com
                    </li>
                </ul>
            </div>

            {{-- Social Media --}}
            <div class="md:text-left text-center">
                <h3 class="text-xl font-bold mb-4 text-blue-400">Ikuti Kami</h3>
                <div class="flex justify-center md:justify-start space-x-4">
                    <a href="#" class="text-gray-600 hover:text-blue-400 text-2xl transition duration-200"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-gray-600 hover:text-blue-400 text-2xl transition duration-200"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-gray-600 hover:text-blue-400 text-2xl transition duration-200"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-gray-600 hover:text-blue-400 text-2xl transition duration-200"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 mt-8 pt-6 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} Algio Trans. All rights reserved.
        </div>
    </footer>

    {{-- Floating WhatsApp Button --}}
    <a href="https://wa.me/6282117999587?text={{ urlencode('Halo Algio Trans, saya ingin bertanya tentang...') }}" class="whatsapp-float" target="_blank" aria-label="WhatsApp Algio Trans">
        <i class="fab fa-whatsapp"></i>
    </a>

    {{-- Back to Top Button --}}
    <button onclick="topFunction()" id="back-to-top" title="Go to top">
        <i class="fas fa-arrow-up"></i>
    </button>

    @vite('resources/js/app.js')
    {{-- AOS Library JS --}}
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS here as well, if not already in content section
        AOS.init({
            duration: 1000,
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

        if (mobileMenuButton) { // Ensure button exists before adding listener
            mobileMenuButton.addEventListener('click', toggleMobileMenu);
        }
        if (closeMobileMenuButton) { // Ensure button exists
            closeMobileMenuButton.addEventListener('click', toggleMobileMenu);
        }
        if (mobileMenuOverlay) { // Ensure overlay exists
            mobileMenuOverlay.addEventListener('click', toggleMobileMenu); // Close when clicking outside
        }

        // Close mobile menu when a link is clicked (optional, if links navigate)
        document.querySelectorAll('#mobile-menu-sidebar a').forEach(link => {
            link.addEventListener('click', () => {
                if (mobileMenuSidebar.classList.contains('active')) {
                    toggleMobileMenu();
                }
            });
        });

        // Back to Top Button Logic
        const backToTopButton = document.getElementById("back-to-top");

        // When the user scrolls down 20px from the top of the document, show the button
        window.onscroll = function() { scrollFunction() };

        function scrollFunction() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                backToTopButton.style.display = "flex";
            } else {
                backToTopButton.style.display = "none";
            }
        }

        // When the user clicks on the button, scroll to the top of the document (smooth scroll)
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