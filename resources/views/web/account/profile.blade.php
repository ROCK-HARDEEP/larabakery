@extends('web.layouts.app')

@section('content')
    <!-- Hero Section -->
    <section class="skc-hero-section" style="height: 300px;">
        <div class="skc-hero-slider">
            <div class="skc-hero-slide active">
                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=1600" alt="My Profile" class="skc-hero-image">
                <div class="skc-hero-content">
                    <h1 class="skc-hero-title">My Profile</h1>
                    <p class="skc-hero-subtitle">Manage your account information and preferences</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Profile Section -->
    <section class="skc-section">
        <div class="skc-container">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                <!-- Profile Information -->
                <div style="background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); padding: 40px;">
                    <h2 style="font-size: 28px; font-weight: 700; color: var(--skc-black); margin-bottom: 30px;">Profile Information</h2>
                    
                    <form method="POST" action="{{ route('account.profile.update') }}" style="space-y: 6;">
                        @csrf
                        
                        <div>
                            <label for="name" style="display: block; font-size: 14px; font-weight: 600; color: var(--skc-black); margin-bottom: 8px;">Full Name *</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                   style="width: 100%; padding: 12px 16px; border: 1px solid var(--skc-border); border-radius: 8px; font-size: 16px; outline: none; transition: border-color 0.2s;">
                            @error('name')
                                <p style="color: #dc2626; font-size: 14px; margin-top: 5px;">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="email" style="display: block; font-size: 14px; font-weight: 600; color: var(--skc-black); margin-bottom: 8px;">Email *</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                                   style="width: 100%; padding: 12px 16px; border: 1px solid var(--skc-border); border-radius: 8px; font-size: 16px; outline: none; transition: border-color 0.2s;">
                            @error('email')
                                <p style="color: #dc2626; font-size: 14px; margin-top: 5px;">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="phone" style="display: block; font-size: 14px; font-weight: 600; color: var(--skc-black); margin-bottom: 8px;">Phone</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                   style="width: 100%; padding: 12px 16px; border: 1px solid var(--skc-border); border-radius: 8px; font-size: 16px; outline: none; transition: border-color 0.2s;">
                            @error('phone')
                                <p style="color: #dc2626; font-size: 14px; margin-top: 5px;">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="username" style="display: block; font-size: 14px; font-weight: 600; color: var(--skc-black); margin-bottom: 8px;">Username</label>
                            <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}"
                                   style="width: 100%; padding: 12px 16px; border: 1px solid var(--skc-border); border-radius: 8px; font-size: 16px; outline: none; transition: border-color 0.2s;">
                            @error('username')
                                <p style="color: #dc2626; font-size: 14px; margin-top: 5px;">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="gstin" style="display: block; font-size: 14px; font-weight: 600; color: var(--skc-black); margin-bottom: 8px;">GSTIN</label>
                            <input type="text" id="gstin" name="gstin" value="{{ old('gstin', $user->gstin) }}"
                                   style="width: 100%; padding: 12px 16px; border: 1px solid var(--skc-border); border-radius: 8px; font-size: 16px; outline: none; transition: border-color 0.2s;">
                            @error('gstin')
                                <p style="color: #dc2626; font-size: 14px; margin-top: 5px;">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <button type="submit" 
                                style="width: 100%; background: var(--skc-orange); color: white; padding: 16px 24px; border: none; border-radius: 8px; font-size: 18px; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 5px 15px rgba(246, 157, 28, 0.3);">
                            Update Profile
                        </button>
                    </form>
                </div>

                <!-- Change Password -->
                <div style="background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); padding: 40px;">
                    <h2 style="font-size: 28px; font-weight: 700; color: var(--skc-black); margin-bottom: 30px;">Change Password</h2>
                    
                    <form method="POST" action="{{ route('account.profile.update') }}" style="space-y: 6;">
                        @csrf
                        
                        <div>
                            <label for="password" style="display: block; font-size: 14px; font-weight: 600; color: var(--skc-black); margin-bottom: 8px;">New Password</label>
                            <input type="password" id="password" name="password"
                                   style="width: 100%; padding: 12px 16px; border: 1px solid var(--skc-border); border-radius: 8px; font-size: 16px; outline: none; transition: border-color 0.2s;">
                            @error('password')
                                <p style="color: #dc2626; font-size: 14px; margin-top: 5px;">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="password_confirmation" style="display: block; font-size: 14px; font-weight: 600; color: var(--skc-black); margin-bottom: 8px;">Confirm New Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   style="width: 100%; padding: 12px 16px; border: 1px solid var(--skc-border); border-radius: 8px; font-size: 16px; outline: none; transition: border-color 0.2s;">
                        </div>
                        
                        <button type="submit" 
                                style="width: 100%; background: var(--skc-black); color: white; padding: 16px 24px; border: none; border-radius: 8px; font-size: 18px; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
                            Change Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Addresses Section -->
    @if(isset($addresses) && $addresses->count() > 0)
    <section class="skc-section" style="background: var(--skc-light-gray);">
        <div class="skc-container">
            <div style="background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); padding: 40px;">
                <h2 style="font-size: 28px; font-weight: 700; color: var(--skc-black); margin-bottom: 30px;">Saved Addresses</h2>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px;">
                    @foreach($addresses as $address)
                        <div style="border: 1px solid var(--skc-border); border-radius: 10px; padding: 20px; background: white;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                                <h3 style="font-size: 18px; font-weight: 600; color: var(--skc-black);">{{ $address->label }}</h3>
                                <form method="POST" action="{{ route('account.address.delete', $address->id) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" 
                                            style="background: none; border: none; color: #dc2626; cursor: pointer; font-size: 14px; font-weight: 500;"
                                            onclick="return confirm('Are you sure you want to remove this address?')">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                </form>
                            </div>
                            <div style="color: var(--skc-medium-gray); font-size: 14px; line-height: 1.6;">
                                <p>{{ $address->line1 }}</p>
                                @if($address->line2)
                                    <p>{{ $address->line2 }}</p>
                                @endif
                                <p>{{ $address->city }} - {{ $address->pincode }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <h3 style="font-size: 20px; font-weight: 600; color: var(--skc-black); margin-bottom: 20px;">Add New Address</h3>
                <form method="POST" action="{{ route('account.address.store') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    @csrf
                    <div>
                        <label for="label" style="display: block; font-size: 14px; font-weight: 600; color: var(--skc-black); margin-bottom: 8px;">Label *</label>
                        <input type="text" id="label" name="label" placeholder="Home/Work" required
                               style="width: 100%; padding: 12px 16px; border: 1px solid var(--skc-border); border-radius: 8px; font-size: 16px; outline: none; transition: border-color 0.2s;">
                    </div>
                    <div>
                        <label for="line1" style="display: block; font-size: 14px; font-weight: 600; color: var(--skc-black); margin-bottom: 8px;">Address Line 1 *</label>
                        <input type="text" id="line1" name="line1" placeholder="Street address" required
                               style="width: 100%; padding: 12px 16px; border: 1px solid var(--skc-border); border-radius: 8px; font-size: 16px; outline: none; transition: border-color 0.2s;">
                    </div>
                    <div>
                        <label for="line2" style="display: block; font-size: 14px; font-weight: 600; color: var(--skc-black); margin-bottom: 8px;">Address Line 2</label>
                        <input type="text" id="line2" name="line2" placeholder="Apartment, suite, etc."
                               style="width: 100%; padding: 12px 16px; border: 1px solid var(--skc-border); border-radius: 8px; font-size: 16px; outline: none; transition: border-color 0.2s;">
                    </div>
                    <div>
                        <label for="city" style="display: block; font-size: 14px; font-weight: 600; color: var(--skc-black); margin-bottom: 8px;">City *</label>
                        <input type="text" id="city" name="city" placeholder="City" required
                               style="width: 100%; padding: 12px 16px; border: 1px solid var(--skc-border); border-radius: 8px; font-size: 16px; outline: none; transition: border-color 0.2s;">
                    </div>
                    <div>
                        <label for="pincode" style="display: block; font-size: 14px; font-weight: 600; color: var(--skc-black); margin-bottom: 8px;">Pincode *</label>
                        <input type="text" id="pincode" name="pincode" placeholder="Pincode" required
                               style="width: 100%; padding: 12px 16px; border: 1px solid var(--skc-border); border-radius: 8px; font-size: 16px; outline: none; transition: border-color 0.2s;">
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <button type="submit" 
                                style="padding: 12px 24px; background: var(--skc-orange); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                            Add Address
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    @endif

    <!-- Account Actions Section -->
    <section class="skc-section" style="background: var(--skc-light-gray);">
        <div class="skc-container">
            <div style="background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); padding: 40px;">
                <h2 style="font-size: 28px; font-weight: 700; color: var(--skc-black); margin-bottom: 30px;">Account Actions</h2>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                    <!-- Delete Account -->
                    <div style="border: 1px solid #fee2e2; border-radius: 10px; padding: 25px; background: #fef2f2;">
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                            <div style="width: 50px; height: 50px; background: #fee2e2; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-exclamation-triangle" style="font-size: 20px; color: #dc2626;"></i>
                            </div>
                            <div>
                                <h3 style="font-size: 20px; font-weight: 600; color: var(--skc-black);">Delete Account</h3>
                                <p style="color: var(--skc-medium-gray); font-size: 14px;">Permanently delete your account and all data</p>
                            </div>
                        </div>
                        <p style="color: #dc2626; font-size: 14px; margin-bottom: 20px; line-height: 1.5;">
                            This action cannot be undone. All your orders, preferences, and personal information will be permanently deleted.
                        </p>
                        <button onclick="confirmDeleteAccount()" 
                                style="padding: 12px 24px; background: #dc2626; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                            Delete Account
                        </button>
                    </div>
                    
                    <!-- Export Data -->
                    <div style="border: 1px solid var(--skc-border); border-radius: 10px; padding: 25px; background: white;">
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                            <div style="width: 50px; height: 50px; background: var(--skc-light-gray); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-download" style="font-size: 20px; color: var(--skc-orange);"></i>
                            </div>
                            <div>
                                <h3 style="font-size: 20px; font-weight: 600; color: var(--skc-black);">Export Data</h3>
                                <p style="color: var(--skc-medium-gray); font-size: 14px;">Download your personal data</p>
                            </div>
                        </div>
                        <p style="color: var(--skc-medium-gray); font-size: 14px; margin-bottom: 20px; line-height: 1.5;">
                            Download a copy of your personal data including orders, profile information, and preferences.
                        </p>
                        <a href="#" 
                           style="display: inline-block; padding: 12px 24px; background: var(--skc-orange); color: white; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; transition: all 0.2s;">
                            Export Data
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function confirmDeleteAccount() {
            if (confirm('Are you sure you want to delete your account? This action cannot be undone and all your data will be permanently lost.')) {
                if (confirm('This is your final warning. Are you absolutely sure you want to delete your account?')) {
                    // Note: This route doesn't exist yet, so we'll just show an alert
                    alert('Account deletion feature is not yet implemented.');
                }
            }
        }
    </script>
@endsection


