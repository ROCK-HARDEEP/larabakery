<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class VerificationController extends Controller
{
    /**
     * Check if username is available
     */
    public function checkUsername(Request $request)
    {
        $username = $request->input('username');
        
        if (empty($username)) {
            return response()->json([
                'available' => false,
                'message' => 'Username is required.',
            ]);
        }
        
        // Check length (minimum 3 characters)
        if (strlen($username) < 3) {
            return response()->json([
                'available' => false,
                'message' => 'Username must be at least 3 characters long.',
            ]);
        }
        
        // Check format (alphanumeric and underscore only)
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            return response()->json([
                'available' => false,
                'message' => 'Username can only contain letters, numbers, and underscores.',
            ]);
        }
        
        // Check if username exists
        $exists = User::where('username', $username)->exists();
        
        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Username is already taken.' : 'Username is available.',
        ]);
    }
    
    /**
     * Check if email is available
     */
    public function checkEmail(Request $request)
    {
        $email = $request->input('email');
        
        if (empty($email)) {
            return response()->json([
                'available' => false,
                'message' => 'Email is required.',
            ]);
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'available' => false,
                'message' => 'Please enter a valid email address.',
            ]);
        }
        
        // Check if email exists
        $exists = User::where('email', $email)->exists();
        
        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Email is already registered.' : 'Email is available.',
        ]);
    }
    
    /**
     * Check if phone number is available
     */
    public function checkPhone(Request $request)
    {
        $phone = $request->input('phone');
        
        if (empty($phone)) {
            return response()->json([
                'available' => false,
                'message' => 'Phone number is required.',
            ]);
        }
        
        // Validate phone format (10 digits)
        if (!preg_match('/^[0-9]{10}$/', $phone)) {
            return response()->json([
                'available' => false,
                'message' => 'Please enter a valid 10-digit phone number.',
            ]);
        }
        
        // Check if phone exists
        $exists = User::where('phone', $phone)->exists();
        
        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Phone number is already registered.' : 'Phone number is available.',
        ]);
    }
    
    /**
     * Verify pincode and get location details
     */
    public function verifyPincode(Request $request)
    {
        $pincode = $request->input('pincode');
        
        if (empty($pincode)) {
            return response()->json([
                'valid' => false,
                'message' => 'Pincode is required.',
            ]);
        }
        
        // Validate pincode format (6 digits)
        if (!preg_match('/^[0-9]{6}$/', $pincode)) {
            return response()->json([
                'valid' => false,
                'message' => 'Please enter a valid 6-digit pincode.',
            ]);
        }
        
        // Check cache first
        $cacheKey = "pincode_data_{$pincode}";
        $locationData = Cache::get($cacheKey);
        
        if (!$locationData) {
            // Verify pincode using India Post API
            try {
                $response = Http::timeout(10)->get("https://api.postalpincode.in/pincode/{$pincode}");
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data[0]['Status']) && $data[0]['Status'] === 'Success') {
                        $postOffices = $data[0]['PostOffice'];
                        $firstOffice = $postOffices[0];
                        
                        $locationData = [
                            'valid' => true,
                            'pincode' => $pincode,
                            'city' => $firstOffice['District'],
                            'state' => $firstOffice['State'],
                            'district' => $firstOffice['District'],
                            'area' => $firstOffice['Name'],
                            'post_offices' => array_map(function($office) {
                                return [
                                    'name' => $office['Name'],
                                    'branch_type' => $office['BranchType'],
                                    'delivery_status' => $office['DeliveryStatus'],
                                ];
                            }, $postOffices)
                        ];
                        
                        // Cache for 24 hours
                        Cache::put($cacheKey, $locationData, 86400);
                    } else {
                        $locationData = [
                            'valid' => false,
                            'message' => 'Invalid pincode. Please check and try again.',
                        ];
                    }
                } else {
                    $locationData = [
                        'valid' => false,
                        'message' => 'Unable to verify pincode. Please try again later.',
                    ];
                }
            } catch (\Exception $e) {
                $locationData = [
                    'valid' => false,
                    'message' => 'Pincode verification service is temporarily unavailable.',
                ];
            }
        }
        
        return response()->json($locationData);
    }
    
    /**
     * Validate password strength in real-time
     */
    public function checkPasswordStrength(Request $request)
    {
        $password = $request->input('password');
        
        if (empty($password)) {
            return response()->json([
                'strength' => 'weak',
                'score' => 0,
                'requirements' => [
                    'length' => false,
                    'uppercase' => false,
                    'number' => false,
                    'special' => false,
                ],
                'message' => 'Password is required.',
            ]);
        }
        
        $requirements = [
            'length' => strlen($password) >= 8,
            'uppercase' => preg_match('/[A-Z]/', $password),
            'number' => preg_match('/[0-9]/', $password),
            'special' => preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password),
        ];
        
        $score = array_sum($requirements);
        
        $strength = 'weak';
        $message = 'Password is weak';
        
        if ($score === 4) {
            $strength = 'strong';
            $message = 'Password is strong';
        } elseif ($score >= 2) {
            $strength = 'medium';
            $message = 'Password is medium strength';
        }
        
        return response()->json([
            'strength' => $strength,
            'score' => $score,
            'requirements' => $requirements,
            'message' => $message,
        ]);
    }
}