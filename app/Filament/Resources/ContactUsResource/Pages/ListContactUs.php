<?php

namespace App\Filament\Resources\ContactUsResource\Pages;

use App\Filament\Resources\ContactUsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\ContactUs;

class ListContactUs extends ListRecords
{
    protected static string $resource = ContactUsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => ContactUs::count() === 0),
        ];
    }

    public function mount(): void
    {
        parent::mount();

        // If no record exists, create one and redirect to edit
        if (ContactUs::count() === 0) {
            $contactUs = ContactUs::create([
                'get_in_touch_title' => 'Get in Touch',
                'get_in_touch_quote' => 'Have a question, special request, or just want to say hello? We\'d love to hear from you!',
                'contact_address' => "123 Bakery Street\nSweet District\nCity, State 12345",
                'contact_phone' => '+1 (555) 123-4567',
                'contact_email' => 'contact@bakery.com',
                'business_hours' => [
                    ['day' => 'Monday - Friday', 'hours' => '8:00 AM - 6:00 PM'],
                    ['day' => 'Saturday - Sunday', 'hours' => '9:00 AM - 5:00 PM'],
                ],
                'social_media_links' => [],
                'faqs' => [
                    [
                        'question' => 'What are your delivery options?',
                        'answer' => 'We offer same-day delivery for orders placed before 2 PM within a 10-mile radius. We also provide pickup options and nationwide shipping for select items.',
                        'is_active' => true,
                    ],
                    [
                        'question' => 'Do you accept custom cake orders?',
                        'answer' => 'Yes! We love creating custom cakes for special occasions. Please place your order at least 48 hours in advance for custom designs.',
                        'is_active' => true,
                    ],
                    [
                        'question' => 'Are your products suitable for people with allergies?',
                        'answer' => 'We offer gluten-free, dairy-free, and nut-free options. Please inform us about any allergies when placing your order, and we\'ll ensure proper handling.',
                        'is_active' => true,
                    ],
                    [
                        'question' => 'Can I cancel or modify my order?',
                        'answer' => 'Orders can be modified or cancelled up to 24 hours before the scheduled delivery or pickup time. Custom orders may have different cancellation policies.',
                        'is_active' => true,
                    ],
                    [
                        'question' => 'Do you offer catering services for events?',
                        'answer' => 'Yes, we provide catering services for weddings, corporate events, and parties. Contact us for a custom quote based on your requirements.',
                        'is_active' => true,
                    ],
                ],
            ]);

            $this->redirect(ContactUsResource::getUrl('edit', ['record' => $contactUs]));
        }
        // If one record exists, redirect to edit it
        elseif (ContactUs::count() === 1) {
            $this->redirect(ContactUsResource::getUrl('edit', ['record' => ContactUs::first()]));
        }
    }
}