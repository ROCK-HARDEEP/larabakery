<?php

namespace App\Filament\Pages;

use App\Models\PageSection;
use App\Models\AboutUs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class ManageHowItBegan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = null;
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.pages.manage-single-section';
    
    protected static ?string $title = 'Manage How It All Began';

    public ?array $data = [];
    
    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.pages.about-page') => 'About Page',
            url()->current() => 'How It All Began',
        ];
    }

    public function mount(): void
    {
        $section = PageSection::getOrCreate('about', 'how_it_began', [
            'title' => 'How It All Began',
            'content' => 'From a small family kitchen to a beloved neighborhood bakery, discover our journey of passion, dedication, and delicious creations.',
            'data' => [
                'years_experience' => '10+',
                'happy_customers' => '1000+',
                'subtitle' => 'A journey of passion, tradition, and community'
            ]
        ]);
        
        // Get existing AboutUs data if available
        $aboutUs = AboutUs::first();
        if ($aboutUs && (!isset($section->data['years_experience']) || !isset($section->data['happy_customers']))) {
            $data = $section->data ?? [];
            $data['years_experience'] = $aboutUs->years_experience ?? '10+';
            $data['happy_customers'] = $aboutUs->happy_customers ?? '1000+';
            $section->data = $data;
            $section->save();
        }

        $this->form->fill([
            'data.title' => $section->title,
            'data.subtitle' => $section->data['subtitle'] ?? 'A journey of passion, tradition, and community',
            'data.content' => $section->content,
            'data.years_experience' => $section->data['years_experience'] ?? '10+',
            'data.happy_customers' => $section->data['happy_customers'] ?? '1000+',
            'data.is_active' => $section->is_active,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('How It All Began')
                ->description('Share the origins and achievements of your bakery')
                ->schema([
                    Forms\Components\TextInput::make('data.title')
                        ->label('Section Title')
                        ->required(),
                    Forms\Components\TextInput::make('data.subtitle')
                        ->label('Section Subtitle/Quote')
                        ->placeholder('A journey of passion, tradition, and community')
                        ->columnSpanFull(),
                    Forms\Components\RichEditor::make('data.content')
                        ->label('The Beginning Story')
                        ->required()
                        ->columnSpanFull()
                        ->toolbarButtons([
                            'bold',
                            'italic',
                            'underline',
                            'h2',
                            'h3',
                            'bulletList',
                            'orderedList',
                            'link',
                            'redo',
                            'undo',
                        ]),
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('data.years_experience')
                                ->label('Years of Experience')
                                ->placeholder('10+')
                                ->helperText('e.g., 10+, 15+, 20 Years')
                                ->required(),
                            Forms\Components\TextInput::make('data.happy_customers')
                                ->label('Happy Customers')
                                ->placeholder('1000+')
                                ->helperText('e.g., 1000+, 5K+, 10,000+')
                                ->required(),
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
            ['page' => 'about', 'section' => 'how_it_began'],
            [
                'title' => $formData['data']['title'],
                'content' => $formData['data']['content'],
                'data' => [
                    'subtitle' => $formData['data']['subtitle'],
                    'years_experience' => $formData['data']['years_experience'],
                    'happy_customers' => $formData['data']['happy_customers'],
                ],
                'is_active' => $formData['data']['is_active'],
            ]
        );
        
        // Also update AboutUs model for backward compatibility
        $aboutUs = AboutUs::first();
        if ($aboutUs) {
            $aboutUs->years_experience = $formData['data']['years_experience'];
            $aboutUs->happy_customers = $formData['data']['happy_customers'];
            $aboutUs->began_quote = $formData['data']['subtitle'];
            $aboutUs->save();
        }

        Notification::make()
            ->title('How It All Began section saved successfully')
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