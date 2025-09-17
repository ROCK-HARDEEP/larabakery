@extends('web.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-serif font-bold text-gray-800 mb-6">Checkout - Address</h1>

        <!-- GPS Location Section -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border border-blue-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-blue-800 mb-2">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        Use Current Location
                    </h3>
                    <p class="text-blue-600 text-sm">Automatically fill your address using GPS location</p>
                </div>
                <button type="button" id="getLocationBtn" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors flex items-center">
                    <i class="fas fa-location-arrow mr-2"></i>
                    Get My Location
                </button>
            </div>
            
            <!-- Location Status -->
            <div id="locationStatus" class="mt-4 hidden">
                <div class="flex items-center space-x-2">
                    <div id="locationSpinner" class="hidden">
                        <i class="fas fa-spinner fa-spin text-blue-500"></i>
                    </div>
                    <div id="locationSuccess" class="hidden">
                        <i class="fas fa-check-circle text-green-500"></i>
                    </div>
                    <div id="locationError" class="hidden">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <span id="locationMessage" class="text-sm"></span>
                </div>
                
                <!-- Manual Entry Option -->
                <div id="manualEntryOption" class="mt-3 hidden">
                    <button type="button" id="enableManualEntry" class="text-blue-600 hover:text-blue-800 text-sm underline">
                        <i class="fas fa-edit mr-1"></i>
                        Enter address manually
                    </button>
                </div>
            </div>
        </div>

        <form id="address-form" method="POST" action="{{ route('checkout.address.next') }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required class="w-full px-3 py-2 border rounded-lg"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}" required class="w-full px-3 py-2 border rounded-lg"/>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Door/Building No.</label>
                    <input type="text" name="building_no" value="{{ old('building_no') }}" 
                           placeholder="e.g., 123, A-101, Ground Floor" 
                           class="w-full px-3 py-2 border rounded-lg"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Landmark (Optional)</label>
                    <input type="text" name="landmark" value="{{ old('landmark') }}" 
                           placeholder="e.g., Near Metro Station, Behind Mall" 
                           class="w-full px-3 py-2 border rounded-lg"/>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                <textarea name="line1" rows="3" required class="w-full px-3 py-2 border rounded-lg" 
                          placeholder="Street address, area, locality">{{ old('line1') }}</textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pincode</label>
                    <input type="text" id="pincode" name="pincode" value="{{ old('pincode') }}" required 
                           class="w-full px-3 py-2 border rounded-lg @error('pincode') border-red-500 @enderror"
                           maxlength="6" placeholder="Enter 6-digit pincode"/>
                    <div id="pincodeStatus" class="mt-1 text-sm"></div>
                    @error('pincode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                    <input type="text" id="city" name="city" value="{{ old('city') }}" required 
                           class="w-full px-3 py-2 border rounded-lg @error('city') border-red-500 @enderror" 
                           placeholder="Will be auto-filled from pincode"/>
                    @error('city')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">State</label>
                    <input type="text" id="state" name="state" value="{{ old('state') }}" required 
                           class="w-full px-3 py-2 border rounded-lg @error('state') border-red-500 @enderror" 
                           placeholder="Will be auto-filled from pincode"/>
                    @error('state')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 2 (optional)</label>
                <input type="text" name="line2" value="{{ old('line2') }}" class="w-full px-3 py-2 border rounded-lg"/>
            </div>

            <!-- Location Details (displayed after pincode verification) -->
            <div id="locationDetails" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-medium text-blue-800 mb-2">Location Details</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-blue-700">District:</span>
                        <span id="district" class="text-blue-600"></span>
                    </div>
                    <div>
                        <span class="font-medium text-blue-700">Area:</span>
                        <span id="area" class="text-blue-600"></span>
                    </div>
                </div>
                <div id="postOffices" class="mt-3">
                    <span class="font-medium text-blue-700">Available Post Offices:</span>
                    <div id="postOfficeList" class="mt-1 space-y-1 text-blue-600 text-sm"></div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4">
                <a href="{{ route('checkout.summary') }}" class="text-bakery-600">Back</a>
                <button class="bg-bakery-500 text-white px-6 py-2 rounded-lg">Continue</button>
            </div>
        </form>
        <script>
        document.getElementById('address-form').addEventListener('keydown', function(e){ if(e.key==='Enter'){ e.preventDefault(); this.submit(); }});

        // GPS Location Functionality
        const getLocationBtn = document.getElementById('getLocationBtn');
        const locationStatus = document.getElementById('locationStatus');
        const locationSpinner = document.getElementById('locationSpinner');
        const locationSuccess = document.getElementById('locationSuccess');
        const locationError = document.getElementById('locationError');
        const locationMessage = document.getElementById('locationMessage');

        getLocationBtn.addEventListener('click', function() {
            if (!navigator.geolocation) {
                showLocationError('Geolocation is not supported by this browser.');
                return;
            }

            // Show loading state
            showLocationLoading('Getting your location...');
            getLocationBtn.disabled = true;

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;
                    
                    showLocationLoading('Finding your address...');
                    
                    // Reverse geocoding to get address from coordinates
                    reverseGeocode(latitude, longitude);
                },
                function(error) {
                    let errorMessage = 'Unable to get your location.';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = 'Location access denied. Please allow location access and try again.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = 'Location information unavailable.';
                            break;
                        case error.TIMEOUT:
                            errorMessage = 'Location request timed out.';
                            break;
                    }
                    showLocationError(errorMessage);
                    getLocationBtn.disabled = false;
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 60000
                }
            );
        });

        function showLocationLoading(message) {
            locationStatus.classList.remove('hidden');
            locationSpinner.classList.remove('hidden');
            locationSuccess.classList.add('hidden');
            locationError.classList.add('hidden');
            locationMessage.textContent = message;
        }

        function showLocationSuccess(message) {
            locationStatus.classList.remove('hidden');
            locationSpinner.classList.add('hidden');
            locationSuccess.classList.remove('hidden');
            locationError.classList.add('hidden');
            locationMessage.textContent = message;
            getLocationBtn.disabled = false;
        }

        function showLocationError(message) {
            locationStatus.classList.remove('hidden');
            locationSpinner.classList.add('hidden');
            locationSuccess.classList.add('hidden');
            locationError.classList.remove('hidden');
            locationMessage.textContent = message;
            getLocationBtn.disabled = false;
            
            // Show manual entry option
            document.getElementById('manualEntryOption').classList.remove('hidden');
        }

        async function reverseGeocode(latitude, longitude) {
            try {
                // Using OpenStreetMap Nominatim API for reverse geocoding
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&addressdetails=1&accept-language=en`);
                const data = await response.json();

                if (data && data.address) {
                    const address = data.address;
                    
                    // Fill in the form fields
                    document.querySelector('input[name="name"]').value = document.querySelector('input[name="name"]').value || '{{ auth()->user()->name ?? "" }}';
                    document.querySelector('input[name="phone"]').value = document.querySelector('input[name="phone"]').value || '{{ auth()->user()->phone ?? "" }}';
                    
                    // Fill building number if available
                    if (address.house_number) {
                        document.querySelector('input[name="building_no"]').value = address.house_number;
                    }
                    
                    // Fill landmark if available (using suburb or neighbourhood)
                    if (address.suburb || address.neighbourhood) {
                        document.querySelector('input[name="landmark"]').value = address.suburb || address.neighbourhood;
                    }
                    
                    // Build comprehensive address line
                    let addressParts = [];
                    
                    // Add road/street
                    if (address.road) {
                        addressParts.push(address.road);
                    }
                    
                    // Add suburb/area
                    if (address.suburb && address.suburb !== address.city) {
                        addressParts.push(address.suburb);
                    }
                    
                    // Add city
                    if (address.city) {
                        addressParts.push(address.city);
                    }
                    
                    // Add state
                    if (address.state) {
                        addressParts.push(address.state);
                    }
                    
                    // Join all parts
                    const addressLine = addressParts.join(', ');
                    document.querySelector('textarea[name="line1"]').value = addressLine;
                    
                    // Auto-fill city and state
                    if (address.city) {
                        document.getElementById('city').value = address.city;
                    } else if (address.town) {
                        document.getElementById('city').value = address.town;
                    } else if (address.district) {
                        document.getElementById('city').value = address.district;
                    }
                    
                    if (address.state) {
                        document.getElementById('state').value = address.state;
                    }
                    
                    // Try to get pincode from the address
                    if (address.postcode) {
                        document.getElementById('pincode').value = address.postcode;
                        // Trigger pincode verification
                        setTimeout(() => {
                            verifyPincode(address.postcode);
                        }, 500);
                    }
                    
                    showLocationSuccess('Location found! Please review and complete the remaining details.');
                    
                    // Scroll to form
                    document.getElementById('address-form').scrollIntoView({ behavior: 'smooth' });
                } else {
                    // Try alternative geocoding service if OpenStreetMap fails
                    try {
                        const altResponse = await fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${latitude}&longitude=${longitude}&localityLanguage=en`);
                        const altData = await altResponse.json();
                        
                        if (altData && altData.locality) {
                            // Fill basic information from alternative service
                            document.querySelector('textarea[name="line1"]').value = `${altData.locality}, ${altData.countryName}`;
                            document.getElementById('city').value = altData.locality || '';
                            document.getElementById('state').value = altData.principalSubdivision || '';
                            
                            showLocationSuccess('Basic location found! Please complete the address details.');
                            document.getElementById('address-form').scrollIntoView({ behavior: 'smooth' });
                        } else {
                            showLocationError('Could not find address for this location. Please enter manually.');
                        }
                    } catch (altError) {
                        showLocationError('Could not find address for this location. Please enter manually.');
                    }
                }
            } catch (error) {
                console.error('Reverse geocoding error:', error);
                showLocationError('Failed to get address from location. Please enter manually.');
            }
        }

        // Manual entry functionality
        document.getElementById('enableManualEntry').addEventListener('click', function() {
            // Clear any auto-filled data
            document.querySelector('input[name="building_no"]').value = '';
            document.querySelector('input[name="landmark"]').value = '';
            document.querySelector('textarea[name="line1"]').value = '';
            document.getElementById('pincode').value = '';
            document.getElementById('city').value = '';
            document.getElementById('state').value = '';
            
            // Focus on building number field
            document.querySelector('input[name="building_no"]').focus();
            
            // Hide location status
            document.getElementById('locationStatus').classList.add('hidden');
        });

        // Real-time pincode verification
        const pincodeInput = document.getElementById('pincode');
        const cityInput = document.getElementById('city');
        const stateInput = document.getElementById('state');
        const pincodeStatus = document.getElementById('pincodeStatus');
        const locationDetails = document.getElementById('locationDetails');
        const submitBtn = document.querySelector('button[type="submit"]');
        
        let pincodeValid = false;

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

        // Restrict pincode input to numbers only
        pincodeInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Clear previous state
            pincodeValid = false;
            cityInput.value = '';
            stateInput.value = '';
            locationDetails.classList.add('hidden');
            
            if (this.value.length === 6) {
                verifyPincode(this.value);
            } else if (this.value.length > 0) {
                pincodeStatus.innerHTML = '<span class="text-yellow-600">Enter 6-digit pincode</span>';
                this.classList.remove('border-green-500', 'border-red-500');
                this.classList.add('border-yellow-500');
            } else {
                pincodeStatus.innerHTML = '';
                this.classList.remove('border-green-500', 'border-red-500', 'border-yellow-500');
                // Enable manual entry if pincode verification fails
                cityInput.readOnly = false;
                stateInput.readOnly = false;
            }
        });

        // Verify pincode function
        async function verifyPincode(pincode) {
            pincodeStatus.innerHTML = '<span class="text-blue-600"><i class="fas fa-spinner fa-spin mr-1"></i>Verifying pincode...</span>';
            pincodeInput.classList.remove('border-green-500', 'border-red-500', 'border-yellow-500');
            pincodeInput.classList.add('border-blue-500');

            try {
                // Try multiple pincode verification services
                let data = null;
                let success = false;

                // Service 1: India Post API
                try {
                    const response = await fetch(`https://api.postalpincode.in/pincode/${pincode}`);
                    const externalData = await response.json();
                    
                    if (externalData && externalData[0] && externalData[0].Status === 'Success') {
                        const postOffice = externalData[0].PostOffice[0];
                        data = {
                            valid: true,
                            city: postOffice.District,
                            state: postOffice.State,
                            district: postOffice.District,
                            area: postOffice.Block || postOffice.Division,
                            post_offices: externalData[0].PostOffice.map(office => ({
                                name: office.Name,
                                branch_type: office.BranchType
                            }))
                        };
                        success = true;
                    }
                } catch (error) {
                    console.log('India Post API failed, trying next service...');
                }

                // Service 2: Internal API (if available)
                if (!success) {
                    try {
                        const response = await fetch('/api/verify/pincode', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ pincode: pincode })
                        });

                        if (response.ok) {
                            const internalData = await response.json();
                            if (internalData.valid) {
                                data = internalData;
                                success = true;
                            }
                        }
                    } catch (error) {
                        console.log('Internal API failed, trying next service...');
                    }
                }

                // Service 3: Basic validation (fallback)
                if (!success) {
                    // Basic validation - check if it's a 6-digit number
                    if (/^\d{6}$/.test(pincode)) {
                        data = {
                            valid: true,
                            city: '',
                            state: '',
                            message: 'Pincode format is valid'
                        };
                        success = true;
                    } else {
                        data = { valid: false, message: 'Invalid pincode format' };
                    }
                }

                if (data && data.valid) {
                    // Pincode is valid
                    pincodeValid = true;
                    pincodeStatus.innerHTML = '<span class="text-green-600"><i class="fas fa-check mr-1"></i>Pincode verified successfully</span>';
                    pincodeInput.classList.remove('border-blue-500', 'border-red-500');
                    pincodeInput.classList.add('border-green-500');

                    // Fill in the location details if available
                    if (data.city) {
                        cityInput.value = data.city;
                    }
                    if (data.state) {
                        stateInput.value = data.state;
                    }
                    
                    // Display location details
                    document.getElementById('district').textContent = data.district || 'N/A';
                    document.getElementById('area').textContent = data.area || 'N/A';
                    
                    // Display post offices if available
                    if (data.post_offices && data.post_offices.length > 0) {
                        const postOfficeList = document.getElementById('postOfficeList');
                        postOfficeList.innerHTML = '';
                        
                        data.post_offices.slice(0, 5).forEach(office => {
                            const div = document.createElement('div');
                            div.innerHTML = `<span class="font-medium">${office.name}</span> (${office.branch_type})`;
                            postOfficeList.appendChild(div);
                        });
                        
                        if (data.post_offices.length > 5) {
                            const moreDiv = document.createElement('div');
                            moreDiv.innerHTML = `<span class="text-gray-500">... and ${data.post_offices.length - 5} more</span>`;
                            postOfficeList.appendChild(moreDiv);
                        }
                    }
                    
                    locationDetails.classList.remove('hidden');

                    // Check delivery serviceability
                    checkServiceability(pincode);
                } else {
                    // Pincode is invalid
                    pincodeValid = false;
                    pincodeStatus.innerHTML = '<span class="text-red-600"><i class="fas fa-times mr-1"></i>' + (data?.message || 'Invalid pincode') + '</span>';
                    pincodeInput.classList.remove('border-blue-500', 'border-green-500');
                    pincodeInput.classList.add('border-red-500');
                }
            } catch (error) {
                console.error('Pincode verification error:', error);
                pincodeValid = false;
                pincodeStatus.innerHTML = '<span class="text-blue-600"><i class="fas fa-info-circle mr-1"></i>Pincode verification service is temporarily unavailable. You can continue with manual entry.</span>';
                pincodeInput.classList.remove('border-blue-500', 'border-green-500', 'border-red-500');
                pincodeInput.classList.add('border-blue-500');
                
                // Allow form submission even if pincode verification fails
                pincodeValid = true;
                
                // Enable manual entry for city and state
                cityInput.readOnly = false;
                stateInput.readOnly = false;
            }
        }

        // Check delivery serviceability
        async function checkServiceability(pincode) {
            try {
                const response = await fetch('{{ route("checkout.pincode") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ pincode: pincode })
                });

                const data = await response.json();

                if (data.ok) {
                    pincodeStatus.innerHTML += '<br><span class="text-green-600"><i class="fas fa-truck mr-1"></i>Delivery available to this location</span>';
                } else {
                    pincodeStatus.innerHTML += '<br><span class="text-red-600"><i class="fas fa-times-circle mr-1"></i>Delivery not available to this location</span>';
                    pincodeValid = false;
                    pincodeInput.classList.remove('border-green-500');
                    pincodeInput.classList.add('border-red-500');
                }
            } catch (error) {
                console.error('Error checking serviceability:', error);
            }
        }

        // Form validation before submit
        document.getElementById('address-form').addEventListener('submit', function(e) {
            const pincode = pincodeInput.value.trim();
            
            // Basic pincode validation (6 digits)
            if (pincode.length !== 6 || !/^\d{6}$/.test(pincode)) {
                e.preventDefault();
                alert('Please enter a valid 6-digit pincode.');
                pincodeInput.focus();
                return false;
            }
            
            // If pincode verification failed but pincode format is correct, allow submission
            if (!pincodeValid && pincode.length === 6) {
                // Don't show confirmation dialog - just allow submission
                // The user already knows verification is unavailable from the status message
                return true;
            }
        });

        // Initialize validation if pincode is pre-filled
        window.addEventListener('load', function() {
            const initialPincode = pincodeInput.value.trim();
            if (initialPincode && initialPincode.length === 6) {
                verifyPincode(initialPincode);
            }
        });
        </script>
    </div>
</div>
@endsection


