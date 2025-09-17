<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Redirect to Google OAuth with account selection (like major websites)
     */
    public function redirectToGoogle()
    {
        /** @var GoogleProvider $googleProvider */
        $googleProvider = Socialite::driver('google');
        
        return $googleProvider
            ->with([
                'prompt' => 'select_account',  // Force account selection
                'access_type' => 'offline',    // For refresh tokens
                'include_granted_scopes' => 'true'
            ])
            ->redirect();
    }
    
    /**
     * Redirect to Google OAuth for admin login with account selection
     */
    public function redirectToGoogleAdmin()
    {
        /** @var GoogleProvider $googleProvider */
        $googleProvider = Socialite::driver('google');
        
        return $googleProvider
            ->with([
                'prompt' => 'select_account',  // Force account selection
                'access_type' => 'offline',
                'include_granted_scopes' => 'true',
                'state' => 'admin'  // Mark as admin login
            ])
            ->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            // Check if the user cancelled the OAuth process
            if (request()->has('error')) {
                return redirect('/login')->with('error', 'Google login was cancelled. Please try again.');
            }

            /** @var \Laravel\Socialite\Two\User $googleUser */
            $googleUser = Socialite::driver('google')->user();
            
            // Validate required user data
            if (!$googleUser->email) {
                return redirect('/login')->with('error', 'Unable to get your email from Google. Please try again.');
            }
            
            // Check if user already exists with this Google ID
            $user = User::where('google_id', $googleUser->id)->first();
            
            if ($user) {
                // Update login information
                $user->update([
                    'last_login_at' => now(),
                    'login_ip' => request()->ip(),
                    'provider_token' => $googleUser->token,
                ]);
                
                Auth::login($user, true);
                return redirect()->intended('/')->with('success', 'Welcome back! You have been logged in successfully.');
            }
            
            // Check if user exists with same email
            $existingUser = User::where('email', $googleUser->email)->first();
            
            if ($existingUser) {
                // Link Google account to existing user automatically
                $existingUser->update([
                    'google_id' => $googleUser->id,
                    'provider' => 'google',
                    'provider_token' => $googleUser->token,
                    'social_data' => array_merge($existingUser->social_data ?? [], [
                        'google' => [
                            'id' => $googleUser->id,
                            'name' => $googleUser->name,
                            'email' => $googleUser->email,
                            'avatar' => $googleUser->avatar,
                        ]
                    ]),
                    'last_login_at' => now(),
                    'login_ip' => request()->ip(),
                ]);
                
                Auth::login($existingUser, true);
                
                // If username is missing, generate one from Google data
                if (!$existingUser->username) {
                    $username = $this->generateUniqueUsername($googleUser->name, $googleUser->email);
                    $existingUser->update(['username' => $username]);
                }
                
                return redirect()->intended('/')->with('success', 'Google account linked successfully! You can now login with either Google or your email/password.');
            }
            
            // Create new user
            $newUser = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'username' => $this->generateUniqueUsername($googleUser->name, $googleUser->email),
                'google_id' => $googleUser->id,
                'provider' => 'google',
                'provider_token' => $googleUser->token,
                'email_verified_at' => now(), // Google emails are pre-verified
                'password' => Hash::make(Str::random(24)), // Random password for security
                'social_data' => [
                    'google' => [
                        'id' => $googleUser->id,
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'avatar' => $googleUser->avatar,
                    ]
                ],
                'last_login_at' => now(),
                'login_ip' => request()->ip(),
            ]);
            
            Auth::login($newUser, true);
            
            // Redirect to phone verification if phone not provided
            if (!$newUser->phone) {
                return redirect()->route('auth.phone.verify.form')->with('message', 'Please verify your phone number to complete registration.');
            }
            
            return redirect()->intended('/')->with('success', 'Welcome to our bakery! Your account has been created successfully.');
            
        } catch (\Exception $e) {
            Log::error('Google OAuth Error: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Unable to login with Google. Please try again or contact support if the problem persists.');
        }
    }

    /**
     * Generate unique username from name and email
     */
    private function generateUniqueUsername($name, $email): string
    {
        // Clean the name and create base username
        $baseUsername = strtolower(str_replace(' ', '_', $name));
        $baseUsername = preg_replace('/[^a-z0-9_]/', '', $baseUsername);
        
        // If name is not usable, use email prefix
        if (empty($baseUsername) || strlen($baseUsername) < 3) {
            $baseUsername = strtolower(explode('@', $email)[0]);
            $baseUsername = preg_replace('/[^a-z0-9_]/', '', $baseUsername);
        }
        
        $username = $baseUsername;
        $counter = 1;
        
        // Ensure username is unique
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . '_' . $counter;
            $counter++;
        }
        
        return $username;
    }

    /**
     * Show phone verification form for Google users
     */
    public function showPhoneVerificationForm()
    {
        $user = Auth::user();
        
        if (!$user || $user->phone_verified_at) {
            return redirect()->intended('/');
        }
        
        return view('auth.verify-phone');
    }

    /**
     * Send phone verification code
     */
    public function sendPhoneVerificationCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|regex:/^[0-9]{10}$/|unique:users,phone,' . Auth::id(),
        ]);
        
        /** @var User $user */
        $user = Auth::user();
        $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $user->update([
            'phone' => $request->phone,
            'phone_verification_code' => $verificationCode,
        ]);
        
        // Send SMS using SMS service
        $smsService = new SmsService();
        $result = $smsService->sendVerificationCode($request->phone, $verificationCode);
        
        if (!$result['success']) {
            Log::error('SMS sending failed', $result);
            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification code. Please try again.',
                'error' => $result['message']
            ], 500);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Verification code sent to your phone.',
            'debug_code' => config('app.debug') ? $verificationCode : null, // Only show in debug mode
        ]);
    }

    /**
     * Verify phone number
     */
    public function verifyPhone(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string|size:6',
        ]);
        
        /** @var User $user */
        $user = Auth::user();
        
        if ($user->phone_verification_code !== $request->verification_code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code.',
            ], 422);
        }
        
        $user->update([
            'phone_verified_at' => now(),
            'phone_verification_code' => null,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Phone number verified successfully!',
            'redirect' => route('home'),
        ]);
    }
}