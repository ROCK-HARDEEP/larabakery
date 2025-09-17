<x-filament-panels::page>
    <style>
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .profile-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }
        
        @media (min-width: 1024px) {
            .profile-grid {
                grid-template-columns: 2fr 1fr;
            }
        }
        
        .profile-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 24px;
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
        }
        
        .profile-info h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #111827;
            margin: 0 0 4px 0;
        }
        
        .profile-info p {
            font-size: 0.875rem;
            color: #6b7280;
            margin: 0;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }
        
        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        
        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        @media (max-width: 640px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            padding: 10px 24px;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            width: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }
        
        .section-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .section-icon {
            width: 24px;
            height: 24px;
            margin-right: 8px;
        }
        
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 48px;
            height: 24px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #d1d5db;
            transition: .3s;
            border-radius: 24px;
        }
        
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
        }
        
        input:checked + .toggle-slider {
            background-color: #f59e0b;
        }
        
        input:checked + .toggle-slider:before {
            transform: translateX(24px);
        }
        
        .notification-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .notification-item:last-child {
            border-bottom: none;
        }
    </style>

    <div class="profile-container">
        <div class="profile-grid">
            <!-- Left Column -->
            <div>
                <!-- Profile Information -->
                <div class="profile-card" style="margin-bottom: 24px;">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <x-heroicon-o-user class="w-10 h-10 text-white" />
                        </div>
                        <div class="profile-info">
                            <h2>{{ auth()->user()->name ?? 'User Profile' }}</h2>
                            <p>{{ auth()->user()->email ?? 'user@example.com' }}</p>
                        </div>
                    </div>

                    <form wire:submit.prevent="updateProfile">
                        <div class="section-title">
                            <x-heroicon-o-user-circle class="section-icon text-gray-500" />
                            Personal Information
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Name</label>
                                <input type="text" wire:model="name" class="form-input" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" wire:model="email" class="form-input" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Phone</label>
                                <input type="tel" wire:model="phone" class="form-input">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Position</label>
                                <input type="text" wire:model="position" class="form-input" placeholder="Your position">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Bio</label>
                            <textarea wire:model="bio" class="form-textarea" placeholder="Tell us about yourself..."></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Signature</label>
                            <textarea wire:model="signature" class="form-textarea" rows="3" placeholder="Your email signature..."></textarea>
                        </div>

                        <button type="submit" class="btn-primary">
                            <x-heroicon-o-check class="w-5 h-5 inline mr-2" />
                            Update Profile
                        </button>
                    </form>
                </div>

                <!-- Change Password -->
                <div class="profile-card">
                    <form wire:submit.prevent="updatePassword">
                        <div class="section-title">
                            <x-heroicon-o-key class="section-icon text-gray-500" />
                            Change Password
                        </div>

                        <div class="form-group">
                            <label class="form-label">Current Password</label>
                            <input type="password" wire:model="current_password" class="form-input">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">New Password</label>
                                <input type="password" wire:model="new_password" class="form-input">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" wire:model="new_password_confirmation" class="form-input">
                            </div>
                        </div>

                        <button type="submit" class="btn-primary">
                            <x-heroicon-o-lock-closed class="w-5 h-5 inline mr-2" />
                            Update Password
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Column -->
            <div>
                <!-- Preferences -->
                <div class="profile-card" style="margin-bottom: 24px;">
                    <div class="section-title">
                        <x-heroicon-o-cog-6-tooth class="section-icon text-gray-500" />
                        Preferences
                    </div>

                    <form wire:submit.prevent="updateProfile">
                        <div class="form-group">
                            <label class="form-label">Timezone</label>
                            <select wire:model="timezone" class="form-select">
                                <option value="Asia/Kolkata">Asia/Kolkata</option>
                                <option value="UTC">UTC</option>
                                <option value="America/New_York">America/New York</option>
                                <option value="Europe/London">Europe/London</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Language</label>
                            <select wire:model="language" class="form-select">
                                <option value="en">English</option>
                                <option value="hi">Hindi</option>
                                <option value="ta">Tamil</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-primary">
                            <x-heroicon-o-check class="w-5 h-5 inline mr-2" />
                            Save Preferences
                        </button>
                    </form>
                </div>

                <!-- Notifications -->
                <div class="profile-card">
                    <div class="section-title">
                        <x-heroicon-o-bell class="section-icon text-gray-500" />
                        Notifications
                    </div>

                    <form wire:submit.prevent="updateProfile">
                        <div class="notification-item">
                            <div>
                                <div style="font-weight: 500; color: #111827;">Email Notifications</div>
                                <div style="font-size: 0.875rem; color: #6b7280;">Receive updates via email</div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" wire:model="email_notifications">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="notification-item">
                            <div>
                                <div style="font-weight: 500; color: #111827;">SMS Notifications</div>
                                <div style="font-size: 0.875rem; color: #6b7280;">Receive updates via SMS</div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" wire:model="sms_notifications">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="notification-item">
                            <div>
                                <div style="font-weight: 500; color: #111827;">Two-Factor Authentication</div>
                                <div style="font-size: 0.875rem; color: #6b7280;">Add extra security to your account</div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" wire:model="two_factor_enabled">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <button type="submit" class="btn-primary" style="margin-top: 20px;">
                            <x-heroicon-o-check class="w-5 h-5 inline mr-2" />
                            Save Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>