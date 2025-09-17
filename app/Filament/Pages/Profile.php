<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use BackedEnum;
use UnitEnum;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class Profile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Profile';
    protected static ?string $navigationGroup = 'System Settings';
    protected static ?int $navigationSort = 82;
    protected static bool $shouldRegisterNavigation = true;
    protected static string $view = 'filament.pages.profile';
    
    // Profile fields
    public ?string $name = null;
    public ?string $email = null;
    public ?string $phone = null;
    public ?string $position = null;
    public ?string $bio = null;
    public ?string $timezone = null;
    public ?string $language = null;
    public bool $email_notifications = true;
    public bool $sms_notifications = false;
    public ?string $signature = null;
    public bool $two_factor_enabled = false;
    
    // Password fields
    public ?string $current_password = null;
    public ?string $new_password = null;
    public ?string $new_password_confirmation = null;
    
    public function mount(): void
    {
        $this->loadProfile();
    }
    
    public function getTitle(): string
    {
        return 'Admin Profile';
    }

    public function getHeading(): string
    {
        return 'Admin Profile';
    }
    
    protected function loadProfile(): void
    {
        $user = Auth::user();
        
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->position = cache()->get("user_profile_{$user->id}_position", '');
        $this->bio = cache()->get("user_profile_{$user->id}_bio", '');
        $this->timezone = cache()->get("user_profile_{$user->id}_timezone", 'Asia/Kolkata');
        $this->language = cache()->get("user_profile_{$user->id}_language", 'en');
        $this->email_notifications = cache()->get("user_profile_{$user->id}_email_notifications", true);
        $this->sms_notifications = cache()->get("user_profile_{$user->id}_sms_notifications", false);
        $this->signature = cache()->get("user_profile_{$user->id}_signature", '');
        $this->two_factor_enabled = cache()->get("user_profile_{$user->id}_two_factor_enabled", false);
    }
    
    public function updateProfile(): void
    {
        $user = Auth::user();
        
        // Update user model fields
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);
        
        // Store other profile data in cache
        cache()->put("user_profile_{$user->id}_position", $this->position);
        cache()->put("user_profile_{$user->id}_bio", $this->bio);
        cache()->put("user_profile_{$user->id}_timezone", $this->timezone);
        cache()->put("user_profile_{$user->id}_language", $this->language);
        cache()->put("user_profile_{$user->id}_email_notifications", $this->email_notifications);
        cache()->put("user_profile_{$user->id}_sms_notifications", $this->sms_notifications);
        cache()->put("user_profile_{$user->id}_signature", $this->signature);
        cache()->put("user_profile_{$user->id}_two_factor_enabled", $this->two_factor_enabled);
        
        Notification::make()
            ->title('Profile Updated')
            ->body('Your profile has been updated successfully.')
            ->success()
            ->send();
    }
    
    public function updatePassword(): void
    {
        $user = Auth::user();
        
        // Basic validation
        if (empty($this->current_password) || empty($this->new_password) || empty($this->new_password_confirmation)) {
            Notification::make()
                ->title('Error')
                ->body('All password fields are required.')
                ->danger()
                ->send();
            return;
        }
        
        if ($this->new_password !== $this->new_password_confirmation) {
            Notification::make()
                ->title('Error')
                ->body('New password confirmation does not match.')
                ->danger()
                ->send();
            return;
        }
        
        // Verify current password
        if (!Hash::check($this->current_password, $user->password)) {
            Notification::make()
                ->title('Error')
                ->body('The current password is incorrect.')
                ->danger()
                ->send();
            return;
        }
        
        // Update password
        $user->update([
            'password' => Hash::make($this->new_password),
        ]);
        
        // Clear password fields
        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';
        
        Notification::make()
            ->title('Password Changed')
            ->body('Your password has been changed successfully.')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}