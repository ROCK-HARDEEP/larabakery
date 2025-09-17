<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Rules\StrongPassword;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('web.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'));
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    public function showRegistrationForm()
    {
        return view('web.auth.register');
    }

    public function register(Request $request)
    {
        // Check if user already exists with this email
        $existingUser = User::where('email', $request->email)->first();
        
        if ($existingUser) {
            // Check if they're trying to register with same email but user already exists
            if ($existingUser->google_id) {
                return back()->withErrors([
                    'email' => 'An account with this email already exists. Please sign in with Google or use the "Forgot Password" option.'
                ])->withInput();
            } else {
                return back()->withErrors([
                    'email' => 'An account with this email already exists. Please use the login page instead.'
                ])->withInput();
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[a-zA-Z0-9_]+$/',
                'unique:users',
            ],
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => [
                'required',
                'string',
                'regex:/^[0-9]{10}$/',
                'unique:users',
            ],
            'password' => ['required', 'string', 'confirmed', new StrongPassword()],
        ], [
            'username.regex' => 'Username can only contain letters, numbers, and underscores.',
            'phone.regex' => 'Please enter a valid 10-digit phone number.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'last_login_at' => now(),
            'login_ip' => $request->ip(),
        ]);

        Auth::login($user);
        
        // Redirect to phone verification if phone verification is required
        if (!$user->phone_verified_at) {
            return redirect()->route('auth.phone.verify.form')->with('message', 'Please verify your phone number to complete registration.');
        }
        
        return redirect()->route('home');
    }
}
