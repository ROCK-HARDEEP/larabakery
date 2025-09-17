<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
    use HasFactory;

    protected $table = 'contact_us';

    protected $fillable = [
        // Get In Touch Section
        'get_in_touch_image',
        'get_in_touch_title',
        'get_in_touch_quote',
        'get_in_touch_button_text',
        'get_in_touch_button_link',
        'get_in_touch_button_color',

        // Contact Information Section
        'contact_address',
        'contact_phone',
        'contact_email',
        'business_hours',

        // Social Media Section (JSON)
        'social_media_links',

        // Location/Map Section
        'map_embed_link',
        'map_image',
        'map_latitude',
        'map_longitude',
        'map_address',

        // FAQ Section (JSON)
        'faqs',
    ];

    protected $casts = [
        'social_media_links' => 'array',
        'faqs' => 'array',
        'business_hours' => 'array',
    ];
}