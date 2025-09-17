<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactUsResource\Pages;
use App\Models\ContactUs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;

class ContactUsResource extends Resource
{
    protected static ?string $model = ContactUs::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Pages';

    protected static ?string $navigationLabel = 'Contact Page';

    protected static ?string $modelLabel = 'Contact Page';

    protected static ?int $navigationSort = 4;

    // Disable the resource interface - we'll use a custom page
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Contact Page Content')
                    ->tabs([
                        Tabs\Tab::make('Hero Section')
                            ->schema([
                                Section::make('Get In Touch Section')
                                    ->description('The main hero section displayed at the top of the Contact page')
                                    ->schema([
                                        Forms\Components\FileUpload::make('get_in_touch_image')
                                            ->label('Hero Image')
                                            ->image()
                                            ->directory('contact-us')
                                            ->visibility('public')
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('get_in_touch_title')
                                            ->label('Hero Title')
                                            ->required()
                                            ->default('Get in Touch')
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('get_in_touch_quote')
                                            ->label('Hero Subtitle/Quote')
                                            ->rows(2)
                                            ->default('Have a question, special request, or just want to say hello? We\'d love to hear from you!')
                                            ->maxLength(500),
                                        Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('get_in_touch_button_text')
                                                    ->label('Button Text (Optional)')
                                                    ->maxLength(50),
                                                Forms\Components\TextInput::make('get_in_touch_button_link')
                                                    ->label('Button Link')
                                                    ->url()
                                                    ->default('#contact-form'),
                                                Forms\Components\ColorPicker::make('get_in_touch_button_color')
                                                    ->label('Button Color')
                                                    ->default('#F69D1C'),
                                            ]),
                                    ])
                            ]),

