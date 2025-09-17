@extends('web.layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-bakery-50 to-bakery-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 bg-gradient-to-br from-bakery-500 to-bakery-600 rounded-full flex items-center justify-center">
                <i class="fas fa-mobile-alt text-white text-xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-serif font-bold text-gray-900">
                Verify Your Phone Number
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Please verify your phone number to complete your account setup
            </p>
        </div>

        @if(session('message'))
            <div class="rounded-md bg-blue-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-800">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div id="phoneForm" class="space-y-6">
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Mobile Number</label>
                <div class="mt-1 relative">
                    <input id="phone" name="phone" type="tel" required 
                           class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-bakery-500 focus:border-bakery-500 sm:text-sm"
                           placeholder="Enter your 10-digit mobile number"
                           maxlength="10"
                           value="{{ auth()->user()->phone ?? '' }}">
                </div>
                <div id="phoneStatus" class="mt-1 text-sm"></div>
            </div>

            <div>
                <button type="button" id="sendCodeBtn"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-bakery-600 hover:bg-bakery-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bakery-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-paper-plane text-bakery-500 group-hover:text-bakery-400"></i>
                    </span>
                    Send Verification Code
                </button>
            </div>
        </div>

        <div id="verificationForm" class="space-y-6 hidden">
            <div>
                <label for="verification_code" class="block text-sm font-medium text-gray-700">Verification Code</label>
                <div class="mt-1">
                    <input id="verification_code" name="verification_code" type="text" maxlength="6" 
                           class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-bakery-500 focus:border-bakery-500 sm:text-sm text-center text-lg tracking-widest"
                           placeholder="000000">
                </div>
                <div id="verificationStatus" class="mt-1 text-sm"></div>
                <p class="mt-2 text-sm text-gray-500">
                    Enter the 6-digit code sent to your phone number
                </p>
            </div>

            <div>
                <button type="button" id="verifyCodeBtn"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-check text-green-500 group-hover:text-green-400"></i>
                    </span>
                    Verify Phone Number
                </button>
            </div>

            <div class="text-center">
                <button type="button" id="resendCodeBtn" 
                        class="text-sm text-bakery-600 hover:text-bakery-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                    Resend code (<span id="countdown">60</span>s)
                </button>
            </div>
        </div>

        <div class="text-center">
            <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-gray-700">
                Skip for now (you can verify later in your account settings)
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('phone');
    const sendCodeBtn = document.getElementById('sendCodeBtn');
    const verificationCodeInput = document.getElementById('verification_code');
    const verifyCodeBtn = document.getElementById('verifyCodeBtn');
    const resendCodeBtn = document.getElementById('resendCodeBtn');
    const phoneForm = document.getElementById('phoneForm');
    const verificationForm = document.getElementById('verificationForm');
    
    let countdownTimer;
    
    // Restrict phone input to numbers only
    phoneInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        
        // Check if phone number is valid
        const phone = this.value.trim();
        const statusDiv = document.getElementById('phoneStatus');
        
        if (phone.length === 10) {
            statusDiv.innerHTML = '<span class="text-green-600">✓ Valid phone number</span>';
            sendCodeBtn.disabled = false;
        } else if (phone.length > 0) {
            statusDiv.innerHTML = '<span class="text-red-600">Please enter a 10-digit mobile number</span>';
            sendCodeBtn.disabled = true;
        } else {
            statusDiv.innerHTML = '';
            sendCodeBtn.disabled = true;
        }
    });

    // Send verification code
    sendCodeBtn.addEventListener('click', async function() {
        const phone = phoneInput.value.trim();
        
        if (phone.length !== 10) {
            alert('Please enter a valid 10-digit phone number');
            return;
        }
        
        sendCodeBtn.disabled = true;
        sendCodeBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
        
        try {
            const response = await fetch('{{ route("auth.phone.send.code") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ phone: phone })
            });
            
            const data = await response.json();
            
            if (data.success) {
                phoneForm.classList.add('hidden');
                verificationForm.classList.remove('hidden');
                
                // Show debug code in development
                if (data.debug_code) {
                    document.getElementById('verificationStatus').innerHTML = 
                        '<span class="text-blue-600">Debug: Code is ' + data.debug_code + '</span>';
                }
                
                // Start countdown for resend
                startCountdown();
            } else {
                alert('Error: ' + data.message);
                sendCodeBtn.disabled = false;
                sendCodeBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Send Verification Code';
            }
        } catch (error) {
            alert('Failed to send verification code. Please try again.');
            sendCodeBtn.disabled = false;
            sendCodeBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Send Verification Code';
        }
    });

    // Verify code
    verifyCodeBtn.addEventListener('click', async function() {
        const code = verificationCodeInput.value.trim();
        
        if (code.length !== 6) {
            alert('Please enter the 6-digit verification code');
            return;
        }
        
        verifyCodeBtn.disabled = true;
        verifyCodeBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Verifying...';
        
        try {
            const response = await fetch('{{ route("auth.phone.verify") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ verification_code: code })
            });
            
            const data = await response.json();
            
            if (data.success) {
                document.getElementById('verificationStatus').innerHTML = 
                    '<span class="text-green-600">✓ ' + data.message + '</span>';
                
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            } else {
                document.getElementById('verificationStatus').innerHTML = 
                    '<span class="text-red-600">✗ ' + data.message + '</span>';
                verifyCodeBtn.disabled = false;
                verifyCodeBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Verify Phone Number';
            }
        } catch (error) {
            alert('Failed to verify code. Please try again.');
            verifyCodeBtn.disabled = false;
            verifyCodeBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Verify Phone Number';
        }
    });

    // Auto-focus and format verification code input
    verificationCodeInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        
        if (this.value.length === 6) {
            verifyCodeBtn.disabled = false;
        } else {
            verifyCodeBtn.disabled = true;
        }
    });

    // Resend code
    resendCodeBtn.addEventListener('click', function() {
        sendCodeBtn.click();
    });

    // Countdown timer for resend
    function startCountdown() {
        let seconds = 60;
        resendCodeBtn.disabled = true;
        
        countdownTimer = setInterval(() => {
            seconds--;
            document.getElementById('countdown').textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(countdownTimer);
                resendCodeBtn.disabled = false;
                resendCodeBtn.innerHTML = 'Resend code';
            }
        }, 1000);
    }

    // Initialize form state
    const initialPhone = phoneInput.value.trim();
    if (initialPhone && initialPhone.length === 10) {
        sendCodeBtn.disabled = false;
        document.getElementById('phoneStatus').innerHTML = '<span class="text-green-600">✓ Valid phone number</span>';
    } else {
        sendCodeBtn.disabled = true;
    }
});
</script>
@endsection
