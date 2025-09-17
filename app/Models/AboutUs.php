<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutUs extends Model
{
    use HasFactory;

    protected $table = 'about_us';

    protected $fillable = [
        // Hero/Story Section
        'story_image',
        'story_title',
        'story_content',
        'story_quote',

        // How It All Began Section
        'began_title',
        'began_quote',
        'began_content',
        'began_image',
        'years_experience',
        'happy_customers',

        // Our Values Section (JSON)
        'values',

        // Meet Our Team Section (JSON)
        'team_members',

        // Call to Action Section
        'cta_section_color',
        'cta_title',
        'cta_subtitle',
        'cta_description',
        'cta_button_text',
        'cta_button_link',
        'cta_button_color',
        'cta_background_image',
    ];

    protected $casts = [
        'values' => 'array',
        'team_members' => 'array',
    ];
}