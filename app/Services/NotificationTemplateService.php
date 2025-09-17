<?php

namespace App\Services;

use App\Models\NotificationTemplate;
use App\Models\User;
use Illuminate\Support\Str;

class NotificationTemplateService
{
    public function renderTemplate(NotificationTemplate $template, User $user, array $additionalData = []): array
    {
        $variables = $this->buildVariables($user, $additionalData);
        
        return [
            'subject' => $this->renderText($template->default_subject, $variables),
            'body' => $this->renderText($template->default_body_template, $variables),
            'channels' => $template->default_channels,
        ];
    }

    public function renderCampaignMessage(string $bodyTemplate, User $user, array $additionalData = []): string
    {
        $variables = $this->buildVariables($user, $additionalData);
        return $this->renderText($bodyTemplate, $variables);
    }

    protected function buildVariables(User $user, array $additionalData = []): array
    {
        return array_merge([
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'id' => $user->id,
            ],
            'app' => [
                'name' => config('app.name', 'Bakery Shop'),
                'url' => config('app.url', 'https://bakeryshop.com'),
            ],
            'date' => [
                'today' => now()->format('F j, Y'),
                'time' => now()->format('g:i A'),
                'year' => now()->year,
            ],
        ], $additionalData);
    }

    protected function renderText(string $template, array $variables): string
    {
        $rendered = $template;
        
        // Replace variables in the format {{ variable }}
        foreach ($variables as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    $rendered = str_replace("{{ {$key}.{$subKey} }}", $subValue, $rendered);
                }
            } else {
                $rendered = str_replace("{{ {$key} }}", $value, $rendered);
            }
        }

        return $rendered;
    }

    public function getAvailableTemplates(): array
    {
        return NotificationTemplate::active()
            ->select(['id', 'title', 'slug', 'default_subject', 'default_channels'])
            ->get()
            ->map(function ($template) {
                return [
                    'id' => $template->id,
                    'title' => $template->title,
                    'slug' => $template->slug,
                    'subject' => $template->default_subject,
                    'channels' => $template->default_channels,
                ];
            })
            ->toArray();
    }

    public function createTemplateFromCampaign(array $campaignData): NotificationTemplate
    {
        return NotificationTemplate::create([
            'title' => $campaignData['title'] . ' Template',
            'slug' => Str::slug($campaignData['title'] . ' template'),
            'default_subject' => $campaignData['subject'] ?? '',
            'default_body_template' => $campaignData['body_template'],
            'default_channels' => $campaignData['channels'] ?? ['in_app', 'email'],
            'variables' => $this->extractVariables($campaignData['body_template']),
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);
    }

    protected function extractVariables(string $template): array
    {
        preg_match_all('/\{\{\s*([^}]+)\s*\}\}/', $template, $matches);
        
        $variables = [];
        if (isset($matches[1])) {
            foreach ($matches[1] as $match) {
                $variable = trim($match);
                if (str_contains($variable, '.')) {
                    [$parent, $child] = explode('.', $variable, 2);
                    $variables[$parent][$child] = ucfirst($child);
                } else {
                    $variables[$variable] = ucfirst($variable);
                }
            }
        }
        
        return $variables;
    }

    public function validateTemplate(string $template): array
    {
        $errors = [];
        
        // Check for unmatched braces
        $openBraces = substr_count($template, '{{');
        $closeBraces = substr_count($template, '}}');
        
        if ($openBraces !== $closeBraces) {
            $errors[] = 'Unmatched template braces detected';
        }
        
        // Check for empty variables
        if (preg_match('/\{\{\s*\}\}/', $template)) {
            $errors[] = 'Empty variable placeholder detected';
        }
        
        return $errors;
    }

    public function getDefaultVariables(): array
    {
        return [
            'user.name' => 'User Name',
            'user.email' => 'User Email',
            'user.phone' => 'User Phone',
            'user.id' => 'User ID',
            'app.name' => 'Application Name',
            'app.url' => 'Application URL',
            'date.today' => 'Today\'s Date',
            'date.time' => 'Current Time',
            'date.year' => 'Current Year',
        ];
    }
}
