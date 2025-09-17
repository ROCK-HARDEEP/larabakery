<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class HomePage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationGroup = 'Pages';
    protected static ?string $navigationLabel = 'Home Page';
    protected static ?int $navigationSort = 2;
    protected static bool $shouldRegisterNavigation = true;
    protected static string $view = 'filament.pages.home-page';
    
    public function getBreadcrumbs(): array
    {
        return [
            url()->current() => 'Home Page',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('manage_custom_sections')
                ->label('Manage Custom Sections')
                ->icon('heroicon-o-squares-plus')
                ->url('#'),
        ];
    }

    public function getSections(): array
    {
        return [
            [
                'title' => 'Hero Slides',
                'description' => 'Manage banner slides that appear at the top of your homepage',
                'icon' => 'heroicon-o-photo',
                'items_count' => \App\Models\HeroSlide::count(),
                'active_count' => \App\Models\HeroSlide::where('is_active', true)->count(),
                'manage_url' => \App\Filament\Resources\HeroSlideResource::getUrl(),
            ],
            [
                'title' => 'Shop by Category',
                'description' => 'Manage categories displayed in the Shop by Category section',
                'icon' => 'heroicon-o-tag',
                'items_count' => \App\Models\HomePageCategory::count(),
                'active_count' => \App\Models\HomePageCategory::where('is_active', true)->count(),
                'manage_url' => \App\Filament\Resources\HomePageCategoryResource::getUrl(),
            ],
            [
                'title' => 'Popular Products',
                'description' => 'Manage products featured in the Popular Products section',
                'icon' => 'heroicon-o-star',
                'items_count' => \App\Models\PopularProduct::count(),
                'active_count' => \App\Models\PopularProduct::where('is_active', true)->count(),
                'manage_url' => \App\Filament\Resources\PopularProductResource::getUrl(),
            ],
            [
                'title' => 'Why Choose Us',
                'description' => 'Configure trust factors and features that make your store unique',
                'icon' => 'heroicon-o-light-bulb',
                'items_count' => \App\Models\WhyChooseUs::count(),
                'active_count' => \App\Models\WhyChooseUs::where('is_active', true)->count(),
                'manage_url' => \App\Filament\Resources\WhyChooseUsResource::getUrl(),
            ],
            [
                'title' => 'Blogs',
                'description' => 'Manage blog posts to share updates, recipes, and bakery news',
                'icon' => 'heroicon-o-document-text',
                'items_count' => \App\Models\Blog::count(),
                'active_count' => \App\Models\Blog::active()->count(),
                'manage_url' => \App\Filament\Resources\BlogResource::getUrl(),
            ],
            [
                'title' => 'Testimonials', 
                'description' => 'Manage customer testimonials and reviews',
                'icon' => 'heroicon-o-chat-bubble-left-ellipsis',
                'items_count' => \App\Models\Testimonial::count(),
                'active_count' => \App\Models\Testimonial::active()->count(),
                'manage_url' => \App\Filament\Resources\TestimonialResource::getUrl(),
            ],
            [
                'title' => 'FAQs',
                'description' => 'Manage frequently asked questions about your bakery and services',
                'icon' => 'heroicon-o-question-mark-circle',
                'items_count' => \App\Models\HomepageFaq::count(),
                'active_count' => \App\Models\HomepageFaq::where('is_active', true)->count(),
                'manage_url' => \App\Filament\Resources\HomepageFaqResource::getUrl(),
            ],
        ];
    }
}