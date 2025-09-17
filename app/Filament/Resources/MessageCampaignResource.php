<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageCampaignResource\Pages;
use App\Models\MessageCampaign;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class MessageCampaignResource extends Resource
{
    protected static ?string $model = MessageCampaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Campaigns';
    protected static ?string $navigationLabel = 'All Campaigns';
    protected static ?string $modelLabel = 'Campaign';
    protected static ?string $pluralModelLabel = 'Campaigns';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Campaign Details')
                    ->schema([
                        Forms\Components\Hidden::make('created_by')
                            ->default(auth()->id()),
                        Forms\Components\TextInput::make('title')
                            ->label('Campaign Name')
                            ->required()
                            ->placeholder('e.g., Holiday Sale Campaign')
                            ->helperText('Internal name for tracking this campaign'),
                        Forms\Components\Select::make('channels')
                            ->label('Campaign Type')
                            ->options([
                                'email' => 'Email Campaign',
                                'sms' => 'SMS Campaign',
                                'in_app' => 'In-App Notification',
                            ])
                            ->multiple()
                            ->required()
                            ->reactive()
                            ->helperText('Choose the type of campaign to send'),
                        Forms\Components\TextInput::make('subject')
                            ->label('Email Subject')
                            ->visible(fn (Forms\Get $get) => in_array('email', $get('channels') ?? []))
                            ->required(fn (Forms\Get $get) => in_array('email', $get('channels') ?? []))
                            ->placeholder('e.g., 🎄 Special Holiday Offers Just for You!')
                            ->helperText('This will appear in the email subject line'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Recipients')
                    ->schema([
                        Forms\Components\Select::make('recipient_type')
                            ->label('Send To')
                            ->options([
                                'all' => 'All Users',
                                'specific' => 'Specific Users', 
                                'segment' => 'User Segment',
                            ])
                            ->required()
                            ->reactive()
                            ->default('all'),
                        
                        Forms\Components\Select::make('segment')
                            ->label('User Segment')
                            ->options([
                                'new_users' => 'New Users (Last 30 days)',
                                'active_users' => 'Active Users (Last 7 days)',
                                'inactive_users' => 'Inactive Users (30+ days)',
                                'premium_users' => 'Premium Customers',
                            ])
                            ->visible(fn (Forms\Get $get) => $get('recipient_type') === 'segment')
                            ->required(fn (Forms\Get $get) => $get('recipient_type') === 'segment'),
                        
                        Forms\Components\Select::make('specific_users')
                            ->label('Select Users')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(User::all()->pluck('name', 'id'))
                            ->visible(fn (Forms\Get $get) => $get('recipient_type') === 'specific')
                            ->required(fn (Forms\Get $get) => $get('recipient_type') === 'specific'),
                        
                        Forms\Components\Placeholder::make('recipient_count')
                            ->label('Estimated Recipients')
                            ->content(function (Forms\Get $get) {
                                $count = match($get('recipient_type')) {
                                    'all' => User::count(),
                                    'specific' => count($get('specific_users') ?? []),
                                    'segment' => self::getSegmentCount($get('segment')),
                                    default => 0,
                                };
                                return new HtmlString("<span class='text-lg font-semibold'>{$count} users</span>");
                            }),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Content')
                    ->schema([
                        Forms\Components\RichEditor::make('body_template')
                            ->label('Email Body')
                            ->required()
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'strike', 'h2', 'h3',
                                'bulletList', 'orderedList', 'link', 'blockquote', 'codeBlock',
                                'table', 'redo', 'undo',
                            ])
                            ->visible(fn (Forms\Get $get) => in_array('email', $get('channels') ?? []))
                            ->helperText('You can use variables: {{name}}, {{email}}')
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('sms_content')
                            ->label('SMS Message')
                            ->required()
                            ->rows(4)
                            ->maxLength(160)
                            ->helperText(fn ($state) => (160 - strlen($state ?? '')) . ' characters remaining')
                            ->visible(fn (Forms\Get $get) => in_array('sms', $get('channels') ?? []))
                            ->reactive()
                            ->placeholder('Hi {{name}}! Flash Sale: 50% OFF all bakery items today only!')
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('notification_content')
                            ->label('Notification Message')
                            ->required()
                            ->rows(4)
                            ->maxLength(500)
                            ->helperText('Keep it concise. Max 500 characters.')
                            ->visible(fn (Forms\Get $get) => in_array('in_app', $get('channels') ?? []))
                            ->placeholder('Check out our new features that will help you save time and boost productivity!')
                            ->columnSpanFull(),
                        
                        Forms\Components\FileUpload::make('attachments')
                            ->label('Attachments (Optional)')
                            ->multiple()
                            ->maxFiles(3)
                            ->maxSize(5120)
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->helperText('Max 3 files, 5MB each. PDFs and images only.')
                            ->visible(fn (Forms\Get $get) => in_array('email', $get('channels') ?? []))
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Scheduling')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'sending' => 'Sending',
                                'sent' => 'Sent',
                                'failed' => 'Failed',
                            ])
                            ->default('draft')
                            ->required(),
                        
                        Forms\Components\DateTimePicker::make('schedule_at')
                            ->label('Schedule For')
                            ->minDate(now()->addMinutes(5))
                            ->visible(fn (Forms\Get $get) => $get('status') === 'scheduled')
                            ->required(fn (Forms\Get $get) => $get('status') === 'scheduled')
                            ->helperText('Schedule the campaign to be sent at a specific date and time'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->label('Campaign Name'),
                Tables\Columns\TextColumn::make('channels')
                    ->badge()
                    ->label('Type'),
                Tables\Columns\TextColumn::make('subject')
                    ->limit(30)
                    ->toggleable()
                    ->label('Subject/Message'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'scheduled' => 'info',
                        'sending' => 'warning',
                        'sent' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total_recipients')
                    ->numeric()
                    ->sortable()
                    ->label('Recipients'),
                Tables\Columns\TextColumn::make('sent_count')
                    ->numeric()
                    ->label('Sent'),
                Tables\Columns\TextColumn::make('failed_count')
                    ->numeric()
                    ->label('Failed'),
                Tables\Columns\TextColumn::make('schedule_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('channels')
                    ->label('Campaign Type')
                    ->options([
                        'email' => 'Email',
                        'sms' => 'SMS',
                        'in_app' => 'In-App Notification',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'sending' => 'Sending',
                        'sent' => 'Sent',
                        'failed' => 'Failed',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMessageCampaigns::route('/'),
            'create' => Pages\CreateMessageCampaign::route('/create'),
            'edit' => Pages\EditMessageCampaign::route('/{record}/edit'),
        ];
    }

    protected static function getSegmentCount($segment): int
    {
        if (!$segment) return 0;
        
        return match($segment) {
            'new_users' => User::where('created_at', '>=', now()->subDays(30))->count(),
            'active_users' => User::where('last_login_at', '>=', now()->subDays(7))->count(),
            'inactive_users' => User::where('last_login_at', '<=', now()->subDays(30))->orWhereNull('last_login_at')->count(),
            'premium_users' => User::whereHas('orders')->count(),
            default => 0,
        };
    }
}


