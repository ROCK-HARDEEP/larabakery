<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PincodeService
{
    /**
     * Service areas configuration
     * You can modify this list based on your delivery areas
     */
    private array $serviceableStates = [
        'Tamil Nadu', 'Kerala', 'Karnataka', 'Andhra Pradesh', 'Telangana',
        'Maharashtra', 'Gujarat', 'Delhi', 'Haryana', 'Punjab', 'Uttar Pradesh',
        // Add more states as per your delivery coverage
    ];

    private array $serviceableCities = [
        'Chennai', 'Bangalore', 'Hyderabad', 'Mumbai', 'Delhi', 'Pune', 'Coimbatore',
        'Kochi', 'Thiruvananthapuram', 'Mysore', 'Mangalore', 'Visakhapatnam',
        // Add more cities as per your delivery coverage
    ];

    /**
     * Check if a pincode is serviceable for delivery
     */
    public function isServiceable(string $pincode): bool
    {
        // Basic validation
        if (!$this->isValidPincode($pincode)) {
            return false;
        }

        // Check cache first
        $cacheKey = "serviceable_pincode_{$pincode}";
        $isServiceable = Cache::get($cacheKey);
        
        if ($isServiceable !== null) {
            return $isServiceable;
        }

        // Get location data
        $locationData = $this->getPincodeData($pincode);
        
        if (!$locationData || !$locationData['valid']) {
            Cache::put($cacheKey, false, 3600); // Cache for 1 hour
            return false;
        }

        // Check if we deliver to this state/city
        $serviceable = $this->checkServiceability($locationData);
        
        // Cache the result for 24 hours
        Cache::put($cacheKey, $serviceable, 86400);
        
        return $serviceable;
    }

    /**
     * Get detailed pincode data including city, state, etc.
     */
    public function getPincodeData(string $pincode): ?array
    {
        if (!$this->isValidPincode($pincode)) {
            return null;
        }

        $cacheKey = "pincode_data_{$pincode}";
        $data = Cache::get($cacheKey);
        
        if ($data !== null) {
            return $data;
        }

        try {
            $response = Http::timeout(10)->get("https://api.postalpincode.in/pincode/{$pincode}");
            
            if ($response->successful()) {
                $apiData = $response->json();
                
                if (isset($apiData[0]['Status']) && $apiData[0]['Status'] === 'Success') {
                    $postOffices = $apiData[0]['PostOffice'];
                    $firstOffice = $postOffices[0];
                    
                    $data = [
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
                    
                    Cache::put($cacheKey, $data, 86400); // Cache for 24 hours
                    return $data;
                }
            }
        } catch (\Exception $e) {
            Log::error("Pincode API error for {$pincode}: " . $e->getMessage());
        }

        // If API fails, return basic validation result
        $data = [
            'valid' => true, // We assume it's valid if it passes format check
            'pincode' => $pincode,
            'city' => '',
            'state' => '',
            'district' => '',
            'area' => '',
            'post_offices' => []
        ];
        
        Cache::put($cacheKey, $data, 3600); // Cache for 1 hour
        return $data;
    }

    /**
     * Validate pincode format
     */
    private function isValidPincode(string $pincode): bool
    {
        // Indian pincode format: 6 digits
        return preg_match('/^[1-9][0-9]{5}$/', $pincode) === 1;
    }

    /**
     * Check if we provide service to this location
     */
    private function checkServiceability(array $locationData): bool
    {
        $state = $locationData['state'] ?? '';
        $city = $locationData['city'] ?? '';
        $district = $locationData['district'] ?? '';

        // Check if state is serviceable
        if (in_array($state, $this->serviceableStates, true)) {
            return true;
        }

        // Check if city/district is serviceable
        if (in_array($city, $this->serviceableCities, true) || 
            in_array($district, $this->serviceableCities, true)) {
            return true;
        }

        // For demo/testing purposes, allow specific pincode patterns
        $pincode = $locationData['pincode'];
        
        // Allow major city pincodes (you can customize this logic)
        $majorCityPrefixes = [
            '11', '12', '13', '14', '15', '16', '17', '18', '19', // Delhi
            '40', '41', '42', '43', '44', // Mumbai
            '56', '57', '58', '59', // Bangalore
            '60', '61', '62', '63', '64', // Chennai
            '50', '51', '52', '53', '54', '55', // Hyderabad
            '70', '71', '72', '73', '74', '75', // Gujarat
            '30', '31', '32', '33', '34', '35', '36', '37', '38', '39', // Rajasthan
            '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', // Haryana/Punjab
        ];

        foreach ($majorCityPrefixes as $prefix) {
            if (strpos($pincode, $prefix) === 0) {
                return true;
            }
        }

        // For development: Allow all Kerala pincodes (67xxxx, 68xxxx, 69xxxx)
        if (preg_match('/^(67|68|69)\d{4}$/', $pincode)) {
            return true;
        }

        // For development: Allow all Tamil Nadu pincodes (6xxxxx)
        if (preg_match('/^6\d{5}$/', $pincode)) {
            return true;
        }

        return false;
    }

    /**
     * Get all serviceable pincodes (for admin use)
     */
    public function getServiceableAreas(): array
    {
        return [
            'states' => $this->serviceableStates,
            'cities' => $this->serviceableCities,
            'patterns' => [
                'Delhi NCR' => '11xxxx - 19xxxx',
                'Mumbai' => '40xxxx - 44xxxx',
                'Bangalore' => '56xxxx - 59xxxx',
                'Chennai' => '60xxxx - 64xxxx',
                'Hyderabad' => '50xxxx - 55xxxx',
                'Kerala' => '67xxxx - 69xxxx',
                'Tamil Nadu' => '6xxxxx',
            ]
        ];
    }

    /**
     * Add a new serviceable area
     */
    public function addServiceableArea(string $type, string $value): bool
    {
        if ($type === 'state' && !in_array($value, $this->serviceableStates)) {
            $this->serviceableStates[] = $value;
            return true;
        }

        if ($type === 'city' && !in_array($value, $this->serviceableCities)) {
            $this->serviceableCities[] = $value;
            return true;
        }

        return false;
    }

    /**
     * Get delivery charge for a pincode (can be customized)
     */
    public function getDeliveryCharge(string $pincode): float
    {
        if (!$this->isServiceable($pincode)) {
            return 0; // No delivery available
        }

        // Default delivery charge
        $baseCharge = 50.0;

        // You can implement distance-based or zone-based pricing here
        $locationData = $this->getPincodeData($pincode);
        
        if ($locationData && isset($locationData['state'])) {
            // Different charges for different states
            $stateCharges = [
                'Tamil Nadu' => 30.0,
                'Kerala' => 40.0,
                'Karnataka' => 50.0,
                'Delhi' => 60.0,
                'Mumbai' => 70.0,
            ];

            return $stateCharges[$locationData['state']] ?? $baseCharge;
        }

        return $baseCharge;
    }
}