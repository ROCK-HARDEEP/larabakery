<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    /**
     * Log an activity
     */
    public static function log(
        string $description,
        Model $subject = null,
        string $event = null,
        array $properties = null,
        string $logName = 'default'
    ): AuditLog {
        $user = Auth::user();

        $data = [
            'log_name' => $logName,
            'description' => $description,
            'event' => $event,
            'properties' => $properties,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'method' => Request::method(),
            'url' => Request::fullUrl(),
        ];

        if ($subject) {
            $data['subject_type'] = get_class($subject);
            $data['subject_id'] = $subject->id;
        }

        if ($user) {
            $data['causer_type'] = get_class($user);
            $data['causer_id'] = $user->id;
        }

        return AuditLog::create($data);
    }

    /**
     * Log a model creation
     */
    public static function logCreated(Model $model, string $logName = 'default'): AuditLog
    {
        $modelName = class_basename($model);
        $description = "Created {$modelName} #{$model->id}";

        return self::log(
            $description,
            $model,
            'created',
            ['attributes' => $model->getAttributes()],
            $logName
        );
    }

    /**
     * Log a model update
     */
    public static function logUpdated(Model $model, array $oldValues = [], string $logName = 'default'): AuditLog
    {
        $modelName = class_basename($model);
        $description = "Updated {$modelName} #{$model->id}";

        $dirty = $model->getDirty();
        $original = [];

        foreach ($dirty as $key => $value) {
            $original[$key] = $model->getOriginal($key);
        }

        $log = self::log(
            $description,
            $model,
            'updated',
            ['attributes' => $dirty],
            $logName
        );

        $log->update([
            'old_values' => $oldValues ?: $original,
            'new_values' => $dirty
        ]);

        return $log;
    }

    /**
     * Log a model deletion
     */
    public static function logDeleted(Model $model, string $logName = 'default'): AuditLog
    {
        $modelName = class_basename($model);
        $description = "Deleted {$modelName} #{$model->id}";

        return self::log(
            $description,
            $model,
            'deleted',
            ['attributes' => $model->getAttributes()],
            $logName
        );
    }

    /**
     * Log user login
     */
    public static function logLogin(Model $user): AuditLog
    {
        return self::log(
            "User logged in",
            $user,
            'login',
            [
                'ip' => Request::ip(),
                'user_agent' => Request::userAgent()
            ],
            'auth'
        );
    }

    /**
     * Log user logout
     */
    public static function logLogout(Model $user): AuditLog
    {
        return self::log(
            "User logged out",
            $user,
            'logout',
            [
                'ip' => Request::ip(),
                'user_agent' => Request::userAgent()
            ],
            'auth'
        );
    }

    /**
     * Log failed login attempt
     */
    public static function logFailedLogin(string $email): AuditLog
    {
        return self::log(
            "Failed login attempt for {$email}",
            null,
            'failed_login',
            [
                'email' => $email,
                'ip' => Request::ip(),
                'user_agent' => Request::userAgent()
            ],
            'auth'
        );
    }

    /**
     * Log permission denied
     */
    public static function logPermissionDenied(string $action, Model $subject = null): AuditLog
    {
        $description = "Permission denied for action: {$action}";

        if ($subject) {
            $modelName = class_basename($subject);
            $description .= " on {$modelName} #{$subject->id}";
        }

        return self::log(
            $description,
            $subject,
            'permission_denied',
            [
                'action' => $action,
                'ip' => Request::ip()
            ],
            'security'
        );
    }

    /**
     * Log settings change
     */
    public static function logSettingsChange(string $setting, $oldValue, $newValue): AuditLog
    {
        return self::log(
            "Changed setting: {$setting}",
            null,
            'settings_changed',
            [
                'setting' => $setting,
                'old_value' => $oldValue,
                'new_value' => $newValue
            ],
            'settings'
        );
    }

    /**
     * Log bulk action
     */
    public static function logBulkAction(string $action, string $modelClass, array $ids): AuditLog
    {
        $modelName = class_basename($modelClass);
        $count = count($ids);

        return self::log(
            "Performed bulk {$action} on {$count} {$modelName} records",
            null,
            'bulk_action',
            [
                'action' => $action,
                'model' => $modelClass,
                'ids' => $ids,
                'count' => $count
            ],
            'bulk'
        );
    }
}