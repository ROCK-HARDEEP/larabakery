<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AboutPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-information-circle';
    protected static ?string $navigationGroup = 'Pages';
    protected static ?string $navigationLabel = 'About Page';
    protected static ?int $navigationSort = 3;
    protected static bool $shouldRegisterNavigation = true;
    protected static string $view = 'filament.pages.about-page';
    
    public function getBreadcrumbs(): array
    {
        return [
            url()->current() => 'About Page',
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
        $aboutUs = \App\Models\AboutUs::first();
        $editUrl = $aboutUs ? route('filament.admin.resources.aboutuses.edit', $aboutUs->id) : route('filament.admin.resources.aboutuses.create');

        return [
            [
                'title' => 'Hero Section',
                'description' => 'Main banner and introduction displayed at the top of About page',
                'icon' => 'heroicon-o-photo',
                'items_count' => 1,
                'active_count' => $aboutUs && $aboutUs->story_title ? 1 : 0,
                'manage_url' => $editUrl . '?activeTab=Hero%20Section',
                'status' => $aboutUs && $aboutUs->story_title ? 'configured' : 'empty',
            ],
            [
                'title' => 'Our Story',
                'description' => 'Tell your bakery\'s unique story and journey',
                'icon' => 'heroicon-o-book-open',
                'items_count' => 1,
                'active_count' => $aboutUs && $aboutUs->began_content ? 1 : 0,
                'manage_url' => $editUrl . '?activeTab=Our%20Story',
                'status' => $aboutUs && $aboutUs->began_content ? 'configured' : 'empty',
            ],
            [
                'title' => 'Our Values',
                'description' => 'Core values and principles that guide your business',
                'icon' => 'heroicon-o-heart',
                'items_count' => $aboutUs && is_array($aboutUs->values) ? count($aboutUs->values) : 0,
                'active_count' => $aboutUs && is_array($aboutUs->values) ? count($aboutUs->values) : 0,
                'manage_url' => $editUrl . '?activeTab=Our%20Values',
                'status' => $aboutUs && is_array($aboutUs->values) && count($aboutUs->values) > 0 ? 'configured' : 'empty',
            ],
            [
                'title' => 'Meet Our Team',
                'description' => 'Showcase your talented team members',
                'icon' => 'heroicon-o-user-group',
                'items_count' => $aboutUs && is_array($aboutUs->team_members) ? count($aboutUs->team_members) : 0,
                'active_count' => $aboutUs && is_array($aboutUs->team_members) ? count($aboutUs->team_members) : 0,
                'manage_url' => $editUrl . '?activeTab=Meet%20Our%20Team',
                'status' => $aboutUs && is_array($aboutUs->team_members) && count($aboutUs->team_members) > 0 ? 'configured' : 'empty',
            ],
            [
                'title' => 'Call to Action',
                'description' => 'Bottom section with call-to-action to encourage visitors',
                'icon' => 'heroicon-o-sparkles',
                'items_count' => 1,
                'active_count' => $aboutUs && $aboutUs->cta_title ? 1 : 0,
                'manage_url' => $editUrl . '?activeTab=Call%20to%20Action',
                'status' => $aboutUs && $aboutUs->cta_title ? 'configured' : 'empty',
            ],
        ];
    }
}