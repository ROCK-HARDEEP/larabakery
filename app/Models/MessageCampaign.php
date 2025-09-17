<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class MessageCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subject',
        'body_template',
        'channels',
        'filters',
        'schedule_at',
        'status',
        'created_by',
        'total_recipients',
        'sent_count',
        'failed_count',
        'opened_count',
        'clicked_count',
        'started_at',
        'completed_at',
        'recipient_type',
        'specific_users',
        'segment',
        'sms_content',
        'notification_content',
        'attachments',
    ];

    protected $casts = [
        'channels' => 'array',
        'filters' => 'array',
        'schedule_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'draft',
        'channels' => '[]',
        'total_recipients' => 0,
        'sent_count' => 0,
        'failed_count' => 0,
        'opened_count' => 0,
        'clicked_count' => 0,
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(MessageDelivery::class, 'campaign_id');
    }

    public function scopeDue(Builder $query): Builder
    {
        return $query->where('status', 'scheduled')
            ->where('schedule_at', '<=', now());
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['scheduled', 'sending', 'sent']);
    }

    public function getSuccessRateAttribute(): float
    {
        if ($this->total_recipients === 0) {
            return 0;
        }
        return round(($this->sent_count / $this->total_recipients) * 100, 2);
    }

    public function getOpenRateAttribute(): float
    {
        if ($this->sent_count === 0) {
            return 0;
        }
        return round(($this->opened_count / $this->sent_count) * 100, 2);
    }

    public function getClickRateAttribute(): float
    {
        if ($this->opened_count === 0) {
            return 0;
        }
        return round(($this->clicked_count / $this->opened_count) * 100, 2);
    }

    public function markAsSending(): void
    {
        $this->update([
            'status' => 'sending',
            'started_at' => now(),
        ]);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => $this->failed_count === $this->total_recipients ? 'failed' : 'sent',
            'completed_at' => now(),
        ]);
    }

    public function incrementCounter(string $counter): void
    {
        $this->increment($counter);
    }

    public function canBeSent(): bool
    {
        return in_array($this->status, ['draft', 'scheduled']) && !empty($this->channels);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['scheduled', 'sending']);
    }
}