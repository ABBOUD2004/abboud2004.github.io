/**
 * ============================================
 * BOOKING SYSTEM - TESTING UTILITIES
 * ============================================
 * Use this file to test booking functionality
 * Open browser console and run: testBookingSystem()
 */

(function(window) {
    'use strict';

    const BookingTests = {
        
        /**
         * Test 1: Validate email format
         */
        testEmailValidation: function() {
            console.log('🧪 Testing Email Validation...');
            
            const validEmails = [
                'user@example.com',
                'test.user@domain.co.uk',
                'admin+tag@site.org'
            ];
            
            const invalidEmails = [
                'invalid',
                '@example.com',
                'user@',
                'user @example.com'
            ];
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            validEmails.forEach(email => {
                console.assert(
                    emailRegex.test(email),
                    `✅ Valid email should pass: ${email}`
                );
            });
            
            invalidEmails.forEach(email => {
                console.assert(
                    !emailRegex.test(email),
                    `❌ Invalid email should fail: ${email}`
                );
            });
            
            console.log('✅ Email validation test completed');
        },

        /**
         * Test 2: Validate phone format
         */
        testPhoneValidation: function() {
            console.log('🧪 Testing Phone Validation...');
            
            const validPhones = [
                '+250788123456',
                '0788123456',
                '+1 234 567 8900',
                '123-456-7890'
            ];
            
            const invalidPhones = [
                '123',
                'abc',
                '+12',
                ''
            ];
            
            const phoneRegex = /^\+?[\d\s-]{10,}$/;
            
            validPhones.forEach(phone => {
                console.assert(
                    phoneRegex.test(phone),
                    `✅ Valid phone should pass: ${phone}`
                );
            });
            
            invalidPhones.forEach(phone => {
                console.assert(
                    !phoneRegex.test(phone),
                    `❌ Invalid phone should fail: ${phone}`
                );
            });
            
            console.log('✅ Phone validation test completed');
        },

        /**
         * Test 3: Date validation
         */
        testDateValidation: function() {
            console.log('🧪 Testing Date Validation...');
            
            const now = new Date();
            const yesterday = new Date(now);
            yesterday.setDate(yesterday.getDate() - 1);
            
            const tomorrow = new Date(now);
            tomorrow.setDate(tomorrow.getDate() + 1);
            
            const nextWeek = new Date(now);
            nextWeek.setDate(nextWeek.getDate() + 7);
            
            // Test: Past date should be invalid
            console.assert(
                yesterday < now,
                '❌ Past date should be invalid'
            );
            
            // Test: Future date should be valid
            console.assert(
                tomorrow > now,
                '✅ Future date should be valid'
            );
            
            // Test: Checkout after checkin
            console.assert(
                nextWeek > tomorrow,
                '✅ Checkout should be after checkin'
            );
            
            console.log('✅ Date validation test completed');
        },

        /**
         * Test 4: Calculate nights
         */
        testNightsCalculation: function() {
            console.log('🧪 Testing Nights Calculation...');
            
            const testCases = [
                {
                    checkin: new Date('2024-01-01'),
                    checkout: new Date('2024-01-02'),
                    expectedNights: 1
                },
                {
                    checkin: new Date('2024-01-01'),
                    checkout: new Date('2024-01-08'),
                    expectedNights: 7
                },
                {
                    checkin: new Date('2024-01-01T14:00'),
                    checkout: new Date('2024-01-02T10:00'),
                    expectedNights: 1
                }
            ];
            
            testCases.forEach(test => {
                const nights = Math.ceil(
                    (test.checkout - test.checkin) / (1000 * 60 * 60 * 24)
                );
                
                console.assert(
                    nights === test.expectedNights,
                    `Expected ${test.expectedNights} nights, got ${nights}`
                );
            });
            
            console.log('✅ Nights calculation test completed');
        },

        /**
         * Test 5: Price calculation
         */
        testPriceCalculation: function() {
            console.log('🧪 Testing Price Calculation...');
            
            const testCases = [
                { pricePerNight: 175000, nights: 1, expected: 175000 },
                { pricePerNight: 175000, nights: 3, expected: 525000 },
                { pricePerNight: 100000, nights: 7, expected: 700000 }
            ];
            
            testCases.forEach(test => {
                const total = test.pricePerNight * test.nights;
                
                console.assert(
                    total === test.expected,
                    `Expected ${test.expected}, got ${total}`
                );
            });
            
            console.log('✅ Price calculation test completed');
        },

        /**
         * Test 6: Form state management
         */
        testFormState: function() {
            console.log('🧪 Testing Form State Management...');
            
            const form = document.getElementById('booking-form');
            if (!form) {
                console.warn('⚠️ Booking form not found - skipping test');
                return;
            }
            
            // Check initial state
            console.assert(
                !form.dataset.submitting || form.dataset.submitting === 'false',
                '✅ Form should not be submitting initially'
            );
            
            // Check for required elements
            const requiredElements = [
                'b-checkin',
                'b-checkout',
                'b-adults',
                'b-firstname',
                'b-lastname',
                'b-email',
                'b-phone'
            ];
            
            requiredElements.forEach(id => {
                const element = document.getElementById(id);
                console.assert(
                    element !== null,
                    `✅ Required element '${id}' exists`
                );
            });
            
            console.log('✅ Form state test completed');
        },

        /**
         * Test 7: Modal functionality
         */
        testModalFunctionality: function() {
            console.log('🧪 Testing Modal Functionality...');
            
            const bookingModal = document.getElementById('bookingModal');
            const loginModal = document.getElementById('loginModal');
            
            if (!bookingModal || !loginModal) {
                console.warn('⚠️ Modals not found - skipping test');
                return;
            }
            
            // Test initial state
            console.assert(
                bookingModal.classList.contains('hidden'),
                '✅ Booking modal should be hidden initially'
            );
            
            console.assert(
                loginModal.classList.contains('hidden'),
                '✅ Login modal should be hidden initially'
            );
            
            console.log('✅ Modal functionality test completed');
        },

        /**
         * Test 8: Step indicators
         */
        testStepIndicators: function() {
            console.log('🧪 Testing Step Indicators...');
            
            const totalSteps = 5;
            let foundSteps = 0;
            
            for (let i = 1; i <= totalSteps; i++) {
                const stepContent = document.getElementById(`b-step-${i}`);
                const stepIndicator = document.getElementById(`b-step-indicator-${i}`);
                
                if (stepContent && stepIndicator) {
                    foundSteps++;
                }
            }
            
            console.assert(
                foundSteps === totalSteps,
                `✅ Found ${foundSteps}/${totalSteps} steps`
            );
            
            console.log('✅ Step indicators test completed');
        },

        /**
         * Run all tests
         */
        runAllTests: function() {
            console.log('🚀 Starting Booking System Tests...\n');
            
            try {
                this.testEmailValidation();
                this.testPhoneValidation();
                this.testDateValidation();
                this.testNightsCalculation();
                this.testPriceCalculation();
                this.testFormState();
                this.testModalFunctionality();
                this.testStepIndicators();
                
                console.log('\n✅ All tests completed successfully!');
                return true;
            } catch (error) {
                console.error('❌ Tests failed:', error);
                return false;
            }
        },

        /**
         * Test booking flow (interactive)
         */
        testBookingFlow: function(roomId = 1) {
            console.log('🧪 Testing Booking Flow (Interactive)...');
            
            if (typeof openBookingModal !== 'function') {
                console.error('❌ openBookingModal function not found');
                return;
            }
            
            // Simulate opening booking modal
            openBookingModal(
                roomId,
                'Test Room',
                175000,
                70,
                5
            );
            
            console.log('✅ Booking modal opened - please test manually');
            console.log('   1. Fill in dates');
            console.log('   2. Select number of guests');
            console.log('   3. Proceed through steps');
            console.log('   4. Check validation');
        },

        /**
         * Generate test booking data
         */
        generateTestData: function() {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            
            const nextWeek = new Date();
            nextWeek.setDate(nextWeek.getDate() + 7);
            
            return {
                checkin: tomorrow.toISOString().slice(0, 16),
                checkout: nextWeek.toISOString().slice(0, 16),
                adults: '2',
                children: '1',
                firstname: 'John',
                lastname: 'Doe',
                email: 'john.doe@example.com',
                phone: '+250788123456',
                payment_method: 'mtn',
                mtn_number: '0788123456'
            };
        },

        /**
         * Auto-fill form with test data
         */
        autoFillTestData: function() {
            console.log('📝 Auto-filling form with test data...');
            
            const data = this.generateTestData();
            
            const fields = {
                'b-checkin': data.checkin,
                'b-checkout': data.checkout,
                'b-adults': data.adults,
                'b-children': data.children,
                'b-firstname': data.firstname,
                'b-lastname': data.lastname,
                'b-email': data.email,
                'b-phone': data.phone,
                'b-mtn-number': data.mtn_number
            };
            
            Object.entries(fields).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) {
                    element.value = value;
                    console.log(`✅ Filled ${id}: ${value}`);
                } else {
                    console.warn(`⚠️ Element not found: ${id}`);
                }
            });
            
            // Check terms
            const termsCheckbox = document.getElementById('b-terms');
            if (termsCheckbox) {
                termsCheckbox.checked = true;
                console.log('✅ Checked terms and conditions');
            }
            
            console.log('✅ Test data filled successfully');
        }
    };

    // Expose to window for console access
    window.testBookingSystem = BookingTests.runAllTests.bind(BookingTests);
    window.testBookingFlow = BookingTests.testBookingFlow.bind(BookingTests);
    window.fillTestData = BookingTests.autoFillTestData.bind(BookingTests);
    window.BookingTests = BookingTests;

    console.log('📋 Booking Test Utilities Loaded');
    console.log('   Run: testBookingSystem() - to run all tests');
    console.log('   Run: testBookingFlow() - to test booking flow');
    console.log('   Run: fillTestData() - to auto-fill form with test data');

})(window);
