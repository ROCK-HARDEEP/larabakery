@extends('web.layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-bakery-50 to-bakery-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 bg-gradient-to-br from-bakery-500 to-bakery-600 rounded-full flex items-center justify-center">
                <i class="fas fa-birthday-cake text-white text-xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-serif font-bold text-gray-900">
                Create your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Or
                <a href="{{ route('login') }}" class="font-medium text-bakery-600 hover:text-bakery-500">
                    sign in to your existing account
                </a>
            </p>
        </div>

        <!-- Google OAuth Button - Enhanced for Professional Look -->
        <div class="mt-6">
            <a href="{{ route('auth.google') }}" 
               class="w-full flex justify-center items-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition duration-200 ease-in-out transform hover:scale-105">
                <svg class="h-5 w-5 mr-3" viewBox="0 0 24 24">
                    <path fill="#4285f4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34a853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#fbbc05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#ea4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                <span class="font-semibold">Continue with Google</span>
            </a>
            
            <!-- Or divider -->
            <div class="mt-6 relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-gradient-to-br from-bakery-50 to-bakery-100 text-gray-500">Or create account with email</span>
                </div>
            </div>
        </div>
        
        <form class="mt-8 space-y-6" action="{{ route('register.post') }}" method="POST" id="registrationForm">
            @csrf
            <div class="space-y-4">
                <!-- Full Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input id="name" name="name" type="text" autocomplete="name" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-bakery-500 focus:border-bakery-500 sm:text-sm @error('name') border-red-500 @enderror"
                           placeholder="Enter your full name"
                           value="{{ old('name') }}">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input id="username" name="username" type="text" autocomplete="username" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-bakery-500 focus:border-bakery-500 sm:text-sm @error('username') border-red-500 @enderror"
                           placeholder="Choose a unique username"
                           value="{{ old('username') }}">
                    <div id="usernameStatus" class="mt-1 text-sm"></div>
                    @error('username')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input id="email" name="email" type="email" autocomplete="email" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-bakery-500 focus:border-bakery-500 sm:text-sm @error('email') border-red-500 @enderror"
                           placeholder="Enter your email address"
                           value="{{ old('email') }}">
                    <div id="emailStatus" class="mt-1 text-sm"></div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                            @if(str_contains($message, 'already exists'))
                                <br><a href="{{ route('login') }}" class="text-bakery-600 hover:text-bakery-500 underline">Go to login page</a>
                            @endif
                        </p>
                    @enderror
                </div>

                <!-- Phone Number -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Mobile Number</label>
                    <input id="phone" name="phone" type="tel" autocomplete="tel" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-bakery-500 focus:border-bakery-500 sm:text-sm @error('phone') border-red-500 @enderror"
                           placeholder="Enter 10-digit mobile number"
                           value="{{ old('phone') }}"
                           maxlength="10">
                    <div id="phoneStatus" class="mt-1 text-sm"></div>
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-bakery-500 focus:border-bakery-500 sm:text-sm @error('password') border-red-500 @enderror"
                           placeholder="Create a strong password">
                    <div id="passwordStatus" class="mt-1 text-sm"></div>
                    <div id="passwordRequirements" class="mt-2 text-xs text-gray-500">
                        <p class="font-medium">Password must contain:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li id="req-length" class="text-red-500">At least 8 characters</li>
                            <li id="req-uppercase" class="text-red-500">At least 1 uppercase letter</li>
                            <li id="req-number" class="text-red-500">At least 1 number</li>
                            <li id="req-special" class="text-red-500">At least 1 special character</li>
                        </ul>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-bakery-500 focus:border-bakery-500 sm:text-sm @error('password_confirmation') border-red-500 @enderror"
                           placeholder="Confirm your password">
                    <div id="passwordConfirmStatus" class="mt-1 text-sm"></div>
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <button type="submit" id="submitBtn"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-bakery-600 hover:bg-bakery-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bakery-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-user-plus text-bakery-500 group-hover:text-bakery-400"></i>
                    </span>
                    Create Account
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const submitBtn = document.getElementById('submitBtn');

    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Real-time username validation
    const checkUsername = debounce(async function() {
        const username = usernameInput.value.trim();
        const statusDiv = document.getElementById('usernameStatus');
        
        if (username.length < 3) {
            statusDiv.innerHTML = '<span class="text-red-600">Username must be at least 3 characters</span>';
            return;
        }

        try {
            const response = await fetch('/api/verify/username', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ username: username })
            });
            const data = await response.json();
            
            if (data.available) {
                statusDiv.innerHTML = '<span class="text-green-600">✓ Username is available</span>';
            } else {
                statusDiv.innerHTML = '<span class="text-red-600">✗ ' + data.message + '</span>';
            }
        } catch (error) {
            statusDiv.innerHTML = '<span class="text-yellow-600">Unable to check availability</span>';
        }
    }, 500);

    // Real-time email validation
    const checkEmail = debounce(async function() {
        const email = emailInput.value.trim();
        const statusDiv = document.getElementById('emailStatus');
        
        if (!email.includes('@')) {
            statusDiv.innerHTML = '<span class="text-red-600">Please enter a valid email</span>';
            return;
        }

        try {
            const response = await fetch('/api/verify/email', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email: email })
            });
            const data = await response.json();
            
            if (data.available) {
                statusDiv.innerHTML = '<span class="text-green-600">✓ Email is available</span>';
            } else {
                statusDiv.innerHTML = '<span class="text-red-600">✗ ' + data.message + '<br><a href="{{ route("login") }}" class="text-bakery-600 hover:text-bakery-500 underline">Go to login page</a></span>';
            }
        } catch (error) {
            statusDiv.innerHTML = '<span class="text-yellow-600">Unable to check availability</span>';
        }
    }, 500);

    // Real-time phone validation
    const checkPhone = debounce(async function() {
        const phone = phoneInput.value.trim();
        const statusDiv = document.getElementById('phoneStatus');
        
        if (phone.length !== 10 || !/^[0-9]+$/.test(phone)) {
            statusDiv.innerHTML = '<span class="text-red-600">Please enter a valid 10-digit mobile number</span>';
            return;
        }

        try {
            const response = await fetch('/api/verify/phone', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ phone: phone })
            });
            const data = await response.json();
            
            if (data.available) {
                statusDiv.innerHTML = '<span class="text-green-600">✓ Phone number is available</span>';
            } else {
                statusDiv.innerHTML = '<span class="text-red-600">✗ ' + data.message + '</span>';
            }
        } catch (error) {
            statusDiv.innerHTML = '<span class="text-yellow-600">Unable to check availability</span>';
        }
    }, 500);

    // Real-time password strength validation
    const checkPasswordStrength = debounce(async function() {
        const password = passwordInput.value;
        const statusDiv = document.getElementById('passwordStatus');
        
        if (!password) {
            statusDiv.innerHTML = '';
            return;
        }

        try {
            const response = await fetch('/api/verify/password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ password: password })
            });
            const data = await response.json();
            
            // Update requirement indicators
            document.getElementById('req-length').className = data.requirements.length ? 'text-green-600' : 'text-red-500';
            document.getElementById('req-uppercase').className = data.requirements.uppercase ? 'text-green-600' : 'text-red-500';
            document.getElementById('req-number').className = data.requirements.number ? 'text-green-600' : 'text-red-500';
            document.getElementById('req-special').className = data.requirements.special ? 'text-green-600' : 'text-red-500';
            
            // Update status message
            let statusClass = 'text-red-600';
            if (data.strength === 'strong') statusClass = 'text-green-600';
            else if (data.strength === 'medium') statusClass = 'text-yellow-600';
            
            statusDiv.innerHTML = `<span class="${statusClass}">${data.message}</span>`;
            
        } catch (error) {
            statusDiv.innerHTML = '<span class="text-yellow-600">Unable to check password strength</span>';
        }
    }, 300);

    // Password confirmation validation
    const checkPasswordConfirmation = function() {
        const password = passwordInput.value;
        const confirmPassword = passwordConfirmInput.value;
        const statusDiv = document.getElementById('passwordConfirmStatus');
        
        if (!confirmPassword) {
            statusDiv.innerHTML = '';
            return;
        }
        
        if (password === confirmPassword) {
            statusDiv.innerHTML = '<span class="text-green-600">✓ Passwords match</span>';
        } else {
            statusDiv.innerHTML = '<span class="text-red-600">✗ Passwords do not match</span>';
        }
    };

    // Attach event listeners
    usernameInput.addEventListener('input', checkUsername);
    emailInput.addEventListener('input', checkEmail);
    phoneInput.addEventListener('input', checkPhone);
    passwordInput.addEventListener('input', checkPasswordStrength);
    passwordConfirmInput.addEventListener('input', checkPasswordConfirmation);

    // Restrict phone input to numbers only
    phoneInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
});
</script>
@endsection