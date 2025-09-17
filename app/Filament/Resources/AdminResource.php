<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminResource\Pages;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;

class AdminResource extends Resource
{
    // Master superadmin email - only this user has full control
    private const MASTER_SUPERADMIN_EMAIL = 'superadmin@bakeryshop.com';

    /**
     * Check if current user is the master superadmin
     */
    private static function isMasterSuperAdmin(): bool
    {
        $user = auth()->user();
        return $user && $user->email === self::MASTER_SUPERADMIN_EMAIL;
    }
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Admin Users';

    protected static ?string $modelLabel = 'Admin User';

    protected static ?string $pluralModelLabel = 'Admin Users';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('roles') // Eager load roles
            ->whereHas('roles', function (Builder $query) {
                // Include system admin roles and all custom roles
                $query->where(function ($subQuery) {
                    $subQuery->whereIn('name', ['admin', 'superadmin'])
                            ->orWhere('is_custom', true);
                });
            });
    }

    public static function canViewAny(): bool
    {
        // Both superadmin and admin can view admin users
        $user = auth()->user();
        if (!$user) return false;

        // Load roles relationship if not loaded
        if (!$user->relationLoaded('roles')) {
            $user->load('roles');
        }

        return $user->hasRole(['superadmin', 'admin']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Show in navigation for both superadmin and admin
        $user = auth()->user();
        if (!$user) return false;

        // Load roles relationship if not loaded
        if (!$user->relationLoaded('roles')) {
            $user->load('roles');
        }

        return $user->hasRole(['superadmin', 'admin']);
    }

    public static function canCreate(): bool
    {
        // Only master superadmin can create admin users
        $user = auth()->user();
        if (!$user) return false;

        // Only the master superadmin can create new admin/superadmin users
        return self::isMasterSuperAdmin();
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        // Only master superadmin can edit any admin/superadmin user
        if (self::isMasterSuperAdmin()) {
            return true;
        }

        // Other superadmins cannot edit any superadmin users (including other superadmins)
        if (!$user->relationLoaded('roles')) {
            $user->load('roles');
        }

        // If current user is superadmin but not master, they can't edit superadmins
        if ($user->hasRole('superadmin')) {
            // Check if the record being edited is a superadmin
            if (!$record->relationLoaded('roles')) {
                $record->load('roles');
            }
            // Non-master superadmins cannot edit any superadmin users
            return !$record->hasRole('superadmin');
        }

        return false;
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        // Master superadmin cannot delete themselves
        if ($record->email === self::MASTER_SUPERADMIN_EMAIL) {
            return false;
        }

        // Only master superadmin can delete admin/superadmin users
        if (self::isMasterSuperAdmin()) {
            // Master can delete anyone except themselves
            return $record->id !== $user->id;
        }

        // Other superadmins cannot delete superadmin users
        if (!$user->relationLoaded('roles')) {
            $user->load('roles');
        }

        if ($user->hasRole('superadmin')) {
            // Check if the record being deleted is a superadmin
            if (!$record->relationLoaded('roles')) {
                $record->load('roles');
            }
            // Non-master superadmins cannot delete any superadmin users
            return !$record->hasRole('superadmin') && $record->id !== $user->id;
        }

        return false;
    }

    public static function canView($record): bool
    {
        // Both superadmin and admin can view admin user details
        $user = auth()->user();
        if (!$user) return false;

        if (!$user->relationLoaded('roles')) {
            $user->load('roles');
        }

        return $user->hasRole(['superadmin', 'admin']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Admin Information')
                    ->description('Admin user credentials and details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Full Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Enter full name'),

                                Forms\Components\TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->placeholder('Enter email address')
                                    ->autocomplete('new-email'),

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
                                    ->autocomplete('new-password')
                                    ->helperText('Leave empty to keep current password (when editing)'),

                                Forms\Components\Select::make('role')
                                    ->label('Role')
                                    ->options(function () {
                                        $roles = [];

                                        // Base system roles
                                        if (self::isMasterSuperAdmin()) {
                                            $roles = [
                                                'admin' => 'Admin (System Role)',
                                                'superadmin' => 'Super Admin (System Role)',
                                            ];
                                        } else {
                                            $roles = [
                                                'admin' => 'Admin (System Role)',
                                            ];
                                        }

                                        // Add custom roles created through Role Manager
                                        $customRoles = \App\Models\Role::where('is_active', true)
                                            ->where('is_custom', true)
                                            ->pluck('name', 'name')
                                            ->toArray();

                                        // Merge custom roles with system roles
                                        foreach ($customRoles as $roleName => $roleLabel) {
                                            $roles[$roleName] = $roleLabel . ' (Custom Role)';
                                        }

                                        return $roles;
                                    })
                                    ->required()
                                    ->default('admin')
                                    ->helperText(function () {
                                        if (self::isMasterSuperAdmin()) {
                                            return 'System roles: Admin (basic access) | Superadmin (full control) | Custom roles: Created through Role Manager with specific permissions';
                                        }
                                        return 'System roles: Admin (basic access) | Custom roles: Created through Role Manager with specific permissions';
                                    })
                                    ->disabled(function ($record) {
                                        // Disable role selection for non-master superadmins when editing superadmins
                                        if ($record && !self::isMasterSuperAdmin()) {
                                            if (!$record->relationLoaded('roles')) {
                                                $record->load('roles');
                                            }
                                            return $record->hasRole('superadmin');
                                        }
                                        return false;
                                    })
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record) {
                                            $component->state($record->roles->first()?->name);
                                        }
                                    }),

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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(url('https://ui-avatars.com/api/?name=Admin'))
                    ->size(40),

                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

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
                    ->icon('heroicon-m-phone'),

                BadgeColumn::make('roles.name')
                    ->label('Role')
                    ->colors([
                        'danger' => 'superadmin',
                        'warning' => 'admin',
                        'success' => ['Store Manager', 'Content Editor'], // Add your custom role names here
                    ])
                    ->formatStateUsing(function ($state, $record) {
                        $role = $record->roles->first();
                        if ($role && $role->is_custom) {
                            return $state . ' (Custom)';
                        }
                        return ucfirst($state);
                    }),

                IconColumn::make('email_verified_at')
                    ->label('Email Verified')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('last_login_at')
                    ->label('Last Login')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->since(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->visible(function ($record) {
                            // Use the canEdit method to determine visibility
                            return static::canEdit($record);
                        }),
                    DeleteAction::make()
                        ->requiresConfirmation()
                        ->visible(function ($record) {
                            // Use the canDelete method to determine visibility
                            return static::canDelete($record);
                        })
                        ->modalHeading('Delete Admin User')
                        ->modalDescription('Are you sure you want to delete this admin user? This action cannot be undone.')
                        ->modalSubmitActionLabel('Yes, delete'),
                ]),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdmins::route('/'),
            'create' => Pages\CreateAdmin::route('/create'),
            'edit' => Pages\EditAdmin::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('superadmin')) {
            return null;
        }

        return static::getEloquentQuery()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}