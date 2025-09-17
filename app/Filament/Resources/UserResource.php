<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\Widgets\UserStatsOverview;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static bool $shouldRegisterNavigation = false;
    
    protected static ?string $recordTitleAttribute = 'name';

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Email' => $record->email,
            'Phone' => $record->phone,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Personal Information')
                    ->description('Basic user information and credentials')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Full Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Enter full name'),
                                
                                Forms\Components\TextInput::make('username')
                                    ->label('Username')
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(50)
                                    ->placeholder('Unique username'),
                                
                                Forms\Components\TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->placeholder('user@example.com'),
                                
                                Forms\Components\TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(20)
                                    ->placeholder('+91 9876543210'),
                                
                                Forms\Components\TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->maxLength(255)
                                    ->placeholder('Enter password')
                                    ->helperText('Leave empty to keep current password (when editing)'),
                                
                                Forms\Components\FileUpload::make('avatar_url')
                                    ->label('Profile Picture')
                                    ->image()
                                    ->imageEditor()
                                    ->circleCropper()
                                    ->imagePreviewHeight('150')
                                    ->directory('avatars')
                                    ->visibility('public')
                                    ->disk('public')
                                    ->maxSize(2048)
                                    ->helperText('Upload profile picture. Recommended size: 200x200px'),
                            ]),
                    ])
                    ->columns(2),
                
                Section::make('Contact & Address')
                    ->description('User contact details and address information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Textarea::make('address')
                                    ->label('Full Address')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->placeholder('Enter complete address'),
                                
                                Forms\Components\TextInput::make('pincode')
                                    ->label('Pincode')
                                    ->maxLength(10)
                                    ->placeholder('123456'),
                                
                                Forms\Components\TextInput::make('gstin')
                                    ->label('GSTIN (Optional)')
                                    ->maxLength(20)
                                    ->placeholder('GST identification number'),
                            ]),
                    ]),
                
                Section::make('Account Settings')
                    ->description('Account status and verification')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Forms\Components\DateTimePicker::make('email_verified_at')
                                    ->label('Email Verified At')
                                    ->displayFormat('d/m/Y H:i'),
                                
                                Forms\Components\DateTimePicker::make('phone_verified_at')
                                    ->label('Phone Verified At')
                                    ->displayFormat('d/m/Y H:i'),
                                
                                Forms\Components\DateTimePicker::make('last_login_at')
                                    ->label('Last Login')
                                    ->displayFormat('d/m/Y H:i')
                                    ->disabled(),
                            ]),
                    ]),
                
                Section::make('Notification Preferences')
                    ->description('User notification settings')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('notify_email')
                                    ->label('Email Notifications')
                                    ->default(true),
                                
                                Forms\Components\Toggle::make('notify_sms')
                                    ->label('SMS Notifications')
                                    ->default(false),
                                
                                Forms\Components\Toggle::make('notify_whatsapp')
                                    ->label('WhatsApp Notifications')
                                    ->default(false),
                                
                                Forms\Components\Toggle::make('notify_push')
                                    ->label('Push Notifications')
                                    ->default(true),
                            ]),
                    ])
                    ->columns(4),
                
                Section::make('Social Login Information')
                    ->description('Connected social accounts')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('google_id')
                                    ->label('Google ID')
                                    ->disabled()
                                    ->placeholder('Not connected'),
                                
                                Forms\Components\TextInput::make('facebook_id')
                                    ->label('Facebook ID')
                                    ->disabled()
                                    ->placeholder('Not connected'),
                                
                                Forms\Components\TextInput::make('provider')
                                    ->label('Login Provider')
                                    ->disabled()
                                    ->placeholder('Email'),
                            ]),
                    ])
                    ->collapsed(),
                
                Section::make('User Tags & Segmentation')
                    ->description('Tags for user segmentation and targeting')
                    ->schema([
                        Forms\Components\TagsInput::make('tags')
                            ->label('User Tags')
                            ->separator(',')
                            ->placeholder('Add tags...')
                            ->suggestions([
                                'VIP',
                                'Regular',
                                'New Customer',
                                'Wholesale',
                                'Retail',
                                'Premium',
                                'Inactive',
                                'Blocked',
                            ]),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(url('https://ui-avatars.com/api/?name=User'))
                    ->size(40),
                
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable()
                    ->weight('medium'),
                
                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email copied!')
                    ->icon('heroicon-m-envelope'),
                
                TextColumn::make('phone')
                    ->label('Phone')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Phone copied!')
                    ->icon('heroicon-m-phone')
                    ->toggleable(),
                
                TextColumn::make('address')
                    ->label('Address')
                    ->limit(50)
                    ->tooltip(function ($record) {
                        return $record->address;
                    })
                    ->searchable()
                    ->toggleable(),
                
                TextColumn::make('pincode')
                    ->label('Pincode')
                    ->searchable()
                    ->toggleable(),
                
                IconColumn::make('email_verified_at')
                    ->label('Email Verified')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                IconColumn::make('phone_verified_at')
                    ->label('Phone Verified')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->toggleable(),
                
                BadgeColumn::make('provider')
                    ->label('Login Method')
                    ->colors([
                        'primary' => 'email',
                        'info' => 'google',
                        'warning' => 'facebook',
                    ])
                    ->toggleable(),
                
                TextColumn::make('orders_count')
                    ->label('Orders')
                    ->counts('orders')
                    ->sortable()
                    ->badge()
                    ->color('success'),
                
                TextColumn::make('last_login_at')
                    ->label('Last Login')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->since()
                    ->toggleable(),
                
                TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                
                BadgeColumn::make('tags')
                    ->label('Tags')
                    ->separator(',')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('provider')
                    ->label('Login Method')
                    ->options([
                        'email' => 'Email',
                        'google' => 'Google',
                        'facebook' => 'Facebook',
                    ])
                    ->multiple(),
                
                TernaryFilter::make('email_verified_at')
                    ->label('Email Verification')
                    ->placeholder('All users')
                    ->trueLabel('Verified')
                    ->falseLabel('Not Verified')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('email_verified_at'),
                        false: fn (Builder $query) => $query->whereNull('email_verified_at'),
                    ),
                
                TernaryFilter::make('phone_verified_at')
                    ->label('Phone Verification')
                    ->placeholder('All users')
                    ->trueLabel('Verified')
                    ->falseLabel('Not Verified')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('phone_verified_at'),
                        false: fn (Builder $query) => $query->whereNull('phone_verified_at'),
                    ),
                
                Tables\Filters\Filter::make('has_orders')
                    ->label('Has Orders')
                    ->query(fn (Builder $query): Builder => $query->has('orders')),
                
                Tables\Filters\Filter::make('no_orders')
                    ->label('No Orders')
                    ->query(fn (Builder $query): Builder => $query->doesntHave('orders')),
                
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Registered From'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Registered Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                
                Tables\Filters\Filter::make('inactive')
                    ->label('Inactive (30+ days)')
                    ->query(fn (Builder $query): Builder => $query->where('last_login_at', '<', now()->subDays(30))),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete User')
                        ->modalDescription('Are you sure you want to delete this user? This action cannot be undone.')
                        ->modalSubmitActionLabel('Yes, delete')
                        ->successNotificationTitle('User deleted successfully'),
                    Tables\Actions\Action::make('verify_email')
                        ->label('Verify Email')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => !$record->email_verified_at)
                        ->action(fn ($record) => $record->update(['email_verified_at' => now()])),
                    Tables\Actions\Action::make('reset_password')
                        ->label('Reset Password')
                        ->icon('heroicon-o-key')
                        ->color('warning')
                        ->form([
                            Forms\Components\TextInput::make('password')
                                ->label('New Password')
                                ->password()
                                ->required()
                                ->minLength(8),
                        ])
                        ->action(function ($record, array $data): void {
                            $record->update(['password' => Hash::make($data['password'])]);
                        }),
                    Tables\Actions\Action::make('view_orders')
                        ->label('View Orders')
                        ->icon('heroicon-o-shopping-bag')
                        ->url(fn ($record) => OrderResource::getUrl('index', ['tableFilters[user_id][value]' => $record->id]))
                        ->openUrlInNewTab(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete Selected Users')
                        ->modalDescription('Are you sure you want to delete the selected users? This action cannot be undone.')
                        ->modalSubmitActionLabel('Yes, delete all'),
                    Tables\Actions\BulkAction::make('verify_emails')
                        ->label('Verify Emails')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['email_verified_at' => now()])),
                    Tables\Actions\BulkAction::make('add_tag')
                        ->label('Add Tag')
                        ->icon('heroicon-o-tag')
                        ->form([
                            Forms\Components\TextInput::make('tag')
                                ->label('Tag to Add')
                                ->required(),
                        ])
                        ->action(function ($records, array $data): void {
                            foreach ($records as $record) {
                                $tags = $record->tags ?? [];
                                if (!in_array($data['tag'], $tags)) {
                                    $tags[] = $data['tag'];
                                    $record->update(['tags' => $tags]);
                                }
                            }
                        }),
                    Tables\Actions\BulkAction::make('export')
                        ->label('Export to CSV')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->action(function ($records) {
                            // This would export selected users to CSV
                            // Implementation would depend on your export package
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('60s');
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }
    
    public static function getWidgets(): array
    {
        return [
            UserStatsOverview::class,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }
}