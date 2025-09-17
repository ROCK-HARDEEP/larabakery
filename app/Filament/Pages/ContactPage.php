<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ContactPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Pages';
    protected static ?string $navigationLabel = 'Contact Page';
    protected static ?int $navigationSort = 4;
    protected static bool $shouldRegisterNavigation = true;
    protected static string $view = 'filament.pages.contact-page';
    
    public function getBreadcrumbs(): array
    {
        return [
            url()->current() => 'Contact Page',
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
        $contactUs = \App\Models\ContactUs::first();
        $editUrl = $contactUs ? route('filament.admin.resources.contactuses.edit', $contactUs->id) : route('filament.admin.resources.contactuses.create');

        return [
            [
                'title' => 'Get In Touch',
                'description' => 'Welcome section with hero image and call-to-action',
                'icon' => 'heroicon-o-chat-bubble-left-right',
                'items_count' => 1,
                'active_count' => $contactUs && $contactUs->get_in_touch_title ? 1 : 0,
                'manage_url' => $editUrl . '?activeTab=Hero%20Section',
                'status' => $contactUs && $contactUs->get_in_touch_title ? 'configured' : 'empty',
            ],
            [
                'title' => 'Contact Information',
                'description' => 'Address, phone, email, and business hours',
                'icon' => 'heroicon-o-phone',
                'items_count' => 1,
                'active_count' => $contactUs && ($contactUs->contact_phone || $contactUs->contact_email) ? 1 : 0,
                'manage_url' => $editUrl . '?activeTab=Contact%20Info',
                'status' => $contactUs && ($contactUs->contact_phone || $contactUs->contact_email) ? 'configured' : 'empty',
            ],
            [
                'title' => 'Social Media',
                'description' => 'Social media profiles and links',
                'icon' => 'heroicon-o-share',
                'items_count' => $contactUs && is_array($contactUs->social_media_links) ? count($contactUs->social_media_links) : 0,
                'active_count' => $contactUs && is_array($contactUs->social_media_links) ? count($contactUs->social_media_links) : 0,
                'manage_url' => $editUrl . '?activeTab=Social%20Media',
                'status' => $contactUs && is_array($contactUs->social_media_links) && count($contactUs->social_media_links) > 0 ? 'configured' : 'empty',
            ],
            [
                'title' => 'Location Map',
                'description' => 'Interactive map showing your bakery location',
                'icon' => 'heroicon-o-map-pin',
                'items_count' => 1,
                'active_count' => $contactUs && ($contactUs->map_latitude && $contactUs->map_longitude) ? 1 : 0,
                'manage_url' => $editUrl . '?activeTab=Location',
                'status' => $contactUs && ($contactUs->map_latitude && $contactUs->map_longitude) ? 'configured' : 'empty',
            ],
            [
                'title' => 'FAQs',
                'description' => 'Frequently asked questions displayed at bottom of contact page',
                'icon' => 'heroicon-o-question-mark-circle',
                'items_count' => $contactUs && is_array($contactUs->faqs) ? count($contactUs->faqs) : 0,
                'active_count' => $contactUs && is_array($contactUs->faqs) ? count(array_filter($contactUs->faqs, fn($faq) => $faq['is_active'] ?? false)) : 0,
                'manage_url' => $editUrl . '?activeTab=FAQs',
                'status' => $contactUs && is_array($contactUs->faqs) && count($contactUs->faqs) > 0 ? 'configured' : 'empty',
            ],
        ];
    }
}