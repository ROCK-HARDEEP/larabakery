<?php

namespace App\Filament\Pages;

use App\Models\PageSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class ManageContactSections extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = null;
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.pages.manage-contact-sections';

    public ?array $contact_form = [];
    public ?array $contact_info = [];
    public ?array $business_hours = [];
    public ?array $location_map = [];

    public function mount(): void
    {
        $this->loadSections();
    }

    protected function loadSections(): void
    {
        $contactForm = PageSection::getOrCreate('contact', 'contact_form', [
            'title' => 'Get In Touch',
            'content' => 'Send us a message and we\'ll get back to you soon!',
            'data' => [
                'email_to' => 'info@bakery.com',
                'success_message' => 'Thank you for contacting us! We\'ll get back to you soon.',
            ]
        ]);
        
        $contactInfo = PageSection::getOrCreate('contact', 'contact_info', [
            'title' => 'Contact Information',
            'content' => 'Reach out to us through any of these channels',
            'data' => [
                'address' => '123 Baker Street, Sweet City, SC 12345',
                'phone' => '+1 (555) 123-4567',
                'email' => 'info@bakery.com',
                'whatsapp' => '+1 (555) 123-4567',
            ]
        ]);
        
        $businessHours = PageSection::getOrCreate('contact', 'business_hours', [
            'title' => 'Business Hours',
            'content' => 'We\'re here to serve you',
            'data' => [
                'hours' => [
                    ['day' => 'Monday', 'open' => '07:00', 'close' => '19:00'],
                    ['day' => 'Tuesday', 'open' => '07:00', 'close' => '19:00'],
                    ['day' => 'Wednesday', 'open' => '07:00', 'close' => '19:00'],
                    ['day' => 'Thursday', 'open' => '07:00', 'close' => '19:00'],
                    ['day' => 'Friday', 'open' => '07:00', 'close' => '20:00'],
                    ['day' => 'Saturday', 'open' => '08:00', 'close' => '20:00'],
                    ['day' => 'Sunday', 'open' => '08:00', 'close' => '18:00'],
                ],
                'special_note' => 'Closed on major holidays',
            ]
        ]);
        
        $locationMap = PageSection::getOrCreate('contact', 'location_map', [
            'title' => 'Find Us',
            'content' => 'Visit our bakery',
            'data' => [
                'latitude' => '40.7128',
                'longitude' => '-74.0060',
                'zoom' => 15,
                'map_style' => 'streets',
                'google_maps_api_key' => '',
            ]
        ]);

        $this->form->fill([
            'contact_form' => [
                'title' => $contactForm->title,
                'content' => $contactForm->content,
                'email_to' => $contactForm->data['email_to'] ?? '',
                'success_message' => $contactForm->data['success_message'] ?? '',
                'is_active' => $contactForm->is_active,
            ],
            'contact_info' => [
                'title' => $contactInfo->title,
                'content' => $contactInfo->content,
                'address' => $contactInfo->data['address'] ?? '',
                'phone' => $contactInfo->data['phone'] ?? '',
                'email' => $contactInfo->data['email'] ?? '',
                'whatsapp' => $contactInfo->data['whatsapp'] ?? '',
                'is_active' => $contactInfo->is_active,
            ],
            'business_hours' => [
                'title' => $businessHours->title,
                'content' => $businessHours->content,
                'hours' => $businessHours->data['hours'] ?? [],
                'special_note' => $businessHours->data['special_note'] ?? '',
                'is_active' => $businessHours->is_active,
            ],
            'location_map' => [
                'title' => $locationMap->title,
                'content' => $locationMap->content,
                'latitude' => $locationMap->data['latitude'] ?? '',
                'longitude' => $locationMap->data['longitude'] ?? '',
                'zoom' => $locationMap->data['zoom'] ?? 15,
                'map_style' => $locationMap->data['map_style'] ?? 'streets',
                'google_maps_api_key' => $locationMap->data['google_maps_api_key'] ?? '',
                'is_active' => $locationMap->is_active,
            ],
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Tabs::make('Contact Sections')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('Contact Form')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->schema([
                            Forms\Components\TextInput::make('contact_form.title')
                                ->label('Form Section Title')
                                ->required(),
                            Forms\Components\Textarea::make('contact_form.content')
                                ->label('Form Introduction Text')
                                ->rows(3),
                            Forms\Components\TextInput::make('contact_form.email_to')
                                ->label('Send Emails To')
                                ->email()
                                ->required(),
                            Forms\Components\Textarea::make('contact_form.success_message')
                                ->label('Success Message')
                                ->rows(2)
                                ->required(),
                            Forms\Components\Toggle::make('contact_form.is_active')
                                ->label('Form Active')
                                ->default(true),
                        ]),
                    
                    Forms\Components\Tabs\Tab::make('Contact Information')
                        ->icon('heroicon-o-phone')
                        ->schema([
                            Forms\Components\TextInput::make('contact_info.title')
                                ->label('Section Title')
                                ->required(),
                            Forms\Components\Textarea::make('contact_info.content')
                                ->label('Introduction Text')
                                ->rows(2),
                            Forms\Components\TextInput::make('contact_info.address')
                                ->label('Address')
                                ->required(),
                            Forms\Components\TextInput::make('contact_info.phone')
                                ->label('Phone Number')
                                ->tel()
                                ->required(),
                            Forms\Components\TextInput::make('contact_info.email')
                                ->label('Email Address')
                                ->email()
                                ->required(),
                            Forms\Components\TextInput::make('contact_info.whatsapp')
                                ->label('WhatsApp Number')
                                ->tel(),
                            Forms\Components\Toggle::make('contact_info.is_active')
                                ->label('Section Active')
                                ->default(true),
                        ]),
                    
                    Forms\Components\Tabs\Tab::make('Business Hours')
                        ->icon('heroicon-o-clock')
                        ->schema([
                            Forms\Components\TextInput::make('business_hours.title')
                                ->label('Section Title')
                                ->required(),
                            Forms\Components\Textarea::make('business_hours.content')
                                ->label('Introduction Text')
                                ->rows(2),
                            Forms\Components\Repeater::make('business_hours.hours')
                                ->label('Operating Hours')
                                ->schema([
                                    Forms\Components\TextInput::make('day')
                                        ->required(),
                                    Forms\Components\TimePicker::make('open')
                                        ->label('Opening Time')
                                        ->required(),
                                    Forms\Components\TimePicker::make('close')
                                        ->label('Closing Time')
                                        ->required(),
                                ])
                                ->columns(3)
                                ->defaultItems(7),
                            Forms\Components\TextInput::make('business_hours.special_note')
                                ->label('Special Note (e.g., Holiday closures)'),
                            Forms\Components\Toggle::make('business_hours.is_active')
                                ->label('Section Active')
                                ->default(true),
                        ]),
                    
                    Forms\Components\Tabs\Tab::make('Location Map')
                        ->icon('heroicon-o-map-pin')
                        ->schema([
                            Forms\Components\TextInput::make('location_map.title')
                                ->label('Section Title')
                                ->required(),
                            Forms\Components\Textarea::make('location_map.content')
                                ->label('Location Description')
                                ->rows(2),
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TextInput::make('location_map.latitude')
                                        ->label('Latitude')
                                        ->numeric()
                                        ->required(),
                                    Forms\Components\TextInput::make('location_map.longitude')
                                        ->label('Longitude')
                                        ->numeric()
                                        ->required(),
                                ]),
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TextInput::make('location_map.zoom')
                                        ->label('Zoom Level')
                                        ->numeric()
                                        ->minValue(1)
                                        ->maxValue(20)
                                        ->default(15),
                                    Forms\Components\Select::make('location_map.map_style')
                                        ->label('Map Style')
                                        ->options([
                                            'streets' => 'Streets',
                                            'satellite' => 'Satellite',
                                            'hybrid' => 'Hybrid',
                                            'terrain' => 'Terrain',
                                        ])
                                        ->default('streets'),
                                ]),
                            Forms\Components\TextInput::make('location_map.google_maps_api_key')
                                ->label('Google Maps API Key (Optional)')
                                ->password(),
                            Forms\Components\Toggle::make('location_map.is_active')
                                ->label('Map Active')
                                ->default(true),
                        ]),
                ])
                ->columnSpanFull(),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Save Contact Form
        PageSection::updateOrCreate(
            ['page' => 'contact', 'section' => 'contact_form'],
            [
                'title' => $data['contact_form']['title'],
                'content' => $data['contact_form']['content'],
                'data' => [
                    'email_to' => $data['contact_form']['email_to'],
                    'success_message' => $data['contact_form']['success_message'],
                ],
                'is_active' => $data['contact_form']['is_active'],
            ]
        );

        // Save Contact Info
        PageSection::updateOrCreate(
            ['page' => 'contact', 'section' => 'contact_info'],
            [
                'title' => $data['contact_info']['title'],
                'content' => $data['contact_info']['content'],
                'data' => [
                    'address' => $data['contact_info']['address'],
                    'phone' => $data['contact_info']['phone'],
                    'email' => $data['contact_info']['email'],
                    'whatsapp' => $data['contact_info']['whatsapp'],
                ],
                'is_active' => $data['contact_info']['is_active'],
            ]
        );

        // Save Business Hours
        PageSection::updateOrCreate(
            ['page' => 'contact', 'section' => 'business_hours'],
            [
                'title' => $data['business_hours']['title'],
                'content' => $data['business_hours']['content'],
                'data' => [
                    'hours' => $data['business_hours']['hours'],
                    'special_note' => $data['business_hours']['special_note'],
                ],
                'is_active' => $data['business_hours']['is_active'],
            ]
        );

        // Save Location Map
        PageSection::updateOrCreate(
            ['page' => 'contact', 'section' => 'location_map'],
            [
                'title' => $data['location_map']['title'],
                'content' => $data['location_map']['content'],
                'data' => [
                    'latitude' => $data['location_map']['latitude'],
                    'longitude' => $data['location_map']['longitude'],
                    'zoom' => $data['location_map']['zoom'],
                    'map_style' => $data['location_map']['map_style'],
                    'google_maps_api_key' => $data['location_map']['google_maps_api_key'],
                ],
                'is_active' => $data['location_map']['is_active'],
            ]
        );

        Notification::make()
            ->title('Contact page sections saved successfully')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('Save All Sections')
                ->submit('save'),
        ];
    }
}