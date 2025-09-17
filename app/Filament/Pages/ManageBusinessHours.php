<?php

namespace App\Filament\Pages;

use App\Models\PageSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class ManageBusinessHours extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = null;
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.pages.manage-single-section';
    
    protected static ?string $title = 'Manage Business Hours';

    public ?array $data = [];
    
    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.pages.contact-page') => 'Contact Page',
            url()->current() => 'Business Hours',
        ];
    }

    public function mount(): void
    {
        $section = PageSection::getOrCreate('contact', 'business_hours', [
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

        $this->form->fill([
            'data.title' => $section->title,
            'data.content' => $section->content,
            'data.hours' => $section->data['hours'] ?? [],
            'data.special_note' => $section->data['special_note'] ?? '',
            'data.is_active' => $section->is_active,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Business Hours')
                ->description('Set your operating hours')
                ->schema([
                    Forms\Components\TextInput::make('data.title')
                        ->label('Section Title')
                        ->required(),
                    Forms\Components\Textarea::make('data.content')
                        ->label('Introduction Text')
                        ->rows(2),
                    Forms\Components\Repeater::make('data.hours')
                        ->label('Operating Hours')
                        ->schema([
                            Forms\Components\TextInput::make('day')
                                ->label('Day')
                                ->required()
                                ->disabled(),
                            Forms\Components\TimePicker::make('open')
                                ->label('Opening Time')
                                ->required()
                                ->seconds(false),
                            Forms\Components\TimePicker::make('close')
                                ->label('Closing Time')
                                ->required()
                                ->seconds(false),
                        ])
                        ->columns(3)
                        ->defaultItems(7)
                        ->minItems(7)
                        ->maxItems(7)
                        ->reorderable(false)
                        ->deletable(false)
                        ->addable(false),
                    Forms\Components\TextInput::make('data.special_note')
                        ->label('Special Note')
                        ->placeholder('e.g., Closed on major holidays')
                        ->helperText('Any special notes about holidays or exceptions'),
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
            ['page' => 'contact', 'section' => 'business_hours'],
            [
                'title' => $formData['data']['title'],
                'content' => $formData['data']['content'],
                'data' => [
                    'hours' => $formData['data']['hours'],
                    'special_note' => $formData['data']['special_note'],
                ],
                'is_active' => $formData['data']['is_active'],
            ]
        );

        Notification::make()
            ->title('Business hours saved successfully')
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