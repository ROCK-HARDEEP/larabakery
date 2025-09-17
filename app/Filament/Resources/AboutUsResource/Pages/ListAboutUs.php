<?php

namespace App\Filament\Resources\AboutUsResource\Pages;

use App\Filament\Resources\AboutUsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\AboutUs;

class ListAboutUs extends ListRecords
{
    protected static string $resource = AboutUsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => AboutUs::count() === 0),
        ];
    }

    public function mount(): void
    {
        parent::mount();

        // If no record exists, create one and redirect to edit
        if (AboutUs::count() === 0) {
            $aboutUs = AboutUs::create([
                'story_title' => 'About Us',
                'story_content' => 'Discover our passion for baking',
                'began_title' => 'How It All Began',
                'began_quote' => 'Every great bakery has a story',
                'began_content' => 'Our journey started with a simple dream...',
                'years_experience' => '25',
                'happy_customers' => '50K+',
                'cta_title' => 'Ready to Taste the Difference?',
                'cta_subtitle' => 'Visit our bakery today and experience the magic of fresh, handcrafted baked goods',
                'cta_button_text' => 'Visit Us Today',
                'cta_button_link' => '/contact',
                'cta_section_color' => '#000000',
                'cta_button_color' => '#F69D1C',
                'values' => [],
                'team_members' => [],
            ]);

            $this->redirect(AboutUsResource::getUrl('edit', ['record' => $aboutUs]));
        }
        // If one record exists, redirect to edit it
        elseif (AboutUs::count() === 1) {
            $this->redirect(AboutUsResource::getUrl('edit', ['record' => AboutUs::first()]));
        }
    }
}