                        Tabs\Tab::make('Contact Info')
                            ->schema([
                                Section::make('Contact Information')
                                    ->description('Your business contact details')
                                    ->schema([
                                        Forms\Components\Textarea::make('contact_address')
                                            ->label('Business Address')
                                            ->rows(3)
                                            ->placeholder("123 Bakery Street\nSweet District\nCity, State 12345")
                                            ->helperText('Enter each line of the address on a new line'),
                                        Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('contact_phone')
                                                    ->label('Phone Number')
                                                    ->tel()
                                                    ->placeholder('+1 (555) 123-4567'),
                                                Forms\Components\TextInput::make('contact_email')
                                                    ->label('Email Address')
                                                    ->email()
                                                    ->placeholder('contact@bakery.com'),
                                            ]),

                                        Section::make('Business Hours')
                                            ->schema([
                                                Forms\Components\Repeater::make('business_hours')
                                                    ->label('Operating Hours')
                                                    ->schema([
                                                        Grid::make(2)
                                                            ->schema([
                                                                Forms\Components\TextInput::make('day')
                                                                    ->label('Day/Days')
                                                                    ->placeholder('Monday - Friday')
                                                                    ->required(),
                                                                Forms\Components\TextInput::make('hours')
                                                                    ->label('Hours')
                                                                    ->placeholder('8:00 AM - 6:00 PM')
                                                                    ->required(),
                                                            ]),
                                                    ])
                                                    ->defaultItems(2)
                                                    ->default([
                                                        ['day' => 'Monday - Friday', 'hours' => '8:00 AM - 6:00 PM'],
                                                        ['day' => 'Saturday - Sunday', 'hours' => '9:00 AM - 5:00 PM'],
                                                    ])
                                                    ->reorderable()
                                                    ->collapsible()
                                                    ->itemLabel(fn (array $state): ?string => $state['day'] ?? null),
                                            ]),
                                    ])
                            ]),

                        Tabs\Tab::make('Social Media')
                            ->schema([
                                Section::make('Social Media Links')
                                    ->description('Connect your social media profiles')
                                    ->schema([
                                        Forms\Components\Repeater::make('social_media_links')
                                            ->label('Social Media Profiles')
                                            ->schema([
                                                Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\Select::make('platform')
                                                            ->label('Platform')
                                                            ->options([
                                                                'facebook' => 'Facebook',
                                                                'instagram' => 'Instagram',
                                                                'twitter' => 'Twitter/X',
                                                                'youtube' => 'YouTube',
                                                                'linkedin' => 'LinkedIn',
                                                                'pinterest' => 'Pinterest',
                                                                'tiktok' => 'TikTok',
                                                                'whatsapp' => 'WhatsApp',
                                                            ])
                                                            ->reactive()
                                                            ->afterStateUpdated(function ($state, callable $set) {
                                                                $icons = [
                                                                    'facebook' => 'fab fa-facebook-f',
                                                                    'instagram' => 'fab fa-instagram',
                                                                    'twitter' => 'fab fa-twitter',
                                                                    'youtube' => 'fab fa-youtube',
                                                                    'linkedin' => 'fab fa-linkedin-in',
                                                                    'pinterest' => 'fab fa-pinterest',
                                                                    'tiktok' => 'fab fa-tiktok',
                                                                    'whatsapp' => 'fab fa-whatsapp',
                                                                ];
                                                                $set('icon', $icons[$state] ?? 'fas fa-link');
                                                            })
                                                            ->required(),
                                                        Forms\Components\TextInput::make('icon')
                                                            ->label('Icon Class')
                                                            ->placeholder('fab fa-facebook')
                                                            ->helperText('Font Awesome icon class')
                                                            ->required(),
                                                        Forms\Components\TextInput::make('url')
                                                            ->label('Profile URL')
                                                            ->url()
                                                            ->required()
                                                            ->placeholder('https://facebook.com/yourbakery'),
                                                    ]),
                                            ])
                                            ->defaultItems(3)
                                            ->default([
                                                ['platform' => 'facebook', 'url' => 'https://facebook.com/sweetkaramcoffee', 'icon' => 'fab fa-facebook-f'],
                                                ['platform' => 'instagram', 'url' => 'https://instagram.com/sweetkaramcoffee', 'icon' => 'fab fa-instagram'],
                                                ['platform' => 'twitter', 'url' => 'https://twitter.com/sweetkaramcoffee', 'icon' => 'fab fa-twitter'],
                                            ])
                                            ->reorderable()
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['platform'] ?? null)
                                            ->columnSpanFull(),
                                    ])
                            ]),

                        Tabs\Tab::make('Location')
                            ->schema([
                                Section::make('Map & Location')
                                    ->description('Your bakery location on the map')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('map_latitude')
                                                    ->label('Latitude')
                                                    ->numeric()
                                                    ->placeholder('40.7128')
                                                    ->helperText('You can get coordinates from Google Maps'),
                                                Forms\Components\TextInput::make('map_longitude')
                                                    ->label('Longitude')
                                                    ->numeric()
                                                    ->placeholder('-74.0060'),
                                            ]),
                                        Forms\Components\TextInput::make('map_address')
                                            ->label('Map Display Address')
                                            ->placeholder('123 Bakery Street, Sweet District')
                                            ->helperText('This address will be shown below the map')
                                            ->columnSpanFull(),
                                        Forms\Components\FileUpload::make('map_image')
                                            ->label('Map Preview Image (Optional)')
                                            ->image()
                                            ->directory('contact-us')
                                            ->visibility('public')
                                            ->helperText('Optional static map image for fallback')
                                            ->columnSpanFull(),
                                    ])
                            ]),

                        Tabs\Tab::make('FAQs')
                            ->schema([
                                Section::make('Frequently Asked Questions')
                                    ->description('Common questions and answers displayed at the bottom of the contact page')
                                    ->schema([
                                        Forms\Components\Repeater::make('faqs')
                                            ->label('FAQ Items')
                                            ->schema([
                                                Forms\Components\TextInput::make('question')
                                                    ->label('Question')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpanFull(),
                                                Forms\Components\Textarea::make('answer')
                                                    ->label('Answer')
                                                    ->required()
                                                    ->rows(3)
                                                    ->maxLength(1000)
                                                    ->columnSpanFull(),
                                                Forms\Components\Toggle::make('is_active')
                                                    ->label('Show on website')
                                                    ->default(true)
                                                    ->inline(),
                                            ])
                                            ->defaultItems(5)
                                            ->default([
                                                [
                                                    'question' => 'What are your delivery options?',
                                                    'answer' => 'We offer same-day delivery for orders placed before 2 PM within a 10-mile radius. We also provide pickup options and nationwide shipping for select items.',
                                                    'is_active' => true,
                                                ],
                                                [
                                                    'question' => 'Do you accept custom cake orders?',
                                                    'answer' => 'Yes! We love creating custom cakes for special occasions. Please place your order at least 48 hours in advance for custom designs.',
                                                    'is_active' => true,
                                                ],
                                                [
                                                    'question' => 'Are your products suitable for people with allergies?',
                                                    'answer' => 'We offer gluten-free, dairy-free, and nut-free options. Please inform us about any allergies when placing your order, and we\'ll ensure proper handling.',
                                                    'is_active' => true,
                                                ],
                                                [
                                                    'question' => 'Can I cancel or modify my order?',
                                                    'answer' => 'Orders can be modified or cancelled up to 24 hours before the scheduled delivery or pickup time. Custom orders may have different cancellation policies.',
                                                    'is_active' => true,
                                                ],
                                                [
                                                    'question' => 'Do you offer catering services for events?',
                                                    'answer' => 'Yes, we provide catering services for weddings, corporate events, and parties. Contact us for a custom quote based on your requirements.',
                                                    'is_active' => true,
                                                ],
                                            ])
                                            ->reorderable()
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['question'] ?? null)
                                            ->columnSpanFull(),
                                    ])
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('get_in_touch_image')
                    ->label('Hero Image')
                    ->square(),
                Tables\Columns\TextColumn::make('get_in_touch_title')
                    ->label('Page Title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_email')
                    ->label('Contact Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_phone')
                    ->label('Contact Phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('faqs')
                    ->label('FAQs')
                    ->getStateUsing(function ($record) {
                        $faqs = is_array($record->faqs) ? $record->faqs : [];
                        $activeFaqs = array_filter($faqs, function($faq) {
                            return isset($faq['is_active']) && $faq['is_active'];
                        });
                        return count($activeFaqs) . ' active / ' . count($faqs) . ' total';
                    })
                    ->badge(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->paginated(false);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactUs::route('/'),
            'create' => Pages\CreateContactUs::route('/create'),
            'edit' => Pages\EditContactUs::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return ContactUs::count() === 0;
    }
}