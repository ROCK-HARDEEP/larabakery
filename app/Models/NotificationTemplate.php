<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class NotificationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'default_subject',
        'default_body_template',
        'default_channels',
        'variables',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'default_channels' => 'array',
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    protected $attributes = [
        'default_channels' => '["in_app", "email"]',
        'is_active' => true,
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($template) {
            if (empty($template->slug)) {
                $template->slug = Str::slug($template->title);
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getAvailableVariables(): array
    {
        return array_merge([
            'user.name' => 'User Name',
            'user.email' => 'User Email',
            'user.phone' => 'User Phone',
            'app.name' => 'Application Name',
            'app.url' => 'Application URL',
            'date.today' => 'Today\'s Date',
            'date.time' => 'Current Time',
        ], $this->variables ?? []);
    }

    public function renderBody(User $user, array $additionalData = []): string
    {
        $variables = array_merge([
            'user' => $user,
            'app' => [
                'name' => config('app.name'),
                'url' => config('app.url'),
            ],
            'date' => [
                'today' => now()->format('F j, Y'),
                'time' => now()->format('g:i A'),
            ],
        ], $additionalData);

        $blade = $this->default_body_template;
        
        // Simple variable replacement for safety
        foreach ($variables as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    $blade = str_replace("{{ $key.$subKey }}", $subValue, $blade);
                }
            } else {
                $blade = str_replace("{{ $key }}", $value, $blade);
            }
        }

        return $blade;
    }
}