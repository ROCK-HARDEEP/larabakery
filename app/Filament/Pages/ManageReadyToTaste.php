<?php

namespace App\Filament\Pages;

use App\Models\PageSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class ManageReadyToTaste extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = null;
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.pages.manage-single-section';
    
    protected static ?string $title = 'Manage Ready to Taste CTA';

    public ?array $data = [];
    
    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.pages.about-page') => 'About Page',
            url()->current() => 'Ready to Taste',
        ];
    }

    public function mount(): void
    {
        $section = PageSection::getOrCreate('about', 'ready_to_taste', [
            'title' => 'Ready to Taste the Difference?',
            'content' => 'Visit us today and experience the magic of freshly baked goods made with love and passion.',
            'data' => [
                'button_text' => 'Visit Our Bakery',
                'button_link' => '/contact',
                'background_color' => '#000000',
                'button_color' => '#FF6B00',
            ]
        ]);

        $this->form->fill([
            'data.title' => $section->title,
            'data.content' => $section->content,
            'data.button_text' => $section->data['button_text'] ?? '',
            'data.background_color' => $section->data['background_color'] ?? '#000000',
            'data.button_color' => $section->data['button_color'] ?? '#FF6B00',
            'data.image' => $section->image ? (is_array($section->image) ? $section->image : [$section->image]) : [],
            'data.is_active' => $section->is_active,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Call to Action Section')
                ->description('Encourage visitors to take action')
                ->schema([
                    Forms\Components\TextInput::make('data.title')
                        ->label('Title')
                        ->required()
                        ->placeholder('Ready to Taste the Difference?'),
                    Forms\Components\Textarea::make('data.content')
                        ->label('Subtitle')
                        ->rows(2)
                        ->required()
                        ->placeholder('Visit us today and experience the magic'),
                    Forms\Components\Section::make('Background')
                        ->description('Choose either a color or image for the background')
                        ->schema([
                            Forms\Components\ColorPicker::make('data.background_color')
                                ->label('Background Color')
                                ->default('#000000')
                                ->helperText('Used when no image is uploaded'),
                            Forms\Components\FileUpload::make('data.image')
                                ->label('Background Image (Optional)')
                                ->image()
                                ->disk('public')
                                ->directory('about')
                                ->visibility('public')
                                ->imagePreviewHeight('150')
                                ->helperText('If uploaded, will override the background color'),
                        ])
                        ->columns(2)
                        ->collapsible(),
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('data.button_text')
                                ->label('Button Text (Optional)')
                                ->placeholder('Visit Our Bakery')
                                ->helperText('Leave empty to hide button'),
                            Forms\Components\ColorPicker::make('data.button_color')
                                ->label('Button Color')
                                ->default('#FF6B00')
                                ->helperText('Color of the button'),
                        ]),
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
            ['page' => 'about', 'section' => 'ready_to_taste'],
            [
                'title' => $formData['data']['title'],
                'content' => $formData['data']['content'],
                'image' => is_array($formData['data']['image']) ? ($formData['data']['image'][0] ?? null) : $formData['data']['image'],
                'data' => [
                    'button_text' => $formData['data']['button_text'] ?? '',
                    'button_link' => $formData['data']['button_text'] ? '/contact' : '',
                    'background_color' => $formData['data']['background_color'] ?? '#000000',
                    'button_color' => $formData['data']['button_color'] ?? '#FF6B00',
                ],
                'is_active' => $formData['data']['is_active'],
            ]
        );
        
        // Also update AboutUs model for backward compatibility
        $aboutUs = \App\Models\AboutUs::first();
        if ($aboutUs) {
            $aboutUs->cta_title = $formData['data']['title'];
            $aboutUs->cta_subtitle = $formData['data']['content'];
            $aboutUs->cta_button_text = $formData['data']['button_text'] ?? 'Shop Our Products';
            $aboutUs->cta_button_link = $formData['data']['button_text'] ? '/contact' : '/products';
            $aboutUs->cta_section_color = $formData['data']['background_color'] ?? '#000000';
            $aboutUs->cta_button_color = $formData['data']['button_color'] ?? '#FF6B00';
            $aboutUs->save();
        }

        Notification::make()
            ->title('Call to Action section saved successfully')
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