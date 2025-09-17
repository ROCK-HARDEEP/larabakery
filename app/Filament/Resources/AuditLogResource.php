<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\KeyValueEntry;
use Carbon\Carbon;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 99;

    protected static ?string $navigationLabel = 'Audit Logs';

    protected static ?string $modelLabel = 'Audit Log';

    protected static ?string $pluralModelLabel = 'Audit Logs';

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        if (!$user->relationLoaded('roles')) {
            $user->load('roles');
        }

        return $user->hasRole('superadmin');
    }

    public static function canCreate(): bool
    {
        return false; // Audit logs cannot be created manually
    }

    public static function canEdit($record): bool
    {
        return false; // Audit logs cannot be edited
    }

    public static function canDelete($record): bool
    {
        return false; // Audit logs cannot be deleted
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        if (!$user->relationLoaded('roles')) {
            $user->load('roles');
        }

        return $user->hasRole('superadmin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Audit logs are read-only
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Date & Time')
                    ->dateTime('M d, Y H:i:s')
                    ->sortable()
                    ->description(fn (AuditLog $record): string => Carbon::parse($record->created_at)->diffForHumans()),

                BadgeColumn::make('event')
                    ->label('Action')
                    ->getStateUsing(fn (AuditLog $record): string => $record->formatted_event)
                    ->colors([
                        'success' => 'created',
                        'warning' => 'updated',
                        'danger' => ['deleted', 'failed_login', 'permission_denied'],
                        'info' => ['login', 'logout'],
                        'primary' => 'viewed',
                    ])
                    ->icon(fn (string $state): string => match($state) {
                        'created' => 'heroicon-o-plus-circle',
                        'updated' => 'heroicon-o-pencil',
                        'deleted' => 'heroicon-o-trash',
                        'login' => 'heroicon-o-arrow-right-on-rectangle',
                        'logout' => 'heroicon-o-arrow-left-on-rectangle',
                        'failed_login' => 'heroicon-o-x-circle',
                        'permission_denied' => 'heroicon-o-shield-exclamation',
                        default => 'heroicon-o-circle-stack',
                    }),

                TextColumn::make('causer.name')
                    ->label('User')
                    ->getStateUsing(fn (AuditLog $record): string => $record->causer_name)
                    ->searchable()
                    ->description(fn (AuditLog $record): string => $record->causer?->email ?? ''),

                TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                BadgeColumn::make('log_name')
                    ->label('Category')
                    ->colors([
                        'primary' => 'default',
                        'success' => 'auth',
                        'warning' => 'settings',
                        'danger' => 'security',
                        'info' => 'bulk',
                    ]),

                TextColumn::make('subject_type')
                    ->label('Resource')
                    ->getStateUsing(fn (AuditLog $record): string => $record->formatted_subject_type)
                    ->description(fn (AuditLog $record): ?string => $record->subject_id ? "ID: {$record->subject_id}" : null),

                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('method')
                    ->label('Method')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'GET' => 'info',
                        'POST' => 'success',
                        'PUT', 'PATCH' => 'warning',
                        'DELETE' => 'danger',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('log_name')
                    ->label('Category')
                    ->options([
                        'default' => 'Default',
                        'auth' => 'Authentication',
                        'settings' => 'Settings',
                        'security' => 'Security',
                        'bulk' => 'Bulk Actions',
                    ])
                    ->multiple(),

                SelectFilter::make('event')
                    ->label('Action')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                        'login' => 'Login',
                        'logout' => 'Logout',
                        'failed_login' => 'Failed Login',
                        'permission_denied' => 'Permission Denied',
                        'settings_changed' => 'Settings Changed',
                        'bulk_action' => 'Bulk Action',
                    ])
                    ->multiple(),

                Filter::make('causer')
                    ->form([
                        Forms\Components\Select::make('causer_id')
                            ->label('User')
                            ->options(function () {
                                return \App\Models\User::whereHas('roles', function ($query) {
                                    $query->whereIn('name', ['admin', 'superadmin']);
                                })->pluck('name', 'id');
                            })
                            ->searchable(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['causer_id'],
                            fn (Builder $query, $value): Builder => $query->where('causer_id', $value)->where('causer_type', 'App\Models\User'),
                        );
                    }),

                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('From'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Until'),
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
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'From ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Until ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),

                Filter::make('today')
                    ->label('Today')
                    ->query(fn (Builder $query): Builder => $query->whereDate('created_at', Carbon::today())),

                Filter::make('yesterday')
                    ->label('Yesterday')
                    ->query(fn (Builder $query): Builder => $query->whereDate('created_at', Carbon::yesterday())),

                Filter::make('last_7_days')
                    ->label('Last 7 days')
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '>=', Carbon::now()->subDays(7))),

                Filter::make('last_30_days')
                    ->label('Last 30 days')
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '>=', Carbon::now()->subDays(30))),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading('Audit Log Details')
                    ->modalWidth('lg'),
            ])
            ->bulkActions([
                // No bulk actions for audit logs
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('60s');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfoSection::make('Log Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')
                            ->label('Log ID'),
                        TextEntry::make('created_at')
                            ->label('Date & Time')
                            ->dateTime('M d, Y H:i:s'),
                        TextEntry::make('event')
                            ->label('Action')
                            ->getStateUsing(fn (AuditLog $record): string => $record->formatted_event)
                            ->badge()
                            ->color(fn (string $state): string => match($state) {
                                'created' => 'success',
                                'updated' => 'warning',
                                'deleted', 'failed_login', 'permission_denied' => 'danger',
                                'login', 'logout' => 'info',
                                default => 'gray',
                            }),
                        TextEntry::make('log_name')
                            ->label('Category')
                            ->badge(),
                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpan(2),
                    ]),

                InfoSection::make('User Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('causer_name')
                            ->label('User')
                            ->getStateUsing(fn (AuditLog $record): string => $record->causer_name),
                        TextEntry::make('causer.email')
                            ->label('User Email'),
                        TextEntry::make('ip_address')
                            ->label('IP Address'),
                        TextEntry::make('method')
                            ->label('HTTP Method')
                            ->badge(),
                        TextEntry::make('user_agent')
                            ->label('User Agent')
                            ->columnSpan(2),
                        TextEntry::make('url')
                            ->label('URL')
                            ->columnSpan(2),
                    ]),

                InfoSection::make('Subject Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('formatted_subject_type')
                            ->label('Resource Type'),
                        TextEntry::make('subject_id')
                            ->label('Resource ID'),
                    ])
                    ->visible(fn (AuditLog $record): bool => $record->subject_type !== null),

                InfoSection::make('Changes')
                    ->schema([
                        KeyValueEntry::make('old_values')
                            ->label('Old Values')
                            ->visible(fn (AuditLog $record): bool => !empty($record->old_values)),
                        KeyValueEntry::make('new_values')
                            ->label('New Values')
                            ->visible(fn (AuditLog $record): bool => !empty($record->new_values)),
                        KeyValueEntry::make('properties')
                            ->label('Additional Properties')
                            ->visible(fn (AuditLog $record): bool => !empty($record->properties)),
                    ])
                    ->visible(fn (AuditLog $record): bool =>
                        !empty($record->old_values) ||
                        !empty($record->new_values) ||
                        !empty($record->properties)
                    ),
            ]);
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
            'index' => Pages\ListAuditLogs::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('created_at', Carbon::today())->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}