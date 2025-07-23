@extends('layouts.app')

@section('title', 'Pilih Kursi')

@section('content')
{{-- Loading Spinner Overlay --}}
<div id="loading-overlay" class="fixed inset-0 bg-white bg-opacity-90 flex items-center justify-center z-[9999] transition-opacity duration-300">
    <div class="flex flex-col items-center">
        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-sky-500"></div>
        <p class="mt-4 text-lg font-semibold text-sky-700">Memuat pilihan kursi...</p>
    </div>
</div>

<div class="container mx-auto px-4 py-12">
    <div class="max-w-2xl mx-auto bg-white p-8 md:p-10 rounded-3xl shadow-2xl border-2 border-sky-300">
        <h2 class="text-4xl md:text-5xl font-extrabold text-center mb-8 text-sky-700 algiotrans-title-text">Pilih Kursi Anda</h2>

        {{-- Error/Validation Message from Laravel --}}
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-lg relative mb-6 shadow-sm" role="alert">
                <strong class="font-bold">Oops! Ada masalah:</strong>
                <ul class="mt-3 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <p class="text-center text-gray-700 text-lg mb-8">
            Anda perlu memilih <strong class="text-sky-600">{{ $booking->number_of_passengers }}</strong> kursi.
        </p>

        <form id="seat-selection-form" action="{{ route('booking.save-seats', $booking->ulid) }}" method="POST">
            @csrf

            {{-- Vehicle Layout Container --}}
            <div class="bg-sky-50 p-6 md:p-8 rounded-xl border-2 border-dashed border-sky-300 relative mb-8 overflow-hidden">
                <div class="grid grid-cols-5 gap-4 md:gap-6 relative z-10 p-2 md:p-4">

                    <div class="col-start-2 flex justify-center"> {{-- Kursi 1 --}}
                        <div class="seat w-16 h-16 md:w-20 md:h-20 flex flex-col items-center justify-center border-2 rounded-lg cursor-pointer text-center text-base font-bold transition-all duration-200
                            {{ in_array(1, $availableSeats) ? 'bg-sky-200 border-sky-500 hover:bg-sky-300 text-sky-800' : 'bg-red-200 border-red-500 cursor-not-allowed opacity-70 text-red-800' }}"
                            data-seat-number="1" data-available="{{ in_array(1, $availableSeats) ? 'true' : 'false' }}">
                            1
                        </div>
                    </div>
                    <div class="col-start-3"></div> {{-- Aisle space --}}
                    <div class="col-start-4 flex justify-center"> {{-- Sopir --}}
                        <div class="seat-driver w-16 h-16 md:w-20 md:h-20 flex flex-col items-center justify-center bg-gray-600 text-white rounded-lg text-sm font-semibold border border-gray-700 shadow-inner">
                            Sopir
                        </div>
                    </div>

                    <div class="col-start-2 flex justify-center"> {{-- Kursi 2 --}}
                        <div class="seat w-16 h-16 md:w-20 md:h-20 flex flex-col items-center justify-center border-2 rounded-lg cursor-pointer text-center text-base font-bold transition-all duration-200
                            {{ in_array(2, $availableSeats) ? 'bg-sky-200 border-sky-500 hover:bg-sky-300 text-sky-800' : 'bg-red-200 border-red-500 cursor-not-allowed opacity-70 text-red-800' }}"
                            data-seat-number="2" data-available="{{ in_array(2, $availableSeats) ? 'true' : 'false' }}">
                            2
                        </div>
                    </div>
                    <div class="col-start-3 flex justify-center"> {{-- Kursi 3 --}}
                        <div class="seat w-16 h-16 md:w-20 md:h-20 flex flex-col items-center justify-center border-2 rounded-lg cursor-pointer text-center text-base font-bold transition-all duration-200
                            {{ in_array(3, $availableSeats) ? 'bg-sky-200 border-sky-500 hover:bg-sky-300 text-sky-800' : 'bg-red-200 border-red-500 cursor-not-allowed opacity-70 text-red-800' }}"
                            data-seat-number="3" data-available="{{ in_array(3, $availableSeats) ? 'true' : 'false' }}">
                            3
                        </div>
                    </div>
                    <div class="col-start-4 flex justify-center"> {{-- Kursi 4 --}}
                        <div class="seat w-16 h-16 md:w-20 md:h-20 flex flex-col items-center justify-center border-2 rounded-lg cursor-pointer text-center text-base font-bold transition-all duration-200
                            {{ in_array(4, $availableSeats) ? 'bg-sky-200 border-sky-500 hover:bg-sky-300 text-sky-800' : 'bg-red-200 border-red-500 cursor-not-allowed opacity-70 text-red-800' }}"
                            data-seat-number="4" data-available="{{ in_array(4, $availableSeats) ? 'true' : 'false' }}">
                            4
                        </div>
                    </div>

                    <div class="col-start-2 flex justify-center"> {{-- Kursi 5 --}}
                        <div class="seat w-16 h-16 md:w-20 md:h-20 flex flex-col items-center justify-center border-2 rounded-lg cursor-pointer text-center text-base font-bold transition-all duration-200
                            {{ in_array(5, $availableSeats) ? 'bg-sky-200 border-sky-500 hover:bg-sky-300 text-sky-800' : 'bg-red-200 border-red-500 cursor-not-allowed opacity-70 text-red-800' }}"
                            data-seat-number="5" data-available="{{ in_array(5, $availableSeats) ? 'true' : 'false' }}">
                            5
                        </div>
                    </div>
                    <div class="col-start-3 flex justify-center"> {{-- Kursi 6 --}}
                        <div class="seat w-16 h-16 md:w-20 md:h-20 flex flex-col items-center justify-center border-2 rounded-lg cursor-pointer text-center text-base font-bold transition-all duration-200
                            {{ in_array(6, $availableSeats) ? 'bg-sky-200 border-sky-500 hover:bg-sky-300 text-sky-800' : 'bg-red-200 border-red-500 cursor-not-allowed opacity-70 text-red-800' }}"
                            data-seat-number="6" data-available="{{ in_array(6, $availableSeats) ? 'true' : 'false' }}">
                            6
                        </div>
                    </div>
                    <div class="col-start-4 flex justify-center"> {{-- Kursi 7 --}}
                        <div class="seat w-16 h-16 md:w-20 md:h-20 flex flex-col items-center justify-center border-2 rounded-lg cursor-pointer text-center text-base font-bold transition-all duration-200
                            {{ in_array(7, $availableSeats) ? 'bg-sky-200 border-sky-500 hover:bg-sky-300 text-sky-800' : 'bg-red-200 border-red-500 cursor-not-allowed opacity-70 text-red-800' }}"
                            data-seat-number="7" data-available="{{ in_array(7, $availableSeats) ? 'true' : 'false' }}">
                            7
                        </div>
                    </div>

                </div>
            </div> {{-- End Vehicle Layout Container --}}

            {{-- Legenda Kursi --}}
            <div class="flex flex-wrap justify-center space-x-4 md:space-x-8 text-lg mb-8 p-4 bg-sky-50 rounded-lg border border-dashed border-sky-200">
                <div class="flex items-center mb-2 md:mb-0">
                    <span class="w-6 h-6 rounded-full bg-sky-200 border-2 border-sky-500 mr-2 shadow-sm"></span>
                    <span>Tersedia</span>
                </div>
                <div class="flex items-center mb-2 md:mb-0">
                    <span class="w-6 h-6 rounded-full bg-indigo-400 border-2 border-indigo-600 mr-2 shadow-sm"></span>
                    <span>Terpilih</span>
                </div>
                <div class="flex items-center mb-2 md:mb-0">
                    <span class="w-6 h-6 rounded-full bg-red-200 border-2 border-red-500 mr-2 shadow-sm"></span>
                    <span>Tidak Tersedia</span>
                </div>
            </div>

            {{-- Hidden input untuk menyimpan kursi terpilih --}}
            <div id="selected-seats-container"></div>

            <button type="submit" id="save-seats-button" class="w-full bg-sky-500 text-white py-3 rounded-lg text-xl font-bold hover:bg-sky-600 transition duration-300 ease-in-out shadow-lg transform hover:scale-105 flex items-center justify-center">
                <span id="save-button-text">Simpan Pilihan Kursi</span>
                <svg id="save-loading-spinner" class="animate-spin -ml-1 mr-3 h-6 w-6 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </form>
    </div>
</div>

{{-- Custom Toast Notification HTML --}}
<div id="toast-notification" class="fixed top-4 right-4 z-[9999] p-4 rounded-lg shadow-lg flex items-center space-x-3
    transform translate-x-full transition-transform duration-300 ease-out opacity-0 pointer-events-none">
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
        // --- Page Load Loading Overlay Logic ---
        const loadingOverlay = document.getElementById('loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.style.opacity = '0'; // Start fading out immediately
            setTimeout(() => {
                loadingOverlay.style.display = 'none'; // Hide completely after transition
            }, 300); // Matches CSS transition duration
        }
        // --- End Page Load Loading Overlay Logic ---

        const seatElements = document.querySelectorAll('.seat');
        const selectedSeatsContainer = document.getElementById('selected-seats-container');
        const maxSeats = {{ $booking->number_of_passengers }};
        let selectedSeats = [];

        // Initialize selected seats from the hidden inputs if they exist (e.g., after validation error)
        document.querySelectorAll('input[name="selected_seats[]"]').forEach(input => {
            selectedSeats.push(parseInt(input.value));
            const seatElement = document.querySelector(`.seat[data-seat-number="${input.value}"]`);
            if (seatElement) {
                seatElement.classList.remove('bg-sky-200', 'border-sky-500');
                seatElement.classList.add('bg-indigo-400', 'border-indigo-600');
            }
        });


        // --- Form Submission Loading Logic (for the submit button) ---
        const seatSelectionForm = document.getElementById('seat-selection-form');
        const saveSeatsButton = document.getElementById('save-seats-button');
        const saveButtonText = document.getElementById('save-button-text');
        const saveLoadingSpinner = document.getElementById('save-loading-spinner');

        if (seatSelectionForm && saveSeatsButton) {
            seatSelectionForm.addEventListener('submit', () => {
                saveButtonText.textContent = 'Menyimpan...';
                saveLoadingSpinner.classList.remove('hidden');
                saveSeatsButton.disabled = true;
                saveSeatsButton.classList.add('opacity-75', 'cursor-not-allowed');
            });
        }
        // --- End Form Submission Loading Logic ---

        // --- Toast Notification Logic ---
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

            // Reset classes
            toastNotification.className = 'fixed top-4 right-4 z-[9999] p-4 rounded-lg shadow-lg flex items-center space-x-3 transform transition-transform duration-300 ease-out';
            toastIcon.className = 'fas text-2xl';

            // Apply type-specific classes
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

            // Show the toast by removing hidden state and making it visible
            toastNotification.classList.remove('translate-x-full');
            toastNotification.classList.add('opacity-100', 'pointer-events-auto');
            // Remove display: none if it was set
            toastNotification.style.display = 'flex'; // Use flex so content is centered

            toastTimeout = setTimeout(() => {
                hideToast();
            }, duration);
        }

        function hideToast() {
            // Start hiding animation
            toastNotification.classList.add('translate-x-full');
            toastNotification.classList.remove('opacity-100', 'pointer-events-auto');

            // Wait for transition to complete, then set display to none
            toastNotification.addEventListener('transitionend', function handler() {
                if (toastNotification.classList.contains('translate-x-full')) {
                    toastNotification.style.display = 'none';
                    // Remove the event listener itself after it fires once
                    toastNotification.removeEventListener('transitionend', handler);
                }
            });
        }

        toastCloseButton.addEventListener('click', () => {
            clearTimeout(toastTimeout);
            hideToast();
        });


        seatElements.forEach(seat => {
            if (seat.dataset.available === 'true') {
                seat.addEventListener('click', () => {
                    const seatNumber = parseInt(seat.dataset.seatNumber);

                    if (selectedSeats.includes(seatNumber)) {
                        selectedSeats = selectedSeats.filter(n => n !== seatNumber);
                        seat.classList.remove('bg-indigo-400', 'border-indigo-600');
                        seat.classList.add('bg-sky-200', 'border-sky-500');
                    } else {
                        if (selectedSeats.length >= maxSeats) {
                            showToast('warning', 'Peringatan!', `Anda hanya dapat memilih ${maxSeats} kursi.`);
                            return;
                        }

                        selectedSeats.push(seatNumber);
                        seat.classList.remove('bg-sky-200', 'border-sky-500');
                        seat.classList.add('bg-indigo-400', 'border-indigo-600');
                    }

                    updateHiddenInputs();
                });
            }
        });

        function updateHiddenInputs() {
            selectedSeatsContainer.innerHTML = '';
            selectedSeats.sort((a, b) => a - b);
            selectedSeats.forEach(seat => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_seats[]';
                input.value = seat;
                selectedSeatsContainer.appendChild(input);
            });
        }
    });
</script>

<style>
    /* Gradient text untuk judul utama */
    .algiotrans-title-text {
        background: linear-gradient(45deg, #87CEEB, #A0D8EF, #B2EBF2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-family: 'Montserrat', sans-serif;
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