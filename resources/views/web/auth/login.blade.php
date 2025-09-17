@extends('web.layouts.app')

@section('content')
<div class="skc-login-container">
    <div class="skc-login-card">
        <div class="skc-login-header">
            <div class="skc-login-icon">
                <i class="fas fa-birthday-cake"></i>
            </div>
            <h2 class="skc-login-title">Sign in to your account</h2>
            <p class="skc-login-subtitle">
                Or
                <a href="{{ route('register') }}" class="skc-login-link">create a new account</a>
            </p>
        </div>


        <!-- Google OAuth Button -->
        <div class="skc-google-auth">
            <a href="{{ route('auth.google') }}" class="skc-google-btn">
                <svg class="skc-google-icon" viewBox="0 0 24 24">
                    <path fill="#4285f4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34a853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#fbbc05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#ea4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                <span>Continue with Google</span>
            </a>
            
            <!-- Or divider -->
            <div class="skc-divider">
                <div class="skc-divider-line"></div>
                <div class="skc-divider-text">Or continue with email</div>
            </div>
        </div>

        <form class="skc-login-form" action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="skc-form-group">
                <label for="email" class="sr-only">Email address</label>
                <input id="email" name="email" type="email" autocomplete="email" required 
                       class="skc-form-input @error('email') error @enderror"
                       placeholder="Email address"
                       value="{{ old('email') }}">
            </div>
            <div class="skc-form-group">
                <label for="password" class="sr-only">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required 
                       class="skc-form-input @error('password') error @enderror"
                       placeholder="Password">
            </div>

            @if ($errors->any())
                <div class="skc-error-message">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="ml-3">
                            <h3>There were errors with your submission</h3>
                            <div class="mt-2">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="skc-form-options">
                <div class="skc-checkbox-group">
                    <input id="remember" name="remember" type="checkbox" class="skc-checkbox">
                    <label for="remember" class="skc-checkbox-label">
                        Remember me
                    </label>
                </div>

                <div>
                    <a href="#" class="skc-forgot-link">
                        Forgot your password?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit" class="skc-submit-btn">
                    <i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i>
                    Sign in
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
