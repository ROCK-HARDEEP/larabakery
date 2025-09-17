<?php

namespace App\Filament\Pages;

use App\Models\PageSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class ManageMeetOurTeam extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = null;
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.pages.manage-single-section';
    
    protected static ?string $title = 'Manage Meet Our Team';

    public ?array $data = [];
    
    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.pages.about-page') => 'About Page',
            url()->current() => 'Meet Our Team',
        ];
    }

    public function mount(): void
    {
        $section = PageSection::getOrCreate('about', 'meet_our_team', [
            'title' => 'Meet Our Team',
            'content' => 'The talented people behind every delicious creation',
            'data' => [
                'members' => [
                    ['name' => 'John Smith', 'role' => 'Master Baker', 'image' => '', 'bio' => 'With over 20 years of experience, John brings traditional techniques to modern baking.'],
                    ['name' => 'Sarah Johnson', 'role' => 'Pastry Chef', 'image' => '', 'bio' => 'Sarah\'s creative desserts have won multiple awards and hearts.'],
                    ['name' => 'Mike Chen', 'role' => 'Bread Specialist', 'image' => '', 'bio' => 'Mike perfects our artisan breads with patience and precision.'],
                ]
            ]
        ]);
        
        // Check if there's existing data from AboutUs model
        $aboutUs = \App\Models\AboutUs::first();
        if ($aboutUs && $aboutUs->team_members && empty($section->data['members'][0]['image'])) {
            $updatedMembers = $section->data['members'] ?? [];
            foreach ($aboutUs->team_members as $index => $member) {
                if (isset($updatedMembers[$index]) && isset($member['image']) && $member['image']) {
                    $updatedMembers[$index]['image'] = $member['image'];
                }
            }
            $section->data = ['members' => $updatedMembers];
            $section->save();
        }
        
        // Convert image strings to arrays for FileUpload component
        $members = $section->data['members'] ?? [];
        foreach ($members as &$member) {
            if (isset($member['image']) && $member['image'] && !is_array($member['image'])) {
                $member['image'] = [$member['image']];
            }
        }

        $this->form->fill([
            'data.title' => $section->title,
            'data.content' => $section->content,
            'data.members' => $members,
            'data.is_active' => $section->is_active,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Meet Our Team')
                ->description('Showcase your team members')
                ->schema([
                    Forms\Components\TextInput::make('data.title')
                        ->label('Section Title')
                        ->required(),
                    Forms\Components\Textarea::make('data.content')
                        ->label('Team Introduction')
                        ->rows(2),
                    Forms\Components\Repeater::make('data.members')
                        ->label('Team Members')
                        ->schema([
                            Forms\Components\Grid::make(4)
                                ->schema([
                                    Forms\Components\FileUpload::make('image')
                                        ->label('Photo')
                                        ->image()
                                        ->disk('public')
                                        ->directory('team')
                                        ->visibility('public')
                            ->optimizeToWebP()
                                        ->avatar()
                                        ->imageResizeMode('cover')
                                        ->imageCropAspectRatio('1:1')
                                        ->imagePreviewHeight('40')
                                        ->columnSpan(1),
                                    Forms\Components\TextInput::make('name')
                                        ->label('Name')
                                        ->required()
                                        ->columnSpan(1),
                                    Forms\Components\TextInput::make('role')
                                        ->label('Role')
                                        ->required()
                                        ->columnSpan(1),
                                    Forms\Components\TextInput::make('bio')
                                        ->label('Bio')
                                        ->placeholder('Brief description')
                                        ->columnSpan(1),
                                ]),
                        ])
                        ->columns(1)
                        ->defaultItems(3)
                        ->minItems(1)
                        ->maxItems(20)
                        ->reorderable()
                        ->collapsed()
                        ->itemLabel(fn (array $state): ?string => isset($state['name']) ? $state['name'] . ' - ' . ($state['role'] ?? '') : 'New Team Member')
                        ->addActionLabel('Add Team Member')
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
        
        // Convert image arrays back to strings for storage
        $members = $formData['data']['members'];
        foreach ($members as &$member) {
            if (isset($member['image']) && is_array($member['image'])) {
                $member['image'] = $member['image'][0] ?? null;
            }
        }

        PageSection::updateOrCreate(
            ['page' => 'about', 'section' => 'meet_our_team'],
            [
                'title' => $formData['data']['title'],
                'content' => $formData['data']['content'],
                'data' => ['members' => $members],
                'is_active' => $formData['data']['is_active'],
            ]
        );

        Notification::make()
            ->title('Meet Our Team section saved successfully')
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