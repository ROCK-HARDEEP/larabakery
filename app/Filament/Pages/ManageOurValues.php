<?php

namespace App\Filament\Pages;

use App\Models\PageSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class ManageOurValues extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationGroup = null;
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.pages.manage-single-section';
    
    protected static ?string $title = 'Manage Our Values';

    public ?array $data = [];
    
    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.pages.about-page') => 'About Page',
            url()->current() => 'Our Values',
        ];
    }

    public function mount(): void
    {
        $section = PageSection::getOrCreate('about', 'our_values', [
            'title' => 'Our Values',
            'content' => 'The principles that guide everything we do',
            'data' => [
                'values' => [
                    ['title' => 'Quality First', 'description' => 'We never compromise on the quality of our ingredients or craftsmanship', 'icon' => 'star'],
                    ['title' => 'Community Love', 'description' => 'Supporting local suppliers and bringing joy to our neighborhood', 'icon' => 'heart'],
                    ['title' => 'Sustainability', 'description' => 'Eco-friendly practices and responsible sourcing', 'icon' => 'leaf'],
                    ['title' => 'Innovation', 'description' => 'Blending tradition with creative new flavors', 'icon' => 'lightbulb'],
                ]
            ]
        ]);

        $this->form->fill([
            'data.title' => $section->title,
            'data.content' => $section->content,
            'data.values_list' => $section->data['values'] ?? [],
            'data.is_active' => $section->is_active,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Our Values')
                ->description('Define your core values')
                ->schema([
                    Forms\Components\TextInput::make('data.title')
                        ->label('Section Title')
                        ->required(),
                    Forms\Components\Textarea::make('data.content')
                        ->label('Values Introduction')
                        ->rows(2),
                    Forms\Components\Repeater::make('data.values_list')
                        ->label('Core Values')
                        ->schema([
                            Forms\Components\Grid::make(3)
                                ->schema([
                                    Forms\Components\Select::make('icon')
                                        ->label('Icon')
                                        ->options([
                                            'star' => '⭐ Star',
                                            'heart' => '❤️ Heart',
                                            'leaf' => '🍃 Leaf',
                                            'lightbulb' => '💡 Lightbulb',
                                            'flag' => '🚩 Flag',
                                            'shield' => '🛡️ Shield',
                                            'trophy' => '🏆 Trophy',
                                            'hand' => '🤝 Handshake',
                                        ])
                                        ->default('star')
                                        ->columnSpan(1),
                                    Forms\Components\TextInput::make('title')
                                        ->label('Value Title')
                                        ->required()
                                        ->columnSpan(1),
                                    Forms\Components\TextInput::make('description')
                                        ->label('Description')
                                        ->required()
                                        ->columnSpan(1),
                                ]),
                        ])
                        ->columns(1)
                        ->defaultItems(4)
                        ->minItems(1)
                        ->maxItems(8)
                        ->reorderable()
                        ->collapsed()
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'New Value')
                        ->addActionLabel('Add New Value')
                        ->deleteAction(
                            fn (Forms\Components\Actions\Action $action) => $action->requiresConfirmation()
                        ),
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
            ['page' => 'about', 'section' => 'our_values'],
            [
                'title' => $formData['data']['title'],
                'content' => $formData['data']['content'],
                'data' => ['values' => $formData['data']['values_list']],
                'is_active' => $formData['data']['is_active'],
            ]
        );

        Notification::make()
            ->title('Our Values section saved successfully')
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