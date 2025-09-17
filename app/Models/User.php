<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Traits\Auditable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, Auditable;
    
    protected $fillable = [
        'name',
        'email', 
        'password',
        'phone',
        'gstin',
        'username',
        'google_id',
        'facebook_id',
        'provider',
        'provider_token',
        'social_data',
        'phone_verified_at',
        'phone_verification_code',
        'last_login_at',
        'login_ip',
        'address',
        'pincode',
        'pincode_verified_at',
        // Notification preferences
        'notify_in_app',
        'notify_email',
        'notify_whatsapp',
        'notify_sms',
        'notify_push',
        'fcm_token',
        'wa_number',
        'sms_number',
        'avatar_url',
        'tags'
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
        'provider_token',
        'phone_verification_code'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'pincode_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'social_data' => 'array',
        'password' => 'hashed',
        // Notification casts
        'notify_in_app' => 'boolean',
        'notify_email' => 'boolean',
        'notify_whatsapp' => 'boolean',
        'notify_sms' => 'boolean',
        'notify_push' => 'boolean',
        'tags' => 'array',
    ];

    // Relationship used by CustomerResource (for orders_count)
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole(['admin', 'superadmin']);
    }

    // Notification relationships
    public function deliveries()
    {
        return $this->hasMany(MessageDelivery::class);
    }

    public function inAppNotifications()
    {
        return $this->notifications()->where('data->channel', 'in_app');
    }

    public function unreadInAppNotifications()
    {
        return $this->unreadNotifications()->where('data->channel', 'in_app');
    }

    // Notification preference methods
    public function canReceiveChannel(string $channel): bool
    {
        return match($channel) {
            'in_app' => $this->notify_in_app ?? true,
            'email' => $this->notify_email ?? true,
            'whatsapp' => $this->notify_whatsapp ?? false,
            'sms' => $this->notify_sms ?? false,
            'push' => $this->notify_push ?? false,
            default => false,
        };
    }

    public function getNotificationChannels(): array
    {
        $channels = [];
        
        if ($this->notify_in_app) $channels[] = 'in_app';
        if ($this->notify_email) $channels[] = 'email';
        if ($this->notify_whatsapp && $this->wa_number) $channels[] = 'whatsapp';
        if ($this->notify_sms && $this->sms_number) $channels[] = 'sms';
        if ($this->notify_push && $this->fcm_token) $channels[] = 'push';
        
        return $channels;
    }

    public function hasTag(string $tag): bool
    {
        return in_array($tag, $this->tags ?? []);
    }

    public function addTag(string $tag): void
    {
        $tags = $this->tags ?? [];
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->update(['tags' => $tags]);
        }
    }

    public function removeTag(string $tag): void
    {
        $tags = $this->tags ?? [];
        $tags = array_diff($tags, [$tag]);
        $this->update(['tags' => array_values($tags)]);
    }
}
