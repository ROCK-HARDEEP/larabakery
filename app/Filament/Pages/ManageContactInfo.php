<?php

namespace App\Filament\Pages;

use App\Models\PageSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class ManageContactInfo extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-phone';
    protected static ?string $navigationGroup = null;
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.pages.manage-single-section';
    
    protected static ?string $title = 'Manage Contact Information';

    public ?array $data = [];
    
    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.pages.contact-page') => 'Contact Page',
            url()->current() => 'Contact Information',
        ];
    }

    public function mount(): void
    {
        $section = PageSection::getOrCreate('contact', 'contact_info', [
            'title' => 'Contact Information',
            'content' => 'Reach out to us through any of these channels',
            'data' => [
                'address' => '123 Baker Street, Sweet City, SC 12345',
                'phone' => '+1 (555) 123-4567',
                'email' => 'info@bakery.com',
                'whatsapp' => '+1 (555) 123-4567',
            ]
        ]);

        $this->form->fill([
            'data.title' => $section->title,
            'data.content' => $section->content,
            'data.address' => $section->data['address'] ?? '',
            'data.phone' => $section->data['phone'] ?? '',
            'data.email' => $section->data['email'] ?? '',
            'data.whatsapp' => $section->data['whatsapp'] ?? '',
            'data.is_active' => $section->is_active,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Contact Information')
                ->description('Your business contact details')
                ->schema([
                    Forms\Components\TextInput::make('data.title')
                        ->label('Section Title')
                        ->required(),
                    Forms\Components\Textarea::make('data.content')
                        ->label('Introduction Text')
                        ->rows(2),
                    Forms\Components\TextInput::make('data.address')
                        ->label('Business Address')
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('data.phone')
                                ->label('Phone Number')
                                ->tel()
                                ->required(),
                            Forms\Components\TextInput::make('data.email')
                                ->label('Email Address')
                                ->email()
                                ->required(),
                        ]),
                    Forms\Components\TextInput::make('data.whatsapp')
                        ->label('WhatsApp Number')
                        ->tel()
                        ->helperText('Optional: WhatsApp number for customer inquiries'),
                    Forms\Components\Toggle::make('data.is_active')
                        ->label('Section Active')
                        ->default(true),
                ]),
        ];
    }

    public function save(): void
    {
        $formData = $this->form->getState();

        PageSection::updateOrCreate(
            ['page' => 'contact', 'section' => 'contact_info'],
            [
                'title' => $formData['data']['title'],
                'content' => $formData['data']['content'],
                'data' => [
                    'address' => $formData['data']['address'],
                    'phone' => $formData['data']['phone'],
                    'email' => $formData['data']['email'],
                    'whatsapp' => $formData['data']['whatsapp'],
                ],
                'is_active' => $formData['data']['is_active'],
            ]
        );

        Notification::make()
            ->title('Contact information saved successfully')
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