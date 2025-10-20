# 🚀 Quick Start Guide - Hotel Booking System

## ✅ Setup Instructions

### Step 1: Include CSS Files
Add to your `layouts/app.blade.php`:

```html
<head>
    <!-- Existing styles -->
    
    <!-- NEW: Enhanced booking styles -->
    <link rel="stylesheet" href="{{ asset('css/booking-enhancements.css') }}">
</head>
```

### Step 2: Include JavaScript Files
Add before closing `</body>` tag:

```html
<!-- Prevent double submit (IMPORTANT!) -->
<script src="{{ asset('js/prevent-double-submit.js') }}"></script>

<!-- Optional: Testing utilities (only in development) -->
@if(config('app.debug'))
    <script src="{{ asset('js/booking-test.js') }}"></script>
@endif
```

### Step 3: Configure Routes
Ensure your `web.php` has the booking route:

```php
Route::post('/bookings/store', [BookingController::class, 'store'])
    ->name('bookings.store')
    ->middleware(['auth']);
```

### Step 4: Create BookingController
If not exists, create `app/Http/Controllers/BookingController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'room_id' => 'required|exists:rooms,id',
                'facility_id' => 'required|exists:facilities,id',
                'check_in_date' => 'required|date|after:now',
                'check_out_date' => 'required|date|after:check_in_date',
                'adults' => 'required|integer|min:1|max:10',
                'children' => 'nullable|integer|min:0|max:10',
                'guest_firstname' => 'required|string|max:255',
                'guest_lastname' => 'required|string|max:255',
                'guest_email' => 'required|email',
                'guest_phone' => 'required|string',
                'payment_method' => 'required|in:mtn,visa',
                'total_price_rwf' => 'required|numeric',
                'total_price_usd' => 'required|numeric',
                'nights' => 'required|integer|min:1'
            ]);

            $booking = Booking::create([
                'user_id' => auth()->id(),
                'room_id' => $validated['room_id'],
                'facility_id' => $validated['facility_id'],
                'check_in_date' => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                'adults' => $validated['adults'],
                'children' => $validated['children'] ?? 0,
                'guest_name' => $validated['guest_firstname'] . ' ' . $validated['guest_lastname'],
                'guest_email' => $validated['guest_email'],
                'guest_phone' => $validated['guest_phone'],
                'payment_method' => $validated['payment_method'],
                'total_price' => $validated['total_price_rwf'],
                'total_price_usd' => $validated['total_price_usd'],
                'nights' => $validated['nights'],
                'status' => 'pending',
                'booking_reference' => 'BK-' . strtoupper(uniqid())
            ]);

            // TODO: Send confirmation email
            // Mail::to($booking->guest_email)->send(new BookingConfirmation($booking));

            return response()->json([
                'success' => true,
                'message' => 'Booking confirmed successfully!',
                'booking_reference' => $booking->booking_reference,
                'booking' => $booking
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Booking failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Booking failed. Please try again.'
            ], 500);
        }
    }
}
```

### Step 5: Create Booking Model
If not exists, create `app/Models/Booking.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'facility_id',
        'check_in_date',
        'check_out_date',
        'adults',
        'children',
        'guest_name',
        'guest_email',
        'guest_phone',
        'payment_method',
        'total_price',
        'total_price_usd',
        'nights',
        'status',
        'booking_reference'
    ];

    protected $casts = [
        'check_in_date' => 'datetime',
        'check_out_date' => 'datetime',
        'total_price' => 'decimal:2',
        'total_price_usd' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }
}
```

### Step 6: Create Migration
Run this command:

```bash
php artisan make:migration create_bookings_table
```

Edit the migration file:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('facility_id')->constrained()->onDelete('cascade');
            $table->dateTime('check_in_date');
            $table->dateTime('check_out_date');
            $table->integer('adults');
            $table->integer('children')->default(0);
            $table->string('guest_name');
            $table->string('guest_email');
            $table->string('guest_phone');
            $table->enum('payment_method', ['mtn', 'visa']);
            $table->decimal('total_price', 10, 2);
            $table->decimal('total_price_usd', 10, 2);
            $table->integer('nights');
            $table->string('booking_reference')->unique();
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};
```

Then run:

```bash
php artisan migrate
```

---

## 🧪 Testing the System

### 1. Browser Console Tests
Open your browser console and run:

```javascript
// Run all tests
testBookingSystem()

// Test booking flow
testBookingFlow()

