<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeaderFooterSettings extends Model
{
    use HasFactory;

    protected $table = 'header_footer_settings';

    protected $fillable = [
        // Header Settings
        'header_logo',
        'header_brand_name',
        
        // Announcement Bar Settings
        'announcement_bar_enabled',
        'announcement_bar_text',
        'announcement_bar_bg_color',
        'announcement_bar_text_color',
        
        // Footer Settings
        'footer_logo',
        'footer_brand_name',
        'footer_description',
        'footer_background_color',
        'footer_text_color',
        
        // Social Media Links
        'social_media_links',
        
        // Quick Links
        'quick_links',
        
        // Categories Links
        'category_links',
        
        // Customer Service Links
        'customer_service_links',
        
        // Contact Information
        'contact_address',
        'contact_phone',
        'contact_email',
        'contact_hours',
    ];

    protected $casts = [
        'announcement_bar_enabled' => 'boolean',
        'social_media_links' => 'array',
        'quick_links' => 'array',
        'category_links' => 'array',
        'customer_service_links' => 'array',
    ];
}