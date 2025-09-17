<?php

namespace App\Filament\Pages;

use App\Models\PageSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class ManageContactForm extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = null;
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.pages.manage-single-section';
    
    protected static ?string $title = 'Manage Get In Touch';

    public ?array $data = [];
    
    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.pages.contact-page') => 'Contact Page',
            url()->current() => 'Get In Touch',
        ];
    }

    public function mount(): void
    {
        $section = PageSection::getOrCreate('contact', 'contact_form', [
            'title' => 'Get In Touch',
            'content' => 'We would love to hear from you. Feel free to reach out to us anytime!',
            'data' => [
                'button_text' => '',
                'button_link' => '/contact',
            ]
        ]);

        $this->form->fill([
            'data.title' => $section->title,
            'data.content' => $section->content,
            'data.image' => $section->image ? (is_array($section->image) ? $section->image : [$section->image]) : [],
            'data.button_text' => $section->data['button_text'] ?? '',
            'data.is_active' => $section->is_active,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Get In Touch Section')
                ->description('Configure the Get In Touch section')
                ->schema([
                    Forms\Components\TextInput::make('data.title')
                        ->label('Title')
                        ->required()
                        ->placeholder('Get In Touch'),
                    Forms\Components\Textarea::make('data.content')
                        ->label('Description')
                        ->rows(3)
                        ->required()
                        ->placeholder('We would love to hear from you...'),
                    Forms\Components\FileUpload::make('data.image')
                        ->label('Section Image')
                        ->image()
                        ->disk('public')
                        ->directory('contact')
                        ->visibility('public')
                        ->imagePreviewHeight('200')
                        ->helperText('Background or feature image for this section')
                            ->optimizeToWebP(),
                    Forms\Components\TextInput::make('data.button_text')
                        ->label('Button Text (Optional)')
                        ->placeholder('Contact Us Now')
                        ->helperText('Leave empty to hide button'),
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
            ['page' => 'contact', 'section' => 'contact_form'],
            [
                'title' => $formData['data']['title'],
                'content' => $formData['data']['content'],
                'image' => is_array($formData['data']['image']) ? ($formData['data']['image'][0] ?? null) : $formData['data']['image'],
                'data' => [
                    'button_text' => $formData['data']['button_text'] ?? '',
                    'button_link' => $formData['data']['button_text'] ? '/contact' : '',
                ],
                'is_active' => $formData['data']['is_active'],
            ]
        );

        Notification::make()
            ->title('Get In Touch section saved successfully')
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