// Auto-fill form with test data
fillTestData()
```

### 2. Manual Testing Checklist

- [ ] Open hotel details page
- [ ] Click "Book now" button
- [ ] Fill in check-in/checkout dates
- [ ] Select number of adults/children
- [ ] Proceed to next step
- [ ] Verify price calculation
- [ ] Fill guest information
- [ ] Select payment method
- [ ] Submit booking
- [ ] Verify confirmation screen
- [ ] Check email for confirmation (if configured)

### 3. Test Edge Cases

```javascript
// Test 1: Past date validation
document.getElementById('b-checkin').value = '2020-01-01';
// Expected: Error message

// Test 2: Invalid email
document.getElementById('b-email').value = 'invalid-email';
// Expected: Validation error

// Test 3: Checkout before checkin
document.getElementById('b-checkin').value = '2024-12-10';
document.getElementById('b-checkout').value = '2024-12-05';
// Expected: Error message

// Test 4: Double submission
// Click submit button twice quickly
// Expected: Only one request sent
```

---

## 🎨 Customization

### Change Primary Color
Edit `booking-enhancements.css`:

```css
/* Change orange to your brand color */
/* Find and replace #F97316 with your color */
/* Example: #3B82F6 for blue */
```

### Change Animation Speed
```css
/* In booking-enhancements.css */
.modal-content {
    animation-duration: 0.4s; /* Change to 0.6s for slower */
}
```

### Disable Animations
```css
/* Add this at the end of your CSS */
* {
    animation: none !important;
    transition: none !important;
}
```

---

## 🔧 Troubleshooting

### Issue: Form not submitting
**Solution:**
```javascript
// Check console for errors
// Verify CSRF token exists
console.log(document.querySelector('meta[name="csrf-token"]')?.content);

// Check if form element exists
console.log(document.getElementById('booking-form'));

// Verify route is accessible
fetch('/bookings/store', { method: 'POST' })
```

### Issue: Validation errors not showing
**Solution:**
```php
// In BookingController, ensure you're returning JSON
return response()->json([
    'success' => false,
    'message' => 'Validation failed',
    'errors' => $validator->errors()
], 422);
```

### Issue: Double submission still occurring
**Solution:**
```html
<!-- Ensure data attribute is present -->
<form id="booking-form" data-prevent-double-submit>
    <!-- form content -->
</form>

<!-- Ensure JS file is loaded -->
<script src="{{ asset('js/prevent-double-submit.js') }}"></script>
```

### Issue: Modal not opening
**Solution:**
```javascript
// Check if functions are defined
console.log(typeof openBookingModal); // should be "function"

// Manually open modal for debugging
document.getElementById('bookingModal').classList.remove('hidden');
```

---

## 📊 Performance Optimization

### 1. Minify CSS/JS
```bash
npm install -g csso-cli uglify-js

# Minify CSS
csso public/css/booking-enhancements.css -o public/css/booking-enhancements.min.css

# Minify JS
uglifyjs public/js/prevent-double-submit.js -o public/js/prevent-double-submit.min.js
```

### 2. Use in Production
```html
<!-- In production, use minified versions -->
@if(config('app.env') === 'production')
    <link rel="stylesheet" href="{{ asset('css/booking-enhancements.min.css') }}">
    <script src="{{ asset('js/prevent-double-submit.min.js') }}"></script>
@else
    <link rel="stylesheet" href="{{ asset('css/booking-enhancements.css') }}">
    <script src="{{ asset('js/prevent-double-submit.js') }}"></script>
@endif
```

### 3. Enable Caching
```php
// In config/cache.php or .env
CACHE_DRIVER=redis // or memcached
```

---

## 🔐 Security Best Practices

1. **Always validate on server-side** (never trust client validation)
2. **Use CSRF protection** (Laravel does this by default)
3. **Sanitize user inputs** (Laravel does this automatically)
4. **Rate limit API endpoints**

```php
// In routes/web.php
Route::post('/bookings/store', [BookingController::class, 'store'])
    ->middleware(['auth', 'throttle:10,1']); // 10 requests per minute
```

---

## 📚 Additional Resources

- [Laravel Validation Docs](https://laravel.com/docs/validation)
- [JavaScript Fetch API](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API)
- [CSS Animations](https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_Animations)

---

## 🆘 Support

If you encounter issues:

1. Check browser console for errors
2. Check Laravel logs: `storage/logs/laravel.log`
3. Enable debug mode: Set `APP_DEBUG=true` in `.env`
4. Review this documentation
5. Run test suite: `testBookingSystem()`

---

**Last Updated**: 2025-10-20  
**Version**: 2.0  
**Compatibility**: Laravel 8+, PHP 7.4+
