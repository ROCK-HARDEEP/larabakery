<?php

namespace App\Filament\Pages;

use App\Models\PageSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class ManageLocationMap extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = null;
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.pages.manage-single-section';
    
    protected static ?string $title = 'Manage Location Map';

    public ?array $data = [];
    
    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.pages.contact-page') => 'Contact Page',
            url()->current() => 'Location Map',
        ];
    }

    public function mount(): void
    {
        $section = PageSection::getOrCreate('contact', 'location_map', [
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
            'data.address' => $section->data['address'] ?? '123 Baker Street, Sweet City, SC 12345',
            'data.latitude' => $section->data['latitude'] ?? '',
            'data.longitude' => $section->data['longitude'] ?? '',
            'data.is_active' => $section->is_active,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Location Map')
                ->description('Set your bakery location')
                ->schema([
                    Forms\Components\TextInput::make('data.address')
                        ->label('Bakery Address')
                        ->placeholder('123 Baker Street, Sweet City, SC 12345')
                        ->columnSpanFull()
                        ->required()
                        ->helperText('Enter your full address'),
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('data.latitude')
                                ->label('Latitude')
                                ->numeric()
                                ->required()
                                ->placeholder('40.7128'),
                            Forms\Components\TextInput::make('data.longitude')
                                ->label('Longitude')
                                ->numeric()
                                ->required()
                                ->placeholder('-74.0060'),
                        ]),
                    Forms\Components\Actions::make([
                        Forms\Components\Actions\Action::make('use_current_location')
                            ->label('Use Current Location')
                            ->icon('heroicon-o-map-pin')
                            ->action(function ($set) {
                                // This will be handled by JavaScript
                                $this->dispatch('get-current-location');
                            }),
                        Forms\Components\Actions\Action::make('lookup_address')
                            ->label('Lookup Address on Map')
                            ->icon('heroicon-o-magnifying-glass')
                            ->action(function ($get, $set) {
                                $address = $get('data.address');
                                if ($address) {
                                    // Geocode the address
                                    $this->dispatch('geocode-address', address: $address);
                                }
                            }),
                    ])->columnSpanFull(),
                    Forms\Components\View::make('filament.components.map-preview')
                        ->columnSpanFull(),
                    Forms\Components\Toggle::make('data.is_active')
                        ->label('Show Map on Contact Page')
                        ->default(true),
                ]),
        ];
    }

    public function save(): void
    {
        $formData = $this->form->getState();

        PageSection::updateOrCreate(
            ['page' => 'contact', 'section' => 'location_map'],
            [
                'title' => 'Find Us',
                'content' => 'Visit our bakery',
                'data' => [
                    'address' => $formData['data']['address'],
                    'latitude' => $formData['data']['latitude'],
                    'longitude' => $formData['data']['longitude'],
                    'zoom' => 15,
                    'map_style' => 'streets',
                ],
                'is_active' => $formData['data']['is_active'],
            ]
        );

        Notification::make()
            ->title('Location map saved successfully')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('Save Changes')
                ->submit('save'),
        ];
    }
}