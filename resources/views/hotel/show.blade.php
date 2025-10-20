@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;700&family=Inter:wght@400;700&family=Signika+SC&family=Sedan+SC&family=Spinnaker&family=Sora&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    /* Smooth, accessible modal visuals */
    .modal-backdrop { backdrop-filter: blur(8px); animation: fadeIn 0.25s ease; }
    .modal-content { animation: slideUp 0.32s ease; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(24px) scale(0.98); } to { opacity: 1; transform: translateY(0) scale(1); } }

    /* Steps */
    .step-indicator { width: 40px; height: 40px; border-radius: 999px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; transition: all 0.2s ease; }
    .step-indicator.active { background: linear-gradient(135deg, #F97316, #FB923C); color: #fff; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.35); }
    .step-indicator.completed { background: #10B981; color: #fff; }
    .step-indicator.pending { background: #E5E7EB; color: #9CA3AF; }

    /* Scrollable modal body */
    .booking-modal { max-height: 90vh; overflow-y: auto; font-family: 'Poppins', sans-serif; }
    .booking-modal::-webkit-scrollbar { width: 8px; }
    .booking-modal::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .booking-modal::-webkit-scrollbar-thumb { background: #F97316; border-radius: 10px; }

    .step-content { min-height: 400px; }

    .payment-option { transition: all 0.2s ease; cursor: pointer; }
    .payment-option.selected { border-color: #F97316 !important; background: #FFF7ED; }

    .loading-spinner { border: 3px solid #f3f3f3; border-top: 3px solid #F97316; border-radius: 50%; width: 20px; height: 20px; animation: spin 1s linear infinite; display: inline-block; }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

    .category-tab { transition: all 0.2s ease; cursor: pointer; }
    .category-tab.active { background: linear-gradient(135deg, #F97316, #FB923C); color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(249, 115, 22, 0.35); }

    .alert-toast { position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; animation: slideInRight 0.25s ease; }
    @keyframes slideInRight { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
</style>

<div class="bg-gradient-to-b from-gray-100 to-gray-200 min-h-screen font-[Inter] text-black">
    <div class="w-full bg-white min-h-screen">

        <!-- Hero Image -->
        <section class="relative w-full px-4 sm:px-6 lg:px-8 pt-6">
            <div class="relative h-[280px] sm:h-[350px] md:h-[420px] lg:h-[480px] overflow-hidden rounded-3xl border-8 border-orange-100">
                <img src="{{ asset($facility->image ?? 'images/placeholder.jpg') }}" alt="{{ $facility->name }}" class="w-full h-full object-cover">
                <div class="absolute bottom-4 left-4 sm:bottom-6 sm:left-6 text-white text-sm font-semibold bg-black/60 backdrop-blur-sm rounded-2xl px-6 py-3">
                    <p class="text-xs sm:text-sm opacity-90">Average price</p>
                    <p class="text-base sm:text-xl font-bold">{{ number_format($facility->rooms->avg('price_rwf') ?? 175000) }} RWF / {{ $facility->rooms->avg('price_usd') ?? 70 }}$</p>
                </div>
            </div>
        </section>

        <!-- Hotel Description Card -->
        <section class="px-4 sm:px-6 lg:px-8 -mt-16 sm:-mt-20 relative z-10 pb-6 sm:pb-8">
            <div class="max-w-4xl mx-auto bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl shadow-2xl border-2 border-orange-200 p-5 sm:p-8 relative">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900 uppercase tracking-wide">{{ $facility->name }}</h1>
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-orange-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                    <div class="flex items-center gap-0.5" aria-label="Hotel rating: 4 stars">
                        @for($i = 0; $i < 4; $i++)
                            <span class="text-orange-500 text-lg sm:text-xl" aria-hidden="true">★</span>
                        @endfor
                        <span class="text-gray-400 text-xs sm:text-sm ml-1">Stars</span>
                    </div>
                </div>

                <div class="w-full h-0.5 bg-gradient-to-r from-orange-500 to-orange-300 rounded-full mb-4"></div>
                <h2 class="text-base sm:text-lg md:text-xl font-bold text-orange-600 mb-3">Hotel Description</h2>

                <div class="bg-white/70 backdrop-blur-sm rounded-xl p-4 sm:p-6 shadow-sm mb-4">
                    <p class="text-gray-800 leading-relaxed text-xs sm:text-sm md:text-base" style="font-family: 'Sedan SC', serif;">
                        <span class="font-semibold text-gray-900">{{ strtoupper(explode(' ', $facility->name)[0] ?? 'HOTEL') }}</span>
                        {{ $facility->description ?? 'is a four stars hotel located in the center of the city with amazing facilities.' }}
                    </p>
                </div>

                <div class="text-right">
                    <a href="{{ url('/hotels') }}" class="inline-flex items-center text-orange-600 hover:text-orange-700 font-semibold text-xs sm:text-sm group">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Back to Hotels
                    </a>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section class="px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-xl sm:text-2xl md:text-3xl font-bold mb-4 text-gray-800">Services we offer</h2>
                <div class="h-0.5 w-full bg-gray-800 mb-5"></div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4">
                    @php
                        $services = [
                            'Free car parking' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 sm:w-14 sm:h-14" fill="none" viewBox="0 0 24 24" stroke="#F97316" stroke-width="1.5" role="img" aria-label="Free car parking"><rect x="3" y="11" width="18" height="6" rx="2"/><circle cx="7" cy="19" r="2"/><circle cx="17" cy="19" r="2"/><path d="M3 11V6a2 2 0 012-2h14a2 2 0 012 2v5"/></svg>',
                            'Swimming Pool' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 sm:w-14 sm:h-14" fill="none" viewBox="0 0 24 24" stroke="#F97316" stroke-width="1.5" role="img" aria-label="Swimming pool"><path d="M2 15c1.67-1.33 3.33-1.33 5 0s3.33 1.33 5 0 3.33-1.33 5 0 3.33 1.33 5 0M2 19c1.67-1.33 3.33-1.33 5 0s3.33 1.33 5 0 3.33-1.33 5 0 3.33 1.33 5 0M20 12V8l-3-3-4 4-4-4-3 3v4"/></svg>',
                            'Fitness center' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 sm:w-14 sm:h-14" fill="none" viewBox="0 0 24 24" stroke="#F97316" stroke-width="1.5" role="img" aria-label="Fitness center"><path d="M7.5 12h9M6.5 9h11M6.5 15h11"/><rect x="2" y="8" width="3" height="8" rx="1"/><rect x="19" y="8" width="3" height="8" rx="1"/></svg>',
                            'Free wifi' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 sm:w-14 sm:h-14" fill="none" viewBox="0 0 24 24" stroke="#F97316" stroke-width="1.5" role="img" aria-label="Free wifi"><path d="M2 8.5c5-5 15-5 20 0M5 12c3.5-3.5 10.5-3.5 14 0M8.5 15.5c2-2 5-2 7 0"/><circle cx="12" cy="20" r="1" fill="#F97316"/></svg>',
                            'Meetings' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 sm:w-14 sm:h-14" fill="none" viewBox="0 0 24 24" stroke="#F97316" stroke-width="1.5" role="img" aria-label="Meetings"><circle cx="9" cy="7" r="3"/><circle cx="15" cy="7" r="3"/><path d="M3 18c0-2.5 2.5-4 6-4s6 1.5 6 4v2H3v-2zM15 18c0-2.5 2-4 5-4s4 1.5 4 4v2h-9v-2z"/></svg>',
                            'Yoga class for adults' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 sm:w-14 sm:h-14" fill="none" viewBox="0 0 24 24" stroke="#F97316" stroke-width="1.5" role="img" aria-label="Yoga class"><circle cx="12" cy="6" r="3"/><path d="M12 10v5m-4 4l4-4m0 0l4 4M8 15l4-4 4 4"/></svg>',
                        ];
                    @endphp

                    @foreach($facility->services as $service)
                        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-md hover:border-orange-300 transition-all duration-200 p-4 sm:p-5 flex flex-col items-center text-center">
                            <div class="mb-2">{!! $services[$service->name] ?? '<svg class="w-12 h-12 sm:w-14 sm:h-14" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#F97316" stroke-width="1.5" viewBox="0 0 24 24" role="img" aria-label="Service"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>' !!}</div>
                            <p class="text-xs sm:text-sm font-semibold text-gray-700">{{ $service->name }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Explore Rooms Button -->
        <div class="text-center py-6 sm:py-8">
            <a href="#rooms" class="inline-block bg-gradient-to-r from-orange-500 to-orange-600 text-white px-10 sm:px-16 py-3 sm:py-4 rounded-full text-sm sm:text-base md:text-lg font-bold shadow-lg hover:shadow-xl hover:from-orange-600 hover:to-orange-700 transition-all duration-200 transform hover:scale-105">
                Explore our rooms
            </a>
        </div>

        <!-- Rooms Section with Categories -->
        <section id="rooms" class="px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-xl sm:text-2xl md:text-3xl font-bold mb-6 text-gray-800">Our Rooms</h2>

                <!-- Category Tabs -->
                <div class="flex flex-wrap gap-3 mb-8 bg-gray-100 p-4 rounded-2xl">
                    <button type="button" onclick="filterRooms('all')" class="category-tab active px-6 py-3 rounded-xl font-bold text-sm transition-all">All Rooms</button>
                    @php $categories = $facility->rooms->pluck('category')->unique()->filter(); @endphp
                    @foreach($categories as $category)
                        <button type="button" onclick="filterRooms('{{ $category }}')" class="category-tab px-6 py-3 rounded-xl font-bold text-sm bg-white text-gray-700 hover:bg-orange-100 transition-all">{{ $category }}</button>
                    @endforeach
                </div>

                <div class="space-y-5 sm:space-y-6">
                    @foreach($facility->rooms as $room)
                        <div class="room-card bg-gradient-to-br from-orange-50 to-amber-50 border-3 border-orange-300 rounded-3xl shadow-lg overflow-hidden relative" data-category="{{ $room->category }}">
                            <div class="flex items-center justify-between bg-gradient-to-r from-amber-100 to-orange-100 px-4 sm:px-6 py-3 sm:py-4 border-b border-orange-200">
                                <div class="flex items-center gap-3">
                                    <h3 class="text-base sm:text-lg md:text-xl font-bold text-gray-800">{{ $room->name }}</h3>
                                    @if($room->category)
                                        <span class="bg-orange-500 text-white px-3 py-1 rounded-full text-xs font-semibold">{{ $room->category }}</span>
                                    @endif
                                </div>
                                @auth
                                    <button type="button" onclick="openBookingModal({{ $room->id }}, '{{ $room->name }}', {{ (int) ($room->price_rwf ?? 0) }}, {{ (int) ($room->price_usd ?? 0) }}, {{ (int) ($room->availability ?? 0) }})" class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 sm:px-8 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-bold hover:from-orange-600 hover:to-orange-700 shadow-md transition-all duration-200">Book now</button>
                                @else
                                    <button type="button" onclick="openLoginModal('{{ $room->name }}')" class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 sm:px-8 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-bold hover:from-orange-600 hover:to-orange-700 shadow-md transition-all duration-200">Book now</button>
                                @endauth
                            </div>

                            <div class="p-4 sm:p-6">
                                <div class="flex flex-col md:flex-row gap-3 sm:gap-5 mb-4">
                                    <div class="w-full md:w-48 lg:w-56 h-36 sm:h-40 md:h-32 lg:h-36 flex-shrink-0">
                                        <img src="{{ asset($room->image ?? 'images/placeholder.jpg') }}" alt="{{ $room->name }}" class="w-full h-full object-cover rounded-xl shadow-md border-2 border-white">
                                    </div>

                                    <div class="flex-1">
                                        <h4 class="text-orange-600 font-bold text-sm sm:text-base mb-2">Room Description</h4>
                                        <div class="bg-white/70 backdrop-blur-sm rounded-lg p-2.5 sm:p-4 shadow-sm">
                                            <p class="text-gray-800 leading-relaxed text-xs sm:text-sm">{{ $room->description ?? 'Comfortable room with modern amenities' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-1.5 sm:space-y-2 bg-white/60 rounded-xl p-3 sm:p-4 shadow-sm">
                                    <div class="flex items-baseline justify-between">
                                        <span class="text-gray-700 font-semibold text-xs sm:text-sm">Room price</span>
                                        <span class="text-gray-900 font-bold text-sm sm:text-base">{{ number_format($room->price_rwf ?? 0) }} RWF</span>
                                    </div>
                                    <div class="flex items-baseline justify-end">
                                        <span class="text-gray-600 font-medium text-xs sm:text-sm">{{ $room->price_usd ?? 0 }} USD</span>
                                    </div>
                                    <div class="border-t border-gray-300 pt-1.5 sm:pt-2 mt-1.5 sm:mt-2">
                                        <div class="flex items-baseline justify-between">
                                            <span class="text-gray-700 font-semibold text-xs sm:text-sm">Room availability</span>
                                            <span class="text-gray-900 font-bold text-sm sm:text-base">{{ $room->availability ?? 0 }} Rooms</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Gallery Section -->
        @if($facility->gallery->count() > 0)
        <section class="px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-xl sm:text-2xl md:text-3xl font-bold mb-4 text-gray-800">Our Gallery</h2>
                <div class="h-0.5 w-full bg-gray-800 mb-5"></div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 sm:gap-4">
                    @foreach($facility->gallery as $img)
                        <div class="overflow-hidden rounded-xl shadow-md hover:shadow-lg transition-all duration-200 border-2 border-gray-200 hover:border-orange-300">
                            <img src="{{ asset($img->image) }}" alt="Gallery Image" class="w-full h-32 sm:h-40 object-cover">
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- Footer -->
        <footer class="border-t border-gray-300 py-4 bg-gray-50">
            <p class="text-center text-gray-600 text-xs sm:text-sm">Copyright © {{ date('Y') }} Design by B.Moise</p>
        </footer>

    </div>
</div>

<!-- Login Required Modal -->
<div id="loginModal" class="modal-backdrop hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" role="dialog" aria-modal="true" aria-labelledby="loginModalTitle">
    <div class="modal-content bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 relative">
        <button type="button" onclick="closeLoginModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition" aria-label="Close login modal">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <div class="flex justify-center mb-6">
            <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center" aria-hidden="true">
                <svg class="w-10 h-10 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
        </div>

        <h3 id="loginModalTitle" class="text-2xl font-bold text-center text-gray-800 mb-3">Login Required</h3>
        <p class="text-center text-gray-600 mb-8 leading-relaxed">
            To book a room in the hotel<br>
            you need to <span class="font-semibold text-orange-500">login</span> to your<br>
            account first.
        </p>

        <a href="{{ route('login') }}" class="block w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-4 rounded-xl font-semibold text-lg shadow-lg text-center hover:from-orange-600 hover:to-orange-700 transition-all duration-200 transform hover:scale-105">
            Login
        </a>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-orange-500 font-semibold hover:underline">Sign up</a>
            </p>
        </div>
    </div>
</div>

<!-- Booking Modal -->
<div id="bookingModal" class="modal-backdrop hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" role="dialog" aria-modal="true" aria-labelledby="bookingModalTitle">
    <div class="booking-modal bg-white rounded-2xl shadow-2xl max-w-4xl w-full relative max-h-[90vh] overflow-y-auto">
        <button type="button" onclick="closeBookingModal()" class="sticky top-4 float-right z-10 text-gray-400 hover:text-gray-600 transition bg-white rounded-full p-2 shadow-lg mr-4" aria-label="Close booking modal">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <div class="p-8" id="bookingModalBody" tabindex="-1">
            <!-- Progress Steps -->
            <div class="flex items-center justify-between mb-8 overflow-x-auto pb-2" aria-label="Booking steps">
                <div class="flex items-center gap-2 min-w-max">
                    <div class="step-indicator active" id="b-step-indicator-1">1</div>
                    <span class="text-xs font-medium text-gray-700 hidden sm:inline">Reservation</span>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-2"><div class="h-full bg-orange-500 transition-all" id="b-progress-1" style="width: 0%"></div></div>

                <div class="flex items-center gap-2 min-w-max">
                    <div class="step-indicator pending" id="b-step-indicator-2">2</div>
                    <span class="text-xs font-medium text-gray-700 hidden sm:inline">Room Info</span>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-2"><div class="h-full bg-orange-500 transition-all" id="b-progress-2" style="width: 0%"></div></div>

                <div class="flex items-center gap-2 min-w-max">
                    <div class="step-indicator pending" id="b-step-indicator-3">3</div>
                    <span class="text-xs font-medium text-gray-700 hidden sm:inline">Guest Info</span>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-2"><div class="h-full bg-orange-500 transition-all" id="b-progress-3" style="width: 0%"></div></div>

                <div class="flex items-center gap-2 min-w-max">
                    <div class="step-indicator pending" id="b-step-indicator-4">4</div>
                    <span class="text-xs font-medium text-gray-700 hidden sm:inline">Payment</span>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-2"><div class="h-full bg-orange-500 transition-all" id="b-progress-4" style="width: 0%"></div></div>

                <div class="flex items-center gap-2 min-w-max">
                    <div class="step-indicator pending" id="b-step-indicator-5">5</div>
                    <span class="text-xs font-medium text-gray-700 hidden sm:inline">Done</span>
                </div>
            </div>

            <form id="booking-form" action="{{ route('bookings.store') }}" method="POST" data-prevent-double-submit>
                @csrf
                <input type="hidden" name="room_id" id="booking-room-id">
                <input type="hidden" name="facility_id" value="{{ $facility->id }}">
                <input type="hidden" name="total_price_rwf" id="total-price-rwf">
                <input type="hidden" name="total_price_usd" id="total-price-usd">
                <input type="hidden" name="nights" id="nights-count">

                <!-- Step 1: Reservation Details -->
                <div id="b-step-1" class="step-content">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="text-4xl" aria-hidden="true">📅</span>
                        <h2 id="bookingModalTitle" class="text-2xl font-bold text-gray-800">Reservation Details</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="b-checkin" class="block text-sm font-semibold text-gray-700 mb-2">Check-in Date & Time</label>
                            <input type="datetime-local" name="checkin_date" id="b-checkin" required class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:border-orange-500 transition">
                        </div>

                        <div>
                            <label for="b-checkout" class="block text-sm font-semibold text-gray-700 mb-2">Checkout Date & Time</label>
                            <input type="datetime-local" name="checkout_date" id="b-checkout" required class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:border-orange-500 transition">
                        </div>

                        <div>
                            <label for="b-adults" class="block text-sm font-semibold text-gray-700 mb-2">Number of Adults</label>
                            <select name="adults" id="b-adults" required class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:border-orange-500 transition">
                                <option value="">Select</option>
                                <option value="1">1 Adult</option>
                                <option value="2">2 Adults</option>
                                <option value="3">3 Adults</option>
                                <option value="4">4 Adults</option>
                            </select>
                        </div>

                        <div>
                            <label for="b-children" class="block text-sm font-semibold text-gray-700 mb-2">Number of Children</label>
                            <select name="children" id="b-children" class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:border-orange-500 transition">
                                <option value="0">0 Children</option>
                                <option value="1">1 Child</option>
                                <option value="2">2 Children</option>
                                <option value="3">3 Children</option>
                            </select>
                        </div>
                    </div>

                    <button type="button" onclick="goToBookingStep(2)" class="mt-8 w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-4 rounded-xl font-bold text-lg hover:from-orange-600 hover:to-orange-700 transition-all transform hover:scale-105 shadow-lg">
                        Proceed
                    </button>
                </div>

                <!-- Step 2: Room Information -->
                <div id="b-step-2" class="step-content hidden">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="text-4xl" aria-hidden="true">🏨</span>
                        <h2 class="text-2xl font-bold text-gray-800">Room Information</h2>
                    </div>

                    <div class="bg-orange-50 border-2 border-orange-200 rounded-xl p-6 mb-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2" id="b-room-name">Premium Room</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Price per night</p>
                                <p class="text-2xl font-bold text-orange-600" id="b-room-price-rwf">175,000 RWF</p>
                                <p class="text-lg text-gray-700" id="b-room-price-usd">70 USD</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Available</p>
                                <p class="text-xl font-semibold text-gray-800" id="b-room-availability">5 rooms</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3 bg-gray-50 rounded-xl p-6">
                        <div class="flex justify-between"><span class="text-gray-700 font-medium">Check-in:</span><span class="text-gray-900 font-semibold" id="b-display-checkin">-</span></div>
                        <div class="flex justify-between"><span class="text-gray-700 font-medium">Checkout:</span><span class="text-gray-900 font-semibold" id="b-display-checkout">-</span></div>
                        <div class="flex justify-between"><span class="text-gray-700 font-medium">Nights:</span><span class="text-gray-900 font-semibold" id="b-display-nights">-</span></div>
                        <div class="flex justify-between"><span class="text-gray-700 font-medium">Guests:</span><span class="text-gray-900 font-semibold" id="b-display-guests">-</span></div>
                        <div class="flex justify-between pt-3 border-t-2 border-orange-300"><span class="text-gray-800 font-bold text-lg">Total:</span><span class="text-orange-600 font-bold text-2xl" id="b-display-total" aria-live="polite">-</span></div>
                    </div>

                    <button type="button" onclick="goToBookingStep(3)" class="mt-8 w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-4 rounded-xl font-bold text-lg hover:from-orange-600 hover:to-orange-700 transition-all transform hover:scale-105 shadow-lg">
                        Accept & Continue
                    </button>
                </div>

                <!-- Step 3: Guest Information -->
                <div id="b-step-3" class="step-content hidden">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="text-4xl" aria-hidden="true">👤</span>
                        <h2 class="text-2xl font-bold text-gray-800">Guest Information</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="b-firstname" class="block text-sm font-semibold text-gray-700 mb-2">First Name</label>
                            <input type="text" name="guest_firstname" id="b-firstname" required placeholder="Enter first name" class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:border-orange-500 transition">
                        </div>
                        <div>
                            <label for="b-lastname" class="block text-sm font-semibold text-gray-700 mb-2">Last Name</label>
                            <input type="text" name="guest_lastname" id="b-lastname" required placeholder="Enter last name" class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:border-orange-500 transition">
                        </div>
                        <div>
                            <label for="b-email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                            <input type="email" name="guest_email" id="b-email" required placeholder="example@email.com" class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:border-orange-500 transition">
                        </div>
                        <div>
                            <label for="b-phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" name="guest_phone" id="b-phone" required placeholder="+250 xxx xxx xxx" class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:border-orange-500 transition" inputmode="tel" autocomplete="tel" pattern="^\+?[0-9\s\-]{7,15}$">
                        </div>
                        <div class="md:col-span-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" id="b-terms" required class="w-5 h-5 text-orange-500 rounded focus:ring-orange-500">
                                <span class="text-sm text-gray-700">I agree to the <a href="#" class="text-orange-500 font-semibold hover:underline">terms and conditions</a></span>
                            </label>
                        </div>
                    </div>

                    <button type="button" onclick="goToBookingStep(4)" class="mt-8 w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-4 rounded-xl font-bold text-lg hover:from-orange-600 hover:to-orange-700 transition-all transform hover:scale-105 shadow-lg">
                        Proceed to Payment
                    </button>
                </div>

                <!-- Step 4: Payment Methods -->
                <div id="b-step-4" class="step-content hidden">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="text-4xl" aria-hidden="true">💳</span>
                        <h2 class="text-2xl font-bold text-gray-800">Payment Methods</h2>
                    </div>

                    <p class="text-gray-600 mb-6">Choose your preferred payment method</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="payment-option border-2 border-gray-200 rounded-xl p-6 cursor-pointer hover:border-orange-500 transition" onclick="selectPayment('mtn', this)">
                            <div class="flex items-center justify-between mb-4">
                                <input type="radio" name="payment_method" value="mtn" id="payment-mtn" class="w-5 h-5 text-orange-500" />
                                <div class="text-3xl font-bold text-yellow-500">MTN</div>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">Enter your MTN Mobile number:</p>
                            <input type="tel" name="mtn_number" id="b-mtn-number" placeholder="078 xxx xxxx" class="w-full border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:border-orange-500" inputmode="numeric" autocomplete="tel" pattern="^0\d{9}$" maxlength="10" disabled>
                        </div>

                        <div class="payment-option border-2 border-gray-200 rounded-xl p-6 cursor-pointer hover:border-orange-500 transition" onclick="selectPayment('visa', this)">
                            <div class="flex items-center justify-between mb-4">
                                <input type="radio" name="payment_method" value="visa" id="payment-visa" class="w-5 h-5 text-orange-500" />
                                <div class="text-3xl font-bold text-blue-600">VISA</div>
                            </div>
                            <p class="text-sm text-gray-600">Secure payment with Visa</p>
                        </div>
                    </div>

                    <div class="mt-6 bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-2">📧 Invoice will be sent to: <span class="font-semibold" id="b-payment-email">-</span></p>
                        <p class="text-sm text-gray-600">💰 Total Amount: <span class="font-semibold" id="b-payment-total">-</span></p>
                    </div>

                    <!-- Transaction panel (appears during/after processing) -->
                    <div id="b-transaction-panel" class="mt-6 bg-white border-2 border-gray-200 rounded-lg p-4 hidden">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span id="b-txn-status-badge" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Processing</span>
                                <span class="text-sm text-gray-600">Payment method: <span id="b-txn-provider" class="font-semibold">-</span></span>
                            </div>
                            <div class="flex items-center gap-2" id="b-txn-spinner">
                                <span class="loading-spinner"></span>
                                <span class="text-sm text-gray-600">Awaiting confirmation...</span>
                            </div>
                        </div>
                        <div class="mt-3 text-sm text-gray-600">Transaction Ref: <span id="b-txn-ref" class="font-semibold">-</span></div>
                    </div>

                    <button type="submit" id="confirm-booking-btn" class="mt-8 w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-4 rounded-xl font-bold text-lg hover:from-orange-600 hover:to-orange-700 transition-all transform hover:scale-105 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed" data-prevent-double-click>
                        <span id="btn-text">Confirm Booking</span>
                        <span id="btn-loading" class="hidden items-center justify-center gap-2"><span class="loading-spinner"></span> Processing...</span>
                    </button>
                </div>

                <!-- Step 5: Confirmation -->
                <div id="b-step-5" class="step-content hidden">
                    <div class="text-center">
                        <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6" aria-hidden="true">
                            <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-800 mb-3">Thank You!</h2>
                        <p class="text-lg text-gray-600 mb-8">Your booking has been confirmed successfully</p>

                        <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-2xl p-8 mb-8">
                            <p class="text-sm opacity-90 mb-2">Booking Reference Number</p>
                            <p class="text-4xl font-bold mb-4" id="b-booking-ref">BK-{{ date('Y') }}-0001</p>
                            <p class="text-sm opacity-90">Please save this reference number for your records</p>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-6 mb-6 text-left">
                            <h3 class="font-bold text-gray-800 mb-4 text-lg">Booking Summary</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between"><span class="text-gray-600">Guest Name:</span><span class="font-semibold" id="b-confirm-name">-</span></div>
                                <div class="flex justify-between"><span class="text-gray-600">Room:</span><span class="font-semibold" id="b-confirm-room">-</span></div>
                                <div class="flex justify-between"><span class="text-gray-600">Check-in:</span><span class="font-semibold" id="b-confirm-checkin">-</span></div>
                                <div class="flex justify-between"><span class="text-gray-600">Checkout:</span><span class="font-semibold" id="b-confirm-checkout">-</span></div>
                                <div class="flex justify-between"><span class="text-gray-600">Guests:</span><span class="font-semibold" id="b-confirm-guests">-</span></div>
                                <div class="flex justify-between"><span class="text-gray-600">Payment Method:</span><span class="font-semibold" id="b-confirm-payment-method">-</span></div>
                                <div class="flex justify-between"><span class="text-gray-600">Payment Status:</span><span class="font-semibold" id="b-confirm-payment-status">-</span></div>
                                <div class="flex justify-between"><span class="text-gray-600">Transaction Ref:</span><span class="font-semibold" id="b-confirm-txn-ref">-</span></div>
                                <div class="flex justify-between pt-3 border-t-2 border-gray-300"><span class="text-gray-800 font-bold">Total Paid:</span><span class="font-bold text-orange-600" id="b-confirm-total">-</span></div>
                            </div>
                        </div>

                        <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-4 mb-6">
                            <p class="text-sm text-blue-800"><strong>📧 Confirmation email sent!</strong><br>Check your email for booking details and payment receipt.</p>
                        </div>

                        <button type="button" onclick="closeBookingModal()" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-4 rounded-xl font-bold text-lg hover:from-orange-600 hover:to-orange-700 transition-all transform hover:scale-105 shadow-lg">Done</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toast Notification Container -->
<div id="toast-container" aria-live="polite" aria-atomic="true"></div>

<script>
    const BOOKINGS_STORE_URL = @json(route('bookings.store'));

    let selectedRoomName = '';
    let currentBookingStep = 1;
    let roomData = {};
    let bookingData = {};

    // Accessible Toast Notification
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `alert-toast bg-white rounded-lg shadow-2xl p-4 border-l-4 ${type === 'success' ? 'border-green-500' : 'border-red-500'}`;
        toast.setAttribute('role', 'status');
        toast.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                    ${type === 'success'
                        ? '<svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'
                        : '<svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>'
                    }
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-800">${type === 'success' ? 'Success!' : 'Error!'}</p>
                    <p class="text-sm text-gray-600">${message}</p>
                </div>
            </div>
        `;

        const container = document.getElementById('toast-container');
        if (container) {
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(400px)';
                setTimeout(() => toast.remove(), 250);
            }, 4000);
        } else {
            alert(`${type.toUpperCase()}: ${message}`);
        }
    }

    // Room Category Filter
    function filterRooms(category) {
        const rooms = document.querySelectorAll('.room-card');
        const tabs = document.querySelectorAll('.category-tab');
        tabs.forEach(tab => {
            tab.classList.remove('active');
            if (tab.textContent.trim() === category || (category === 'all' && tab.textContent.includes('All'))) {
                tab.classList.add('active');
            }
        });
        rooms.forEach(room => {
            if (category === 'all' || room.dataset.category === category) {
                room.style.display = 'block';
            } else {
                room.style.display = 'none';
            }
        });
    }

    // Login Modal Functions
    function openLoginModal(roomName = '') {
        selectedRoomName = roomName;
        const loginModal = document.getElementById('loginModal');
        if (loginModal) {
            loginModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }
    function closeLoginModal() {
        const loginModal = document.getElementById('loginModal');
        if (loginModal) {
            loginModal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }

    // Open booking modal
    function openBookingModal(roomId, roomName, priceRWF, priceUSD, availability) {
        roomData = { roomId, roomName, priceRWF: Number(priceRWF) || 0, priceUSD: Number(priceUSD) || 0, availability: Number(availability) || 0 };

        document.getElementById('booking-room-id').value = roomId;
        document.getElementById('b-room-name').textContent = roomName;
        document.getElementById('b-room-price-rwf').textContent = new Intl.NumberFormat().format(roomData.priceRWF) + ' RWF';
        document.getElementById('b-room-price-usd').textContent = roomData.priceUSD + ' USD';
        document.getElementById('b-room-availability').textContent = roomData.availability + ' rooms';

        const bookingModal = document.getElementById('bookingModal');
        bookingModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Reset form
        const form = document.getElementById('booking-form');
        form.reset();
        document.getElementById('booking-room-id').value = roomId;
        // Reset payment method and MTN field state
        document.querySelectorAll('input[name="payment_method"]').forEach(r => { r.checked = false; });
        const mtnField = document.getElementById('b-mtn-number');
        if (mtnField) { mtnField.disabled = true; mtnField.required = false; mtnField.value = ''; }

        // Reset steps
        currentBookingStep = 1;
        for (let i = 1; i <= 5; i++) {
            const stepEl = document.getElementById(`b-step-${i}`);
            const indicatorEl = document.getElementById(`b-step-indicator-${i}`);
            if (stepEl) stepEl.classList.add('hidden');
            if (indicatorEl) {
                indicatorEl.classList.remove('active', 'completed');
                indicatorEl.classList.add('pending');
                indicatorEl.textContent = i;
            }
            if (i < 5) {
                const progressEl = document.getElementById(`b-progress-${i}`);
                if (progressEl) progressEl.style.width = '0%';
            }
        }
        document.getElementById('b-step-1').classList.remove('hidden');
        document.getElementById('b-step-indicator-1').classList.remove('pending');
        document.getElementById('b-step-indicator-1').classList.add('active');

        // Min check-in date = now (local)
        const now = new Date();
        const tzoffset = now.getTimezoneOffset() * 60000;
        const localISOTime = new Date(Date.now() - tzoffset).toISOString().slice(0, 16);
        document.getElementById('b-checkin').min = localISOTime;

        // Focus trap start
        setTimeout(() => {
            const body = document.getElementById('bookingModalBody');
            if (body) body.focus();
        }, 0);
    }

    // Close booking modal
    function closeBookingModal() {
        const bookingModal = document.getElementById('bookingModal');
        bookingModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Navigate steps
    function goToBookingStep(targetStep) {
        if (!validateBookingStep(currentBookingStep)) return;

        document.getElementById(`b-step-${currentBookingStep}`).classList.add('hidden');
        document.getElementById(`b-step-indicator-${currentBookingStep}`).classList.remove('active');
        document.getElementById(`b-step-indicator-${currentBookingStep}`).classList.add('completed');
        document.getElementById(`b-step-indicator-${currentBookingStep}`).innerHTML = '✓';
        const progressEl = document.getElementById(`b-progress-${currentBookingStep}`);
        if (progressEl) progressEl.style.width = '100%';

        currentBookingStep = targetStep;
        document.getElementById(`b-step-${targetStep}`).classList.remove('hidden');
        document.getElementById(`b-step-indicator-${targetStep}`).classList.remove('pending');
        document.getElementById(`b-step-indicator-${targetStep}`).classList.add('active');

        populateBookingStepData(targetStep);
    }

    // Validate current step inputs
    function validateBookingStep(step) {
        if (step === 1) {
            const checkin = document.getElementById('b-checkin').value;
            const checkout = document.getElementById('b-checkout').value;
            const adults = document.getElementById('b-adults').value;

            if (!checkin || !checkout || !adults) {
                showToast('Please fill in all required fields', 'error');
                return false;
            }

            const checkinDate = new Date(checkin);
            const checkoutDate = new Date(checkout);
            if (checkoutDate <= checkinDate) {
                showToast('Checkout date must be after check-in date', 'error');
                return false;
            }

            document.getElementById('b-checkout').min = checkin;
            return true;
        }

        if (step === 3) {
            const firstname = document.getElementById('b-firstname').value.trim();
            const lastname = document.getElementById('b-lastname').value.trim();
            const email = document.getElementById('b-email').value.trim();
            const phone = document.getElementById('b-phone').value.trim();
            const terms = document.getElementById('b-terms').checked;

            if (!firstname || !lastname || !email || !phone || !terms) {
                showToast('Please fill in all required fields and accept terms', 'error');
                return false;
            }
            return true;
        }
        return true;
    }

    // Populate next step data
    function populateBookingStepData(step) {
        if (step === 2) {
            const checkin = new Date(document.getElementById('b-checkin').value);
            const checkout = new Date(document.getElementById('b-checkout').value);
            let nights = Math.round((checkout - checkin) / (1000 * 60 * 60 * 24));
            if (nights < 1) nights = 1; // Clamp to minimum 1 night
            const adults = parseInt(document.getElementById('b-adults').value || '0', 10);
            const children = parseInt(document.getElementById('b-children').value || '0', 10);

            bookingData.checkin = checkin;
            bookingData.checkout = checkout;
            bookingData.nights = nights;
            bookingData.adults = adults;
            bookingData.children = children;

            document.getElementById('b-display-checkin').textContent = checkin.toLocaleString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
            document.getElementById('b-display-checkout').textContent = checkout.toLocaleString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
            document.getElementById('b-display-nights').textContent = `${nights} night${nights > 1 ? 's' : ''}`;
            const guestsLabel = `${adults} Adult${adults > 1 ? 's' : ''}${children > 0 ? `, ${children} Child${children > 1 ? 'ren' : ''}` : ''}`;
            document.getElementById('b-display-guests').textContent = guestsLabel;

            const totalRWF = (roomData.priceRWF || 0) * nights;
            const totalUSD = (roomData.priceUSD || 0) * nights;
            bookingData.totalRWF = totalRWF;
            bookingData.totalUSD = totalUSD;

            document.getElementById('b-display-total').textContent = new Intl.NumberFormat().format(totalRWF) + ' RWF / ' + totalUSD + ' USD';
            document.getElementById('total-price-rwf').value = totalRWF;
            document.getElementById('total-price-usd').value = totalUSD;
            document.getElementById('nights-count').value = nights;
        }
        if (step === 4) {
            bookingData.email = document.getElementById('b-email').value.trim();
            bookingData.firstname = document.getElementById('b-firstname').value.trim();
            bookingData.lastname = document.getElementById('b-lastname').value.trim();
            bookingData.phone = document.getElementById('b-phone').value.trim();
            document.getElementById('b-payment-email').textContent = bookingData.email || '-';
            document.getElementById('b-payment-total').textContent = document.getElementById('b-display-total').textContent;
        }
    }

    // Select payment method (card click or radio)
    function selectPayment(method, el) {
        document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('selected'));
        if (el) el.classList.add('selected');
        const radio = document.getElementById(`payment-${method}`);
        if (radio) radio.checked = true;
        // Toggle MTN input enablement/requirement
        const mtnInput = document.getElementById('b-mtn-number');
        if (mtnInput) {
            const isMtn = method === 'mtn';
            mtnInput.disabled = !isMtn;
            mtnInput.required = isMtn;
            if (!isMtn) mtnInput.value = '';
        }
        // Reflect provider in transaction panel (if shown)
        const providerEl = document.getElementById('b-txn-provider');
        if (providerEl) providerEl.textContent = method.toUpperCase();
    }

    // Form Submission (AJAX)
    document.addEventListener('DOMContentLoaded', function() {
        const bookingForm = document.getElementById('booking-form');
        if (!bookingForm) return;

        bookingForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            e.stopPropagation();

            const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
            if (!paymentMethod) {
                showToast('Please select a payment method', 'error');
                return;
            }
            if (paymentMethod.value === 'mtn') {
                const mtnNumber = document.getElementById('b-mtn-number').value.trim();
                if (!mtnNumber) {
                    showToast('Please enter your MTN Mobile number', 'error');
                    return;
                }
            }

            const btn = document.getElementById('confirm-booking-btn');
            const btnText = document.getElementById('btn-text');
            const btnLoading = document.getElementById('btn-loading');
            btn.disabled = true;
            if (btnText) btnText.classList.add('hidden');
            if (btnLoading) { btnLoading.classList.remove('hidden'); btnLoading.classList.add('flex'); }

            const formData = new FormData(this);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            if (!csrfToken) {
                showToast('Security error. Please refresh the page.', 'error');
                btn.disabled = false; if (btnText) btnText.classList.remove('hidden'); if (btnLoading) { btnLoading.classList.add('hidden'); btnLoading.classList.remove('flex'); }
                return;
            }

            const bookingPayload = {
                booking_type: 'hotel',
                facility_id: formData.get('facility_id') || {{ (int) $facility->id }},
                room_id: formData.get('room_id'),
                check_in_date: formData.get('checkin_date'),
                check_out_date: formData.get('checkout_date'),
                adults: parseInt(formData.get('adults')), children: parseInt(formData.get('children') || 0),
                guest_name: `${formData.get('guest_firstname')} ${formData.get('guest_lastname')}`.trim(),
                guest_firstname: formData.get('guest_firstname'), guest_lastname: formData.get('guest_lastname'),
                guest_email: formData.get('guest_email'), guest_phone: formData.get('guest_phone'),
                payment_method: formData.get('payment_method'), payment_phone: formData.get('mtn_number') || formData.get('guest_phone'),
                total_price: parseFloat(formData.get('total_price_rwf')), total_price_rwf: parseFloat(formData.get('total_price_rwf')), total_price_usd: parseFloat(formData.get('total_price_usd')),
                nights: parseInt(formData.get('nights'))
            };

            // Show transaction panel as soon as we start
            const txnPanel = document.getElementById('b-transaction-panel');
            const txnBadge = document.getElementById('b-txn-status-badge');
            const txnSpinner = document.getElementById('b-txn-spinner');
            const txnProvider = document.getElementById('b-txn-provider');
            const txnRefEl = document.getElementById('b-txn-ref');
            if (txnPanel) txnPanel.classList.remove('hidden');
            if (txnBadge) { txnBadge.textContent = 'Processing'; txnBadge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700'; }
            if (txnSpinner) txnSpinner.classList.remove('hidden');
            if (txnProvider) txnProvider.textContent = (paymentMethod.value || '-').toUpperCase();
            if (txnRefEl) txnRefEl.textContent = '-';

            try {
                const response = await fetch(BOOKINGS_STORE_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(bookingPayload)
                });

                let result; const contentType = response.headers.get('content-type') || '';
                if (contentType.includes('application/json')) {
                    result = await response.json();
                } else {
                    const text = await response.text();
                    console.error('Unexpected non-JSON response:', text.slice(0, 1000));
                    showToast('Server error: Expected JSON but received HTML.', 'error');
                    btn.disabled = false; if (btnText) btnText.classList.remove('hidden'); if (btnLoading) { btnLoading.classList.add('hidden'); btnLoading.classList.remove('flex'); }
                    return;
                }

                if (response.ok && result.success) {
                    // Update transaction panel with success and reference
                    const ref = result.transaction_reference || result.payment_reference || result.booking_reference || ('BK-' + Date.now());
                    if (txnBadge) { txnBadge.textContent = 'Paid'; txnBadge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700'; }
                    if (txnSpinner) txnSpinner.classList.add('hidden');
                    if (txnRefEl) txnRefEl.textContent = ref;

                    const bookingRef = result.booking_reference || ('BK-' + Date.now());
                    document.getElementById('b-booking-ref').textContent = bookingRef;
                    document.getElementById('b-confirm-name').textContent = `${bookingData.firstname} ${bookingData.lastname}`.trim();
                    document.getElementById('b-confirm-room').textContent = roomData.roomName;
                    document.getElementById('b-confirm-checkin').textContent = bookingData.checkin.toLocaleString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
                    document.getElementById('b-confirm-checkout').textContent = bookingData.checkout.toLocaleString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
                    document.getElementById('b-confirm-guests').textContent = document.getElementById('b-display-guests').textContent;
                    document.getElementById('b-confirm-total').textContent = document.getElementById('b-display-total').textContent;
                    const confirmPayMethod = document.getElementById('b-confirm-payment-method');
                    const confirmPayStatus = document.getElementById('b-confirm-payment-status');
                    const confirmTxnRef = document.getElementById('b-confirm-txn-ref');
                    if (confirmPayMethod) confirmPayMethod.textContent = (paymentMethod.value || '-').toUpperCase();
                    if (confirmPayStatus) confirmPayStatus.textContent = 'Paid';
                    if (confirmTxnRef) confirmTxnRef.textContent = ref;
                    showToast('Booking confirmed successfully! Reference: ' + bookingRef, 'success');
                    goToBookingStep(5);
                } else {
                    let errorMessage = result.message || 'Booking failed. Please try again.';
                    if (result.errors) {
                        const errors = Object.values(result.errors).flat();
                        errorMessage = errors.join(', ');
                    }
                    showToast(errorMessage, 'error');
                    if (txnBadge) { txnBadge.textContent = 'Failed'; txnBadge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700'; }
                    if (txnSpinner) txnSpinner.classList.add('hidden');
                    btn.disabled = false; if (btnText) btnText.classList.remove('hidden'); if (btnLoading) { btnLoading.classList.add('hidden'); btnLoading.classList.remove('flex'); }
                }
            } catch (error) {
                console.error('Network error:', error);
                showToast('Network error. Please check your connection and try again.', 'error');
                if (txnBadge) { txnBadge.textContent = 'Network Error'; txnBadge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700'; }
                if (txnSpinner) txnSpinner.classList.add('hidden');
                btn.disabled = false; if (btnText) btnText.classList.remove('hidden'); if (btnLoading) { btnLoading.classList.add('hidden'); btnLoading.classList.remove('flex'); }
            }
        });
    });

    // Close modals when clicking outside
    const bookingModalEl = document.getElementById('bookingModal');
    if (bookingModalEl) {
        bookingModalEl.addEventListener('click', function(e) { if (e.target === this) closeBookingModal(); });
    }
    const loginModalEl = document.getElementById('loginModal');
    if (loginModalEl) {
        loginModalEl.addEventListener('click', function(e) { if (e.target === this) closeLoginModal(); });
    }

    // Close modals with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { closeBookingModal(); closeLoginModal(); }
        if (e.key === 'Tab') { // simple focus trap for booking modal
            const modal = document.getElementById('bookingModal');
            if (!modal || modal.classList.contains('hidden')) return;
            const focusable = modal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            if (!focusable.length) return;
            const first = focusable[0]; const last = focusable[focusable.length - 1];
            if (e.shiftKey && document.activeElement === first) { last.focus(); e.preventDefault(); }
            else if (!e.shiftKey && document.activeElement === last) { first.focus(); e.preventDefault(); }
        }
    });

    // Update checkout min date when checkin changes
    const checkinInput = document.getElementById('b-checkin');
    if (checkinInput) {
        checkinInput.addEventListener('change', function() {
            const checkoutInput = document.getElementById('b-checkout');
            if (checkoutInput) checkoutInput.min = this.value;
        });
    }
</script>

@push('scripts')
<script src="{{ asset('js/prevent-double-submit.js') }}" defer></script>
@endpush
@endsection
