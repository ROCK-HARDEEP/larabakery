<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'event',
        'causer_type',
        'causer_id',
        'properties',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'method',
        'url',
    ];

    protected $casts = [
        'properties' => 'array',
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Get the subject of the activity.
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the causer of the activity.
     */
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get formatted event name
     */
    public function getFormattedEventAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->event ?? 'unknown'));
    }

    /**
     * Get formatted subject type
     */
    public function getFormattedSubjectTypeAttribute(): string
    {
        if (!$this->subject_type) {
            return 'System';
        }

        $className = class_basename($this->subject_type);
        return ucwords(str_replace('_', ' ', \Illuminate\Support\Str::snake($className)));
    }

    /**
     * Get causer name
     */
    public function getCauserNameAttribute(): string
    {
        if ($this->causer) {
            return $this->causer->name ?? $this->causer->email ?? 'Unknown';
        }
        return 'System';
    }

    /**
     * Get changes summary
     */
    public function getChangesSummaryAttribute(): array
    {
        $changes = [];

        if ($this->old_values && $this->new_values) {
            foreach ($this->new_values as $key => $newValue) {
                $oldValue = $this->old_values[$key] ?? null;
                if ($oldValue !== $newValue) {
                    $changes[$key] = [
                        'old' => $oldValue,
                        'new' => $newValue
                    ];
                }
            }
        }

        return $changes;
    }

    /**
     * Scope for filtering by causer
     */
    public function scopeByCauser($query, Model $causer)
    {
        return $query->where('causer_type', get_class($causer))
                     ->where('causer_id', $causer->id);
    }

    /**
     * Scope for filtering by subject
     */
    public function scopeForSubject($query, Model $subject)
    {
        return $query->where('subject_type', get_class($subject))
                     ->where('subject_id', $subject->id);
    }

    /**
     * Scope for filtering by event
     */
    public function scopeByEvent($query, string $event)
    {
        return $query->where('event', $event);
    }

    /**
     * Scope for filtering by log name
     */
    public function scopeInLog($query, string $logName)
    {
        return $query->where('log_name', $logName);
    }
}