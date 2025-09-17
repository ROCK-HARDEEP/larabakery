<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\HeaderFooterSettings;

class HeaderFooterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $headerFooterSettings = HeaderFooterSettings::first();
            
            if (!$headerFooterSettings) {
                $headerFooterSettings = HeaderFooterSettings::create([
                    'header_brand_name' => config('app.name', 'Bakery Shop'),
                    'announcement_bar_enabled' => true,
                    'announcement_bar_text' => '🎂 Fresh Baked Daily | FREE Delivery on orders above ₹500 | Same Day Delivery Available 🚚',
                    'announcement_bar_bg_color' => '#f69d1c',
                    'announcement_bar_text_color' => '#ffffff',
                    'footer_brand_name' => config('app.name', 'Bakery Shop'),
                    'footer_description' => 'Your favorite neighborhood bakery serving fresh, delicious treats daily.',
                    'footer_background_color' => '#1a1a1a',
                    'footer_text_color' => '#ffffff',
                    'social_media_links' => [
                        ['platform' => 'facebook', 'url' => 'https://facebook.com', 'icon' => 'fab fa-facebook-f'],
                        ['platform' => 'instagram', 'url' => 'https://instagram.com', 'icon' => 'fab fa-instagram'],
                        ['platform' => 'twitter', 'url' => 'https://twitter.com', 'icon' => 'fab fa-twitter'],
                        ['platform' => 'youtube', 'url' => 'https://youtube.com', 'icon' => 'fab fa-youtube'],
                    ],
                    'quick_links' => [
                        ['title' => 'Home', 'url' => '/'],
                        ['title' => 'About Us', 'url' => '/about'],
                        ['title' => 'Products', 'url' => '/products'],
                        ['title' => 'Contact', 'url' => '/contact'],
                    ],
                    'category_links' => [
                        ['title' => 'Cakes', 'url' => '/category/cakes'],
                        ['title' => 'Pastries', 'url' => '/category/pastries'],
                        ['title' => 'Coffee', 'url' => '/category/coffee'],
                        ['title' => 'Beverages', 'url' => '/category/beverages'],
                    ],
                    'customer_service_links' => [
                        ['title' => 'FAQ', 'url' => '/faq'],
                        ['title' => 'Shipping Info', 'url' => '/shipping'],
                        ['title' => 'Returns', 'url' => '/returns'],
                        ['title' => 'Terms & Conditions', 'url' => '/terms'],
                    ],
                    'contact_address' => '123 Bakery Street, Sweet City, SC 12345',
                    'contact_phone' => '+1 (555) 123-4567',
                    'contact_email' => 'hello@bakeryshop.com',
                    'contact_hours' => 'Mon-Fri: 8AM-8PM, Sat-Sun: 9AM-6PM',
                ]);
            }
            
            $view->with('headerFooterSettings', $headerFooterSettings);
        });
    }
}