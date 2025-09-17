<?php

namespace App\Filament\Pages;

use App\Models\PageSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class ManageOurStory extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = null;
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.pages.manage-single-section';
    
    protected static ?string $title = 'Manage Our Story';

    public ?array $data = [];
    
    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.pages.about-page') => 'About Page',
            url()->current() => 'Our Story',
        ];
    }

    public function mount(): void
    {
        $section = PageSection::getOrCreate('about', 'our_story', [
            'title' => 'Our Story',
            'content' => 'Every great bakery has a story. Ours began with a simple passion for creating extraordinary baked goods that bring joy to our community.',
        ]);
        
        // Check if there's existing data from AboutUs model
        $aboutUs = \App\Models\AboutUs::first();
        if ($aboutUs && $aboutUs->story_image && !$section->image) {
            $section->image = $aboutUs->story_image;
            $section->save();
        }

        $this->form->fill([
            'data.title' => $section->title,
            'data.content' => $section->content,
            'data.image' => $section->image ? (is_array($section->image) ? $section->image : [$section->image]) : [],
            'data.is_active' => $section->is_active,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Our Story')
                ->description('Tell your bakery\'s unique story')
                ->schema([
                    Forms\Components\TextInput::make('data.title')
                        ->label('Section Title')
                        ->required(),
                    Forms\Components\RichEditor::make('data.content')
                        ->label('Your Story')
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
                    Forms\Components\FileUpload::make('data.image')
                        ->label('Story Image')
                        ->image()
                        ->disk('public')
                        ->directory('about')
                        ->visibility('public')
                        ->imagePreviewHeight('200')
                        ->helperText('An image that represents your story')
                            ->optimizeToWebP(),
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
            ['page' => 'about', 'section' => 'our_story'],
            [
                'title' => $formData['data']['title'],
                'content' => $formData['data']['content'],
                'image' => is_array($formData['data']['image']) ? ($formData['data']['image'][0] ?? null) : $formData['data']['image'],
                'is_active' => $formData['data']['is_active'],
            ]
        );

        Notification::make()
            ->title('Our Story section saved successfully')
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