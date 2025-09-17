<x-filament-panels::page>
    <style>
        /* Container and Layout */
        .settings-container {
            width: 100%;
            max-width: 100%;
            padding: 0 20px;
        }
        
        @media (min-width: 1280px) {
            .settings-container {
                max-width: 1280px;
                margin: 0 auto;
            }
        }
        
        @media (min-width: 1536px) {
            .settings-container {
                max-width: 1536px;
            }
        }
        
        @media (min-width: 1920px) {
            .settings-container {
                max-width: 1800px;
            }
        }

        /* Settings Cards */
        .settings-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 16px;
            margin-bottom: 24px;
            width: 100%;
        }
        
        @media (min-width: 768px) {
            .settings-card {
                padding: 24px;
            }
        }
        
        @media (min-width: 1024px) {
            .settings-card {
                padding: 32px;
            }
        }

        /* Grid Layouts */
        .settings-grid {
            display: grid;
            gap: 24px;
            grid-template-columns: 1fr;
        }
        
        @media (min-width: 1280px) {
            .settings-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (min-width: 1920px) {
            .settings-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* Header Styles */
        .settings-header {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        @media (min-width: 640px) {
            .settings-header {
                flex-direction: row;
                align-items: center;
                margin-bottom: 24px;
            }
        }

        .settings-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }
        
        @media (min-width: 640px) {
            .settings-icon {
                margin-bottom: 0;
                margin-right: 16px;
            }
        }

        .settings-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }
        
        @media (min-width: 768px) {
            .settings-title {
                font-size: 1.25rem;
            }
        }
        
        @media (min-width: 1024px) {
            .settings-title {
                font-size: 1.5rem;
            }
        }

        .settings-subtitle {
            font-size: 0.75rem;
            color: #6b7280;
            margin: 4px 0 0 0;
        }
        
        @media (min-width: 768px) {
            .settings-subtitle {
                font-size: 0.875rem;
            }
        }

        /* Form Elements */
        .form-grid {
            display: grid;
            gap: 16px;
        }
        
        @media (min-width: 768px) {
            .form-grid {
                gap: 20px;
            }
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }
        
        @media (min-width: 640px) {
            .form-row {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
        }
        
        @media (min-width: 1024px) {
            .form-row {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            }
        }

        .form-group {
            margin-bottom: 16px;
        }
        
        @media (min-width: 768px) {
            .form-group {
                margin-bottom: 20px;
            }
        }

        .form-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }
        
        @media (min-width: 768px) {
            .form-label {
                font-size: 0.875rem;
            }
        }

        .form-input, .form-select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        
        @media (min-width: 768px) {
            .form-input, .form-select {
                padding: 10px 14px;
            }
        }

        .form-input:hover, .form-select:hover, .form-textarea:hover {
            border-color: #9ca3af;
        }
        
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        }

        .form-textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.875rem;
            resize: vertical;
            min-height: 60px;
        }
        
        @media (min-width: 768px) {
            .form-textarea {
                min-height: 80px;
                padding: 10px 14px;
            }
        }

        .form-select {
            background: white;
            cursor: pointer;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1em;
            padding-right: 2.5rem;
        }
        /* Toggle Switch */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }
        
        @media (min-width: 768px) {
            .toggle-switch {
                width: 48px;
            }
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
            transform: translateX(20px);
        }
        
        @media (min-width: 768px) {
            input:checked + .toggle-slider:before {
                transform: translateX(24px);
            }
        }

        /* Utilities */
        .section-divider {
            border-top: 1px solid #e5e7eb;
            margin: 20px 0;
        }
        
        @media (min-width: 768px) {
            .section-divider {
                margin: 24px 0;
            }
        }

        /* Buttons */
        .btn-save {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
            width: 100%;
        }
        
        @media (min-width: 768px) {
            .btn-save {
                padding: 12px 24px;
                font-size: 1rem;
            }
        }
        
        .btn-save:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        /* Payment Method Cards */
        .payment-method-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 16px;
        }
        
        @media (min-width: 768px) {
            .payment-method-card {
                padding: 16px;
            }
        }
        
        .payment-method-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        
        .payment-method-title {
            font-weight: 600;
            color: #111827;
            font-size: 0.875rem;
        }
        
        @media (min-width: 768px) {
            .payment-method-title {
                font-size: 1rem;
            }
        }

        /* Shipping Columns */
        .shipping-columns {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }
        
        @media (min-width: 1024px) {
            .shipping-columns {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        /* Full Width Container */
        @media (min-width: 1920px) {
            .settings-full-width {
                grid-column: span 3;
            }
        }
        
        @media (min-width: 1280px) and (max-width: 1919px) {
            .settings-full-width {
                grid-column: span 2;
            }
        }
    </style>

    <div class="settings-container">
        <div class="settings-grid">
            <!-- General Settings -->
            <div class="settings-card">
            <div class="settings-header">
                <div class="settings-icon" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                    <x-heroicon-o-cog-6-tooth class="w-6 h-6 text-white" />
                </div>
                <div>
                    <h2 class="settings-title">General Settings</h2>
                    <p class="settings-subtitle">Configure your store's basic information</p>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Site Name</label>
                        <input type="text" wire:model="site_name" class="form-input" placeholder="Your Store Name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Site Tagline</label>
                        <input type="text" wire:model="site_tagline" class="form-input" placeholder="Your tagline">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Site Description</label>
                    <textarea wire:model="site_description" class="form-textarea" placeholder="Brief description of your store"></textarea>
                </div>

                <div class="section-divider"></div>

                <h3 style="font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 16px;">Contact Information</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" wire:model="contact_email" class="form-input" placeholder="contact@example.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="tel" wire:model="contact_phone" class="form-input" placeholder="+91 9876543210">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea wire:model="contact_address" class="form-textarea" rows="2" placeholder="Street address"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">City</label>
                        <input type="text" wire:model="contact_city" class="form-input" placeholder="City">
                    </div>
                    <div class="form-group">
                        <label class="form-label">State</label>
                        <input type="text" wire:model="contact_state" class="form-input" placeholder="State">
                    </div>
                    <div class="form-group">
                        <label class="form-label">PIN Code</label>
                        <input type="text" wire:model="contact_pincode" class="form-input" placeholder="600001">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Country</label>
                        <input type="text" wire:model="contact_country" class="form-input" placeholder="India">
                    </div>
                </div>

                <div class="section-divider"></div>

                <h3 style="font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 16px;">Localization</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Language</label>
                        <select wire:model="default_language" class="form-select">
                            <option value="en">English</option>
                            <option value="hi">Hindi</option>
                            <option value="ta">Tamil</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Timezone</label>
                        <select wire:model="timezone" class="form-select">
                            <option value="Asia/Kolkata">Asia/Kolkata</option>
                            <option value="UTC">UTC</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Currency</label>
                        <select wire:model="default_currency" class="form-select">
                            <option value="INR">INR (₹)</option>
                            <option value="USD">USD ($)</option>
                            <option value="EUR">EUR (€)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Currency Symbol</label>
                        <input type="text" wire:model="currency_symbol" class="form-input" placeholder="₹">
                    </div>
                </div>

                <button wire:click="saveGeneral" class="btn-save">
                    <x-heroicon-o-check class="w-5 h-5 inline mr-2" />
                    Save General Settings
                </button>
            </div>
            </div>

            <!-- Payment Settings -->
            <div class="settings-card">
            <div class="settings-header">
                <div class="settings-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <x-heroicon-o-credit-card class="w-6 h-6 text-white" />
                </div>
                <div>
                    <h2 class="settings-title">Payment Settings</h2>
                    <p class="settings-subtitle">Configure payment methods and gateways</p>
                </div>
            </div>

            <div class="form-grid">
                <!-- Cash on Delivery -->
                <div class="payment-method-card">
                    <div class="payment-method-header">
                        <span class="payment-method-title">Cash on Delivery</span>
                        <label class="toggle-switch">
                            <input type="checkbox" wire:model="enable_cod">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    @if($enable_cod)
                    <div class="form-group">
                        <label class="form-label">Extra COD Charge (₹)</label>
                        <input type="number" wire:model="cod_extra_charge" class="form-input" placeholder="0">
                    </div>
                    @endif
                </div>

                <!-- Razorpay -->
                <div class="payment-method-card">
                    <div class="payment-method-header">
                        <span class="payment-method-title">Razorpay</span>
                        <label class="toggle-switch">
                            <input type="checkbox" wire:model="enable_razorpay">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    @if($enable_razorpay)
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Key ID</label>
                            <input type="text" wire:model="razorpay_key_id" class="form-input" placeholder="rzp_test_...">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Key Secret</label>
                            <input type="password" wire:model="razorpay_key_secret" class="form-input" placeholder="••••••••">
                        </div>
                    </div>
                    @endif
                </div>

                <!-- PhonePe -->
                <div class="payment-method-card">
                    <div class="payment-method-header">
                        <span class="payment-method-title">PhonePe</span>
                        <label class="toggle-switch">
                            <input type="checkbox" wire:model="enable_phonepe">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    @if($enable_phonepe)
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Merchant ID</label>
                            <input type="text" wire:model="phonepe_merchant_id" class="form-input" placeholder="MERCHANT...">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Salt Key</label>
                            <input type="password" wire:model="phonepe_salt_key" class="form-input" placeholder="••••••••">
                        </div>
                    </div>
                    @endif
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Transaction Currency</label>
                        <select wire:model="transaction_currency" class="form-select">
                            <option value="INR">INR</option>
                            <option value="USD">USD</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Refund Time Limit (Days)</label>
                        <input type="number" wire:model="refund_time_limit" class="form-input" placeholder="7">
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" wire:model="allow_refunds" id="allow_refunds" style="width: 16px; height: 16px;">
                    <label for="allow_refunds" class="form-label" style="margin: 0;">Allow Refunds</label>
                </div>

                <button wire:click="savePayment" class="btn-save">
                    <x-heroicon-o-check class="w-5 h-5 inline mr-2" />
                    Save Payment Settings
                </button>
            </div>
            </div>

            <!-- Shipping Settings - Full Width -->
            <div class="settings-card settings-full-width">
            <div class="settings-header">
                <div class="settings-icon" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">
                    <x-heroicon-o-truck class="w-6 h-6 text-white" />
                </div>
                <div>
                    <h2 class="settings-title">Shipping & Delivery</h2>
                    <p class="settings-subtitle">Configure delivery options and schedules</p>
                </div>
            </div>

            <div class="form-grid">
                <div class="shipping-columns">
                    <!-- Left Column -->
                    <div>
                        <h3 style="font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 16px;">Delivery Zones</h3>
                        
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
                            <input type="checkbox" wire:model="enable_delivery" id="enable_delivery" style="width: 16px; height: 16px;">
                            <label for="enable_delivery" class="form-label" style="margin: 0;">Enable Delivery</label>
                        </div>

                        @if($enable_delivery)
                        <div class="form-group">
                            <label class="form-label">Delivery PIN Codes (comma separated)</label>
                            <textarea wire:model="delivery_pincodes" class="form-textarea" rows="3" placeholder="600001, 600002, 600003"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Delivery Radius (km)</label>
                            <input type="number" wire:model="delivery_radius" class="form-input" placeholder="10">
                        </div>
                        @endif

                        <h3 style="font-size: 1rem; font-weight: 600; color: #111827; margin: 24px 0 16px 0;">Shipping Methods</h3>
                        
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                            <input type="checkbox" wire:model="enable_flat_rate" id="enable_flat_rate" style="width: 16px; height: 16px;">
                            <label for="enable_flat_rate" class="form-label" style="margin: 0;">Flat Rate Shipping</label>
                        </div>
                        @if($enable_flat_rate)
                        <div class="form-group">
                            <label class="form-label">Flat Rate Amount (₹)</label>
                            <input type="number" wire:model="flat_rate_amount" class="form-input" placeholder="50">
                        </div>
                        @endif

                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                            <input type="checkbox" wire:model="enable_free_shipping" id="enable_free_shipping" style="width: 16px; height: 16px;">
                            <label for="enable_free_shipping" class="form-label" style="margin: 0;">Free Shipping</label>
                        </div>
                        @if($enable_free_shipping)
                        <div class="form-group">
                            <label class="form-label">Minimum Order Amount (₹)</label>
                            <input type="number" wire:model="free_shipping_minimum" class="form-input" placeholder="500">
                        </div>
                        @endif

                        <div style="display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" wire:model="enable_local_pickup" id="enable_local_pickup" style="width: 16px; height: 16px;">
                            <label for="enable_local_pickup" class="form-label" style="margin: 0;">Local Pickup</label>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div>
                        <h3 style="font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 16px;">Express Delivery</h3>
                        
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                            <input type="checkbox" wire:model="enable_express_delivery" id="enable_express_delivery" style="width: 16px; height: 16px;">
                            <label for="enable_express_delivery" class="form-label" style="margin: 0;">Enable Express Delivery</label>
                        </div>
                        @if($enable_express_delivery)
                        <div class="form-group">
                            <label class="form-label">Express Delivery Charge (₹)</label>
                            <input type="number" wire:model="express_delivery_charge" class="form-input" placeholder="100">
                        </div>
                        @endif

                        <h3 style="font-size: 1rem; font-weight: 600; color: #111827; margin: 24px 0 16px 0;">Delivery Slots</h3>
                        
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                            <input type="checkbox" wire:model="enable_delivery_slots" id="enable_delivery_slots" style="width: 16px; height: 16px;">
                            <label for="enable_delivery_slots" class="form-label" style="margin: 0;">Enable Delivery Slots</label>
                        </div>
                        @if($enable_delivery_slots)
                        <div class="form-group">
                            <label class="form-label">Morning Slot</label>
                            <input type="text" wire:model="delivery_slot_morning" class="form-input" placeholder="9:00 AM - 12:00 PM">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Afternoon Slot</label>
                            <input type="text" wire:model="delivery_slot_afternoon" class="form-input" placeholder="12:00 PM - 3:00 PM">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Evening Slot</label>
                            <input type="text" wire:model="delivery_slot_evening" class="form-input" placeholder="3:00 PM - 6:00 PM">
                        </div>
                        @endif

                        <h3 style="font-size: 1rem; font-weight: 600; color: #111827; margin: 24px 0 16px 0;">Order Timing</h3>
                        
                        <div class="form-group">
                            <label class="form-label">Same Day Delivery Cutoff</label>
                            <input type="time" wire:model="same_day_cutoff" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Next Day Delivery Cutoff</label>
                            <input type="time" wire:model="next_day_cutoff" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Preparation Time (minutes)</label>
                            <input type="number" wire:model="preparation_time" class="form-input" placeholder="60">
                        </div>
                    </div>
                </div>

                <button wire:click="saveShipping" class="btn-save">
                    <x-heroicon-o-check class="w-5 h-5 inline mr-2" />
                    Save Shipping Settings
                </button>
            </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>