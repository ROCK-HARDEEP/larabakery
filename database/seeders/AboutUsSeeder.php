<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AboutUs;

class AboutUsSeeder extends Seeder
{
    public function run(): void
    {
        AboutUs::truncate();
        
        AboutUs::create([
            'story_image' => 'about-us/our-story.jpg',
            'story_title' => 'Our Story',
            'story_content' => 'From humble beginnings to becoming the most loved bakery in the city, we\'ve been crafting delicious memories for over a decade.',
            
            'began_title' => 'How It All Began',
            'began_quote' => 'A journey of passion, tradition, and community',
            'began_content' => "It all started in a small kitchen with a dream and a family recipe book. Our founder, Maria, inherited her grandmother's passion for baking and decided to share that love with the world.\n\nWhat began as weekend baking sessions for friends and family quickly grew into a beloved community bakery. Every recipe was tested, every ingredient carefully selected, and every customer treated like family.\n\nToday, we continue that tradition of excellence, using the same time-honored techniques and the finest ingredients to create unforgettable bakery experiences.",
            'years_experience' => '10+',
            'happy_customers' => '1000+',
            
            'values' => [
                [
                    'id' => 1,
                    'icon' => 'fas fa-heart',
                    'title' => 'Made with Love',
                    'description' => 'Every item is crafted with passion and attention to detail, just like our grandmothers taught us.',
                    'is_active' => true,
                    'sort_order' => 1
                ],
                [
                    'id' => 2,
                    'icon' => 'fas fa-leaf',
                    'title' => 'Quality Ingredients',
                    'description' => 'We source only the finest, freshest ingredients to ensure every bite is a delight.',
                    'is_active' => true,
                    'sort_order' => 2
                ],
                [
                    'id' => 3,
                    'icon' => 'fas fa-users',
                    'title' => 'Community First',
                    'description' => 'We\'re not just a bakery, we\'re part of the community, celebrating life\'s sweet moments together.',
                    'is_active' => true,
                    'sort_order' => 3
                ]
            ],
            
            'team_members' => [
                [
                    'id' => 1,
                    'name' => 'Maria Rodriguez',
                    'designation' => 'Founder & Head Baker',
                    'description' => 'With over 15 years of experience, Maria brings traditional recipes to life with modern techniques.',
                    'image' => 'team-members/maria.jpg',
                    'is_active' => true,
                    'sort_order' => 1
                ],
                [
                    'id' => 2,
                    'name' => 'David Chen',
                    'designation' => 'Operations Manager',
                    'description' => 'David ensures every customer receives the perfect experience from order to delivery.',
                    'image' => 'team-members/david.jpg',
                    'is_active' => true,
                    'sort_order' => 2
                ],
                [
                    'id' => 3,
                    'name' => 'Sarah Johnson',
                    'designation' => 'Pastry Chef',
                    'description' => 'Sarah\'s creative flair brings unique and beautiful designs to our custom cakes and pastries.',
                    'image' => 'team-members/sarah.jpg',
                    'is_active' => true,
                    'sort_order' => 3
                ]
            ],
            
            'cta_section_color' => '#000000',
            'cta_title' => 'Ready to Taste the Difference?',
            'cta_subtitle' => 'Experience the love and tradition in every bite',
            'cta_button_text' => 'Shop Our Products',
            'cta_button_link' => '/products',
            'cta_button_color' => '#FF6B00',
        ]);
    }
}