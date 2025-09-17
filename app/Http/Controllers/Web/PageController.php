<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use App\Models\ContactUs;
use App\Models\PageSection;

class PageController extends Controller
{
    public function about()
    {
        // Try to get data from AboutUs model first (existing data)
        $aboutUs = AboutUs::first();
        
        // If no data exists, create default with PageSection data
        if (!$aboutUs) {
            $aboutUs = new AboutUs();
        }
        
        // Get PageSection data and merge it
        $sections = PageSection::where('page', 'about')->where('is_active', true)->get();
        
        foreach ($sections as $section) {
            switch ($section->section) {
                case 'our_story':
                    $aboutUs->story_title = $section->title;
                    $aboutUs->story_content = strip_tags($section->content);
                    $aboutUs->story_image = $section->image;
                    break;
                    
                case 'how_it_began':
                    $aboutUs->began_title = $section->title;
                    $aboutUs->began_quote = $section->data['subtitle'] ?? substr(strip_tags($section->content), 0, 100);
                    $aboutUs->began_content = strip_tags($section->content);
                    if (isset($section->data['years_experience'])) {
                        $aboutUs->years_experience = $section->data['years_experience'];
                    }
                    if (isset($section->data['happy_customers'])) {
                        $aboutUs->happy_customers = $section->data['happy_customers'];
                    }
                    break;
                    
                case 'our_values':
                    if (isset($section->data['values'])) {
                        $values = [];
                        foreach ($section->data['values'] as $value) {
                            $iconMap = [
                                'star' => 'fas fa-star',
                                'heart' => 'fas fa-heart',
                                'leaf' => 'fas fa-leaf',
                                'lightbulb' => 'fas fa-lightbulb',
                                'flag' => 'fas fa-flag',
                                'shield' => 'fas fa-shield-alt',
                                'trophy' => 'fas fa-trophy',
                                'hand' => 'fas fa-handshake'
                            ];
                            $values[] = [
                                'icon' => $iconMap[$value['icon']] ?? 'fas fa-star',
                                'title' => $value['title'],
                                'description' => $value['description']
                            ];
                        }
                        $aboutUs->values = $values;
                    }
                    break;
                    
                case 'meet_our_team':
                    if (isset($section->data['members'])) {
                        $team = [];
                        foreach ($section->data['members'] as $member) {
                            $team[] = [
                                'name' => $member['name'],
                                'designation' => $member['role'] ?? $member['designation'] ?? '',
                                'description' => $member['bio'] ?? $member['description'] ?? '',
                                'image' => $member['image'] ?? null
                            ];
                        }
                        $aboutUs->team_members = $team;
                    }
                    break;
                    
                case 'ready_to_taste':
                    $aboutUs->cta_title = $section->title;
                    $aboutUs->cta_subtitle = strip_tags($section->content);
                    $aboutUs->cta_button_text = $section->data['button_text'] ?? 'Shop Our Products';
                    $aboutUs->cta_button_link = $section->data['button_link'] ?? '/products';
                    $aboutUs->cta_section_color = $section->data['background_color'] ?? '#000000';
                    $aboutUs->cta_button_color = $section->data['button_color'] ?? '#FF6B00';
                    // If there's a background image, it will be in $section->image
                    if ($section->image) {
                        // The frontend can check for this image and use it instead of the color
                        $aboutUs->cta_background_image = $section->image;
                    }
                    break;
            }
        }
        
        // Set default values if not set
        if (!$aboutUs->story_title) {
            $aboutUs->story_title = 'Our Story';
            $aboutUs->story_content = 'From humble beginnings to becoming the most loved bakery in the city, we\'ve been crafting delicious memories for over a decade.';
        }
        if (!$aboutUs->began_title) {
            $aboutUs->began_title = 'How It All Began';
            $aboutUs->began_quote = 'A journey of passion, tradition, and community';
            $aboutUs->began_content = 'It all started in a small kitchen with a dream and a family recipe book.';
        }
        if (!$aboutUs->years_experience) {
            $aboutUs->years_experience = '10+';
        }
        if (!$aboutUs->happy_customers) {
            $aboutUs->happy_customers = '1000+';
        }
        if (!$aboutUs->cta_title) {
            $aboutUs->cta_title = 'Ready to Taste the Difference?';
            $aboutUs->cta_subtitle = 'Experience the love and tradition in every bite';
            $aboutUs->cta_button_text = 'Shop Our Products';
            $aboutUs->cta_button_link = '/products';
            $aboutUs->cta_section_color = '#000000';
            $aboutUs->cta_button_color = '#FF6B00';
        }
        if (empty($aboutUs->values)) {
            $aboutUs->values = [
                ['icon' => 'fas fa-heart', 'title' => 'Made with Love', 'description' => 'Every item is crafted with passion.'],
                ['icon' => 'fas fa-leaf', 'title' => 'Quality Ingredients', 'description' => 'We source only the finest ingredients.'],
                ['icon' => 'fas fa-users', 'title' => 'Community First', 'description' => 'We\'re part of the community.']
            ];
        }
        if (empty($aboutUs->team_members)) {
            $aboutUs->team_members = [
                ['name' => 'John Doe', 'designation' => 'Head Baker', 'description' => 'Master of traditional recipes.', 'image' => null],
                ['name' => 'Jane Smith', 'designation' => 'Pastry Chef', 'description' => 'Creative pastry designer.', 'image' => null]
            ];
        }
        
        return view('web.pages.about', compact('aboutUs'));
    }

    public function contact()
    {
        // Try to get data from ContactUs model first (existing data)
        $contactUs = ContactUs::first();
        
        // If no data exists, create default with PageSection data
        if (!$contactUs) {
            $contactUs = new ContactUs();
        }
        
        // Get PageSection data and merge it
        $sections = PageSection::where('page', 'contact')->where('is_active', true)->get();
        
        foreach ($sections as $section) {
            switch ($section->section) {
                case 'contact_form':
                    $contactUs->get_in_touch_title = $section->title;
                    $contactUs->get_in_touch_quote = strip_tags($section->content);
                    // Add the image from PageSection
                    if ($section->image) {
                        $contactUs->get_in_touch_image = $section->image;
                    }
                    // Add button data if exists
                    if (isset($section->data['button_text']) && $section->data['button_text']) {
                        $contactUs->get_in_touch_button_text = $section->data['button_text'];
                        $contactUs->get_in_touch_button_link = $section->data['button_link'] ?? '/contact';
                    }
                    break;
                    
                case 'contact_info':
                    if (isset($section->data['address'])) {
                        $contactUs->contact_address = $section->data['address'];
                    }
                    if (isset($section->data['phone'])) {
                        $contactUs->contact_phone = $section->data['phone'];
                    }
                    if (isset($section->data['email'])) {
                        $contactUs->contact_email = $section->data['email'];
                    }
                    break;
                    
                case 'business_hours':
                    if (isset($section->data['hours'])) {
                        $hours = [];
                        foreach ($section->data['hours'] as $hour) {
                            $hours[] = [
                                'day' => $hour['day'],
                                'hours' => $hour['open'] . ' - ' . $hour['close']
                            ];
                        }
                        $contactUs->business_hours = $hours;
                    }
                    break;
                    
                case 'location_map':
                    if (isset($section->data['latitude']) && isset($section->data['longitude'])) {
                        // Create Google Maps embed URL
                        $lat = $section->data['latitude'];
                        $lng = $section->data['longitude'];
                        $zoom = $section->data['zoom'] ?? 15;
                        $contactUs->map_embed_link = "https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d1000!2d{$lng}!3d{$lat}!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sus";
                    }
                    break;
            }
        }
        
        // Set default values if not set
        if (!$contactUs->get_in_touch_title) {
            $contactUs->get_in_touch_title = 'Get In Touch';
            $contactUs->get_in_touch_quote = 'We would love to hear from you. Feel free to reach out to us anytime!';
        }
        if (!$contactUs->contact_address) {
            $contactUs->contact_address = '123 Bakery Street, Downtown, City 12345';
        }
        if (!$contactUs->contact_phone) {
            $contactUs->contact_phone = '+1 (555) 123-4567';
        }
        if (!$contactUs->contact_email) {
            $contactUs->contact_email = 'hello@sweetbakery.com';
        }
        if (empty($contactUs->business_hours)) {
            $contactUs->business_hours = [
                ['day' => 'Monday - Friday', 'hours' => '8:00 AM - 8:00 PM'],
                ['day' => 'Saturday', 'hours' => '9:00 AM - 6:00 PM'],
                ['day' => 'Sunday', 'hours' => '10:00 AM - 4:00 PM'],
            ];
        }
        if (empty($contactUs->social_media_links)) {
            $contactUs->social_media_links = [
                ['platform' => 'facebook', 'url' => 'https://facebook.com/sweetbakery', 'icon' => 'fab fa-facebook-f'],
                ['platform' => 'instagram', 'url' => 'https://instagram.com/sweetbakery', 'icon' => 'fab fa-instagram'],
                ['platform' => 'twitter', 'url' => 'https://twitter.com/sweetbakery', 'icon' => 'fab fa-twitter'],
            ];
        }
        if (!$contactUs->map_embed_link) {
            $contactUs->map_embed_link = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.2219901290355!2d-74.00369368400567!3d40.71312937933039!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a316bb5a2ad%3A0x8b8a8f0f0f0f0f0f!2sNew+York%2C+NY%2C+USA!5e0!3m2!1sen!2sus!4v1234567890';
        }
        if (empty($contactUs->faqs)) {
            $contactUs->faqs = [
                ['id' => '1', 'question' => 'What are your opening hours?', 'answer' => 'Check our business hours section above.', 'is_active' => true],
                ['id' => '2', 'question' => 'Do you offer custom cake orders?', 'answer' => 'Yes! We specialize in custom cakes for all occasions.', 'is_active' => true],
                ['id' => '3', 'question' => 'Do you deliver?', 'answer' => 'Yes, we offer delivery within a 10-mile radius.', 'is_active' => true],
            ];
        }
        
        return view('web.pages.contact', compact('contactUs'));
    }
}
