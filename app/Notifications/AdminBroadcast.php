<?php

namespace App\Notifications;

use App\Models\MessageCampaign;
use App\Models\User;
use App\Services\NotificationTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminBroadcast extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected MessageCampaign $campaign,
        protected string $channel,
        protected User $user
    ) {
        $this->onQueue('notifications');
    }

    public function via($notifiable): array
    {
        return [$this->channel];
    }

    public function toMail($notifiable): MailMessage
    {
        $templateService = app(NotificationTemplateService::class);
        $renderedBody = $templateService->renderCampaignMessage(
            $this->campaign->body_template,
            $this->user
        );

        return (new MailMessage)
            ->subject($this->campaign->subject ?? 'Message from ' . config('app.name'))
            ->greeting('Hello ' . $this->user->name . '!')
            ->line($renderedBody)
            ->salutation('Best regards, ' . config('app.name'));
    }

    public function toArray($notifiable): array
    {
        $templateService = app(NotificationTemplateService::class);
        $renderedBody = $templateService->renderCampaignMessage(
            $this->campaign->body_template,
            $this->user
        );

        return [
            'campaign_id' => $this->campaign->id,
            'title' => $this->campaign->title,
            'subject' => $this->campaign->subject,
            'body' => $renderedBody,
            'channel' => $this->channel,
            'type' => 'admin_broadcast',
            'read_at' => null,
        ];
    }

    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }

    public function toBroadcast($notifiable): array
    {
        return [
            'id' => $this->id,
            'campaign_id' => $this->campaign->id,
            'title' => $this->campaign->title,
            'subject' => $this->campaign->subject,
            'body' => $this->campaign->body_template,
            'channel' => $this->channel,
            'type' => 'admin_broadcast',
            'created_at' => now()->toISOString(),
        ];
    }
}
