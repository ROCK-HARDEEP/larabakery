<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use UnitEnum;
use BackedEnum;

class AdminSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static ?string $navigationLabel = 'Admin Settings';
    protected static ?string $navigationGroup = 'System Settings';
    protected static ?int $navigationSort = 81;
    protected static bool $shouldRegisterNavigation = true;
    protected static string $view = 'filament.pages.admin-settings';

    public static function shouldRegisterNavigation(): bool
    {
        // Only show in navigation for superadmin users
        $user = auth()->user();
        if (!$user) return false;

        if (!$user->relationLoaded('roles')) {
            $user->load('roles');
        }

        return $user->hasRole('superadmin');
    }

    public static function canAccess(): bool
    {
        // Only allow access for superadmin users
        $user = auth()->user();
        if (!$user) return false;

        if (!$user->relationLoaded('roles')) {
            $user->load('roles');
        }

        return $user->hasRole('superadmin');
    }
    
    // General Settings
    public ?string $site_name = null;
    public ?string $site_tagline = null;
    public ?string $site_description = null;
    public ?string $contact_email = null;
    public ?string $contact_phone = null;
    public ?string $contact_address = null;
    public ?string $contact_city = null;
    public ?string $contact_state = null;
    public ?string $contact_pincode = null;
    public ?string $contact_country = null;
    public ?string $default_language = null;
    public ?string $default_currency = null;
    public ?string $currency_symbol = null;
    public ?string $timezone = null;
    
    // Payment Settings
    public bool $enable_cod = true;
    public ?string $cod_extra_charge = null;
    public bool $enable_razorpay = false;
    public ?string $razorpay_key_id = null;
    public ?string $razorpay_key_secret = null;
    public bool $enable_phonepe = false;
    public ?string $phonepe_merchant_id = null;
    public ?string $phonepe_salt_key = null;
    public bool $enable_paypal = false;
    public ?string $paypal_client_id = null;
    public ?string $paypal_client_secret = null;
    public bool $enable_stripe = false;
    public ?string $stripe_publishable_key = null;
    public ?string $stripe_secret_key = null;
    public ?string $transaction_currency = null;
    public bool $allow_refunds = true;
    public ?string $refund_time_limit = null;
    
    // Shipping Settings
    public bool $enable_delivery = true;
    public ?string $delivery_pincodes = null;
    public ?string $delivery_radius = null;
    public bool $enable_flat_rate = true;
    public ?string $flat_rate_amount = null;
    public bool $enable_free_shipping = true;
    public ?string $free_shipping_minimum = null;
    public bool $enable_local_pickup = true;
    public bool $enable_express_delivery = false;
    public ?string $express_delivery_charge = null;
    public bool $enable_delivery_slots = true;
    public ?string $delivery_slot_morning = null;
    public ?string $delivery_slot_afternoon = null;
    public ?string $delivery_slot_evening = null;
    public ?string $same_day_cutoff = null;
    public ?string $next_day_cutoff = null;
    public ?string $preparation_time = null;

    public function mount(): void
    {
        $this->loadSettings();
    }
    
    public function getTitle(): string
    {
        return 'Admin Settings';
    }

    public function getHeading(): string
    {
        return 'Admin Settings';
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('clearCache')
                ->label('Clear Cache')
                ->icon('heroicon-o-trash')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Clear All Caches')
                ->modalDescription('Are you sure you want to clear all application caches? This action cannot be undone.')
                ->modalSubmitActionLabel('Yes, clear cache')
                ->action(function () {
                    Artisan::call('cache:clear');
                    Artisan::call('config:clear');
                    Artisan::call('view:clear');
                    Artisan::call('route:clear');
                    
                    Notification::make()
                        ->title('Cache Cleared')
                        ->body('All application caches have been cleared successfully.')
                        ->success()
                        ->send();
                }),
        ];
    }
    
    public function getClearCacheActionProperty(): Action
    {
        return Action::make('clearCache')
            ->label('Clear Cache')
            ->icon('heroicon-o-trash')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Clear All Caches')
            ->modalDescription('Are you sure you want to clear all application caches? This action cannot be undone.')
            ->modalSubmitActionLabel('Yes, clear cache')
            ->action(function () {
                Artisan::call('cache:clear');
                Artisan::call('config:clear');
                Artisan::call('view:clear');
                Artisan::call('route:clear');
                
                Notification::make()
                    ->title('Cache Cleared')
                    ->body('All application caches have been cleared successfully.')
                    ->success()
                    ->send();
            });
    }
    
    protected function loadSettings(): void
    {
        // Load General Settings
        $this->site_name = Cache::get('settings.site_name', config('app.name', 'Bakery Shop'));
        $this->site_tagline = Cache::get('settings.site_tagline', 'Fresh Baked Daily');
        $this->site_description = Cache::get('settings.site_description', '');
        $this->contact_email = Cache::get('settings.contact_email', '');
        $this->contact_phone = Cache::get('settings.contact_phone', '');
        $this->contact_address = Cache::get('settings.contact_address', '');
        $this->contact_city = Cache::get('settings.contact_city', '');
        $this->contact_state = Cache::get('settings.contact_state', '');
        $this->contact_pincode = Cache::get('settings.contact_pincode', '');
        $this->contact_country = Cache::get('settings.contact_country', 'India');
        $this->default_language = Cache::get('settings.default_language', 'en');
        $this->default_currency = Cache::get('settings.default_currency', 'INR');
        $this->currency_symbol = Cache::get('settings.currency_symbol', '₹');
        $this->timezone = Cache::get('settings.timezone', 'Asia/Kolkata');
        
        // Load Payment Settings
        $this->enable_cod = Cache::get('payment.enable_cod', true);
        $this->cod_extra_charge = Cache::get('payment.cod_extra_charge', '0');
        $this->enable_razorpay = Cache::get('payment.enable_razorpay', false);
        $this->razorpay_key_id = Cache::get('payment.razorpay_key_id', '');
        $this->razorpay_key_secret = Cache::get('payment.razorpay_key_secret', '');
        $this->enable_phonepe = Cache::get('payment.enable_phonepe', false);
        $this->phonepe_merchant_id = Cache::get('payment.phonepe_merchant_id', '');
        $this->phonepe_salt_key = Cache::get('payment.phonepe_salt_key', '');
        $this->enable_paypal = Cache::get('payment.enable_paypal', false);
        $this->paypal_client_id = Cache::get('payment.paypal_client_id', '');
        $this->paypal_client_secret = Cache::get('payment.paypal_client_secret', '');
        $this->enable_stripe = Cache::get('payment.enable_stripe', false);
        $this->stripe_publishable_key = Cache::get('payment.stripe_publishable_key', '');
        $this->stripe_secret_key = Cache::get('payment.stripe_secret_key', '');
        $this->transaction_currency = Cache::get('payment.transaction_currency', 'INR');
        $this->allow_refunds = Cache::get('payment.allow_refunds', true);
        $this->refund_time_limit = Cache::get('payment.refund_time_limit', '7');
        
        // Load Shipping Settings
        $this->enable_delivery = Cache::get('shipping.enable_delivery', true);
        $this->delivery_pincodes = Cache::get('shipping.delivery_pincodes', '');
        $this->delivery_radius = Cache::get('shipping.delivery_radius', '10');
        $this->enable_flat_rate = Cache::get('shipping.enable_flat_rate', true);
        $this->flat_rate_amount = Cache::get('shipping.flat_rate_amount', '50');
        $this->enable_free_shipping = Cache::get('shipping.enable_free_shipping', true);
        $this->free_shipping_minimum = Cache::get('shipping.free_shipping_minimum', '500');
        $this->enable_local_pickup = Cache::get('shipping.enable_local_pickup', true);
        $this->enable_express_delivery = Cache::get('shipping.enable_express_delivery', false);
        $this->express_delivery_charge = Cache::get('shipping.express_delivery_charge', '100');
        $this->enable_delivery_slots = Cache::get('shipping.enable_delivery_slots', true);
        $this->delivery_slot_morning = Cache::get('shipping.delivery_slot_morning', '9:00 AM - 12:00 PM');
        $this->delivery_slot_afternoon = Cache::get('shipping.delivery_slot_afternoon', '12:00 PM - 3:00 PM');
        $this->delivery_slot_evening = Cache::get('shipping.delivery_slot_evening', '3:00 PM - 6:00 PM');
        $this->same_day_cutoff = Cache::get('shipping.same_day_cutoff', '14:00');
        $this->next_day_cutoff = Cache::get('shipping.next_day_cutoff', '20:00');
        $this->preparation_time = Cache::get('shipping.preparation_time', '60');
    }
    
    public function saveGeneral(): void
    {
        $generalData = [
            'site_name' => $this->site_name,
            'site_tagline' => $this->site_tagline,
            'site_description' => $this->site_description,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'contact_address' => $this->contact_address,
            'contact_city' => $this->contact_city,
            'contact_state' => $this->contact_state,
            'contact_pincode' => $this->contact_pincode,
            'contact_country' => $this->contact_country,
            'default_language' => $this->default_language,
            'default_currency' => $this->default_currency,
            'currency_symbol' => $this->currency_symbol,
            'timezone' => $this->timezone,
        ];
        
        foreach ($generalData as $key => $value) {
            Cache::put("settings.{$key}", $value);
        }
        
        Notification::make()
            ->title('General Settings Saved')
            ->body('General settings have been updated successfully.')
            ->success()
            ->send();
    }
    
    public function savePayment(): void
    {
        $paymentData = [
            'enable_cod' => $this->enable_cod,
            'cod_extra_charge' => $this->cod_extra_charge,
            'enable_razorpay' => $this->enable_razorpay,
            'razorpay_key_id' => $this->razorpay_key_id,
            'razorpay_key_secret' => $this->razorpay_key_secret,
            'enable_phonepe' => $this->enable_phonepe,
            'phonepe_merchant_id' => $this->phonepe_merchant_id,
            'phonepe_salt_key' => $this->phonepe_salt_key,
            'enable_paypal' => $this->enable_paypal,
            'paypal_client_id' => $this->paypal_client_id,
            'paypal_client_secret' => $this->paypal_client_secret,
            'enable_stripe' => $this->enable_stripe,
            'stripe_publishable_key' => $this->stripe_publishable_key,
            'stripe_secret_key' => $this->stripe_secret_key,
            'transaction_currency' => $this->transaction_currency,
            'allow_refunds' => $this->allow_refunds,
            'refund_time_limit' => $this->refund_time_limit,
        ];
        
        foreach ($paymentData as $key => $value) {
            Cache::put("payment.{$key}", $value);
        }
        
        Notification::make()
            ->title('Payment Settings Saved')
            ->body('Payment settings have been updated successfully.')
            ->success()
            ->send();
    }
    
    public function saveShipping(): void
    {
        $shippingData = [
            'enable_delivery' => $this->enable_delivery,
            'delivery_pincodes' => $this->delivery_pincodes,
            'delivery_radius' => $this->delivery_radius,
            'enable_flat_rate' => $this->enable_flat_rate,
            'flat_rate_amount' => $this->flat_rate_amount,
            'enable_free_shipping' => $this->enable_free_shipping,
            'free_shipping_minimum' => $this->free_shipping_minimum,
            'enable_local_pickup' => $this->enable_local_pickup,
            'enable_express_delivery' => $this->enable_express_delivery,
            'express_delivery_charge' => $this->express_delivery_charge,
            'enable_delivery_slots' => $this->enable_delivery_slots,
            'delivery_slot_morning' => $this->delivery_slot_morning,
            'delivery_slot_afternoon' => $this->delivery_slot_afternoon,
            'delivery_slot_evening' => $this->delivery_slot_evening,
            'same_day_cutoff' => $this->same_day_cutoff,
            'next_day_cutoff' => $this->next_day_cutoff,
            'preparation_time' => $this->preparation_time,
        ];
        
        foreach ($shippingData as $key => $value) {
            Cache::put("shipping.{$key}", $value);
        }
        
        Notification::make()
            ->title('Shipping Settings Saved')
            ->body('Shipping & delivery settings have been updated successfully.')
            ->success()
            ->send();
    }
}