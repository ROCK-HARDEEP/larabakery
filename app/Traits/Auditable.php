<?php

namespace App\Traits;

use App\Services\AuditLogService;

trait Auditable
{
    /**
     * Boot the auditable trait for a model.
     */
    public static function bootAuditable()
    {
        static::created(function ($model) {
            if (!self::shouldLogActivity()) {
                return;
            }
            AuditLogService::logCreated($model, 'default');
        });

        static::updated(function ($model) {
            if (!self::shouldLogActivity()) {
                return;
            }

            // Skip if only timestamps were updated
            $dirty = $model->getDirty();
            unset($dirty['updated_at']);

            if (!empty($dirty)) {
                AuditLogService::logUpdated($model, [], 'default');
            }
        });

        static::deleted(function ($model) {
            if (!self::shouldLogActivity()) {
                return;
            }
            AuditLogService::logDeleted($model, 'default');
        });
    }

    /**
     * Check if activity logging is enabled
     */
    protected static function shouldLogActivity(): bool
    {
        // Don't log if we're running migrations or seeding
        if (app()->runningInConsole()) {
            return false;
        }

        // Don't log audit log changes (prevent infinite loop)
        if (static::class === 'App\Models\AuditLog') {
            return false;
        }

        return true;
    }
}