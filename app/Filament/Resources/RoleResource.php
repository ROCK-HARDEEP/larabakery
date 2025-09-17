<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Models\Role;
use App\Models\ModulePermission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Component;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Actions\ActionGroup;
use Filament\Notifications\Notification;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Role Manager';

    protected static ?string $modelLabel = 'Role';

    protected static ?string $pluralModelLabel = 'Roles';

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        return $user->hasRole('superadmin');
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        return $user->hasRole('superadmin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Role Information')
                    ->description('Basic role details and configuration')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Role Name')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->placeholder('e.g., content_manager')
                                    ->helperText('Use lowercase with underscores')
                                    ->disabled(fn ($record) => $record && $record->isSystemRole()),

                                TextInput::make('guard_name')
                                    ->label('Guard')
                                    ->default('web')
                                    ->disabled()
                                    ->dehydrated(),

                                Textarea::make('description')
                                    ->label('Description')
                                    ->rows(2)
                                    ->maxLength(500)
                                    ->placeholder('Describe the role\'s purpose and responsibilities')
                                    ->columnSpan(2),

                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true)
                                    ->helperText('Inactive roles cannot be assigned to users'),

                                TextInput::make('priority')
                                    ->label('Priority')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Higher priority roles appear first'),
                            ]),
                    ]),

                Section::make('Module Permissions')
                    ->description('Configure access permissions for each module')
                    ->schema([
                        static::getPermissionsGrid(),
                    ])
                    ->collapsible(),
            ]);
    }

    protected static function getPermissionsGrid(): Component
    {
        return Grid::make(1)
            ->schema(function () {
                $modules = ModulePermission::orderBy('group')
                    ->orderBy('sort_order')
                    ->orderBy('module_name')
                    ->get()
                    ->groupBy('group');

                $schema = [];

                foreach ($modules as $group => $groupModules) {
                    $groupName = $group ?: 'General';

                    $schema[] = Section::make($groupName)
                        ->schema(
                            $groupModules->map(function ($module) {
                                return Grid::make(6)
                                    ->schema([
                                        Forms\Components\Placeholder::make("module_{$module->id}_name")
                                            ->label('')
                                            ->content($module->module_name)
                                            ->columnSpan(1),

                                        Toggle::make("module_permissions.{$module->id}.can_view")
                                            ->label('View')
                                            ->inline()
                                            ->default(false)
                                            ->afterStateHydrated(function ($component, $state, $record) use ($module) {
                                                if ($record) {
                                                    $permission = $record->modulePermissions()
                                                        ->where('module_permission_id', $module->id)
                                                        ->first();
                                                    $component->state($permission?->can_view ?? false);
                                                }
                                            }),

                                        Toggle::make("module_permissions.{$module->id}.can_create")
                                            ->label('Create')
                                            ->inline()
                                            ->default(false)
                                            ->afterStateHydrated(function ($component, $state, $record) use ($module) {
                                                if ($record) {
                                                    $permission = $record->modulePermissions()
                                                        ->where('module_permission_id', $module->id)
                                                        ->first();
                                                    $component->state($permission?->can_create ?? false);
                                                }
                                            }),

                                        Toggle::make("module_permissions.{$module->id}.can_update")
                                            ->label('Update')
                                            ->inline()
                                            ->default(false)
                                            ->afterStateHydrated(function ($component, $state, $record) use ($module) {
                                                if ($record) {
                                                    $permission = $record->modulePermissions()
                                                        ->where('module_permission_id', $module->id)
                                                        ->first();
                                                    $component->state($permission?->can_update ?? false);
                                                }
                                            }),

                                        Toggle::make("module_permissions.{$module->id}.can_delete")
                                            ->label('Delete')
                                            ->inline()
                                            ->default(false)
                                            ->afterStateHydrated(function ($component, $state, $record) use ($module) {
                                                if ($record) {
                                                    $permission = $record->modulePermissions()
                                                        ->where('module_permission_id', $module->id)
                                                        ->first();
                                                    $component->state($permission?->can_delete ?? false);
                                                }
                                            }),

                                        Toggle::make("module_permissions.{$module->id}.can_export")
                                            ->label('Export')
                                            ->inline()
                                            ->default(false)
                                            ->afterStateHydrated(function ($component, $state, $record) use ($module) {
                                                if ($record) {
                                                    $permission = $record->modulePermissions()
                                                        ->where('module_permission_id', $module->id)
                                                        ->first();
                                                    $component->state($permission?->can_export ?? false);
                                                }
                                            }),
                                    ]);
                            })->toArray()
                        )
                        ->collapsible()
                        ->collapsed();
                }

                return $schema;
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Role Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->icon(fn ($record) => $record->isSystemRole() ? 'heroicon-o-lock-closed' : null)
                    ->iconColor('warning')
                    ->tooltip(fn ($record) => $record->isSystemRole() ? 'System Role (Protected)' : null),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users')
                    ->badge()
                    ->color('success'),

                BadgeColumn::make('is_custom')
                    ->label('Type')
                    ->getStateUsing(fn ($record) => $record->is_custom ? 'Custom' : 'System')
                    ->colors([
                        'primary' => fn ($state) => $state === 'Custom',
                        'gray' => fn ($state) => $state === 'System',
                    ]),

                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->disabled(fn ($record) => $record->isSystemRole()),

                TextColumn::make('priority')
                    ->label('Priority')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),

                Tables\Filters\SelectFilter::make('is_custom')
                    ->label('Type')
                    ->options([
                        '1' => 'Custom',
                        '0' => 'System',
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->visible(fn ($record) => !$record->isSystemRole() || auth()->user()->hasRole('superadmin')),
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn ($record) => !$record->isSystemRole())
                        ->before(function ($record) {
                            if ($record->users()->count() > 0) {
                                Notification::make()
                                    ->title('Cannot Delete Role')
                                    ->body('This role has users assigned to it.')
                                    ->danger()
                                    ->send();

                                return false;
                            }
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('priority', 'desc');
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }
}