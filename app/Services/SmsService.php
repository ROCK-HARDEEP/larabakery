<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SmsService
{
    protected $provider;
    protected $config;

    public function __construct()
    {
        $this->provider = config('notifications.sms.provider', 'test');
        $this->config = config('notifications.sms.' . $this->provider, []);
    }

    /**
     * Send SMS message
     */
    public function send($to, $message, $from = null)
    {
        try {
            if (!$this->isEnabled()) {
                Log::warning('SMS service is not enabled. Message would be sent to: ' . $to);
                return [
                    'success' => false,
                    'message' => 'SMS service is not configured',
                    'error' => 'SMS_PROVIDER not configured'
                ];
            }

            switch ($this->provider) {
                case 'twilio':
                    return $this->sendViaTwilio($to, $message, $from);
                case 'test':
                    return $this->sendTestSms($to, $message);
                default:
                    return $this->sendTestSms($to, $message);
            }
        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage(), [
                'to' => $to,
                'provider' => $this->provider,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send SMS',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send SMS via Twilio
     */
    protected function sendViaTwilio($to, $message, $from = null)
    {
        $from = $from ?: $this->config['from'];
        
        if (!$this->config['enabled']) {
            return $this->sendTestSms($to, $message);
        }

        $response = Http::withBasicAuth(
            $this->config['account_sid'],
            $this->config['auth_token']
        )->post("https://api.twilio.com/2010-04-01/Accounts/{$this->config['account_sid']}/Messages.json", [
            'From' => $from,
            'To' => $to,
            'Body' => $message,
        ]);

        if ($response->successful()) {
            Log::info('SMS sent successfully via Twilio', [
                'to' => $to,
                'message_id' => $response->json('sid')
            ]);

            return [
                'success' => true,
                'message' => 'SMS sent successfully',
                'message_id' => $response->json('sid')
            ];
        }

        Log::error('Twilio SMS failed', [
            'to' => $to,
            'response' => $response->json(),
            'status' => $response->status()
        ]);

        return [
            'success' => false,
            'message' => 'Failed to send SMS via Twilio',
            'error' => $response->json('message', 'Unknown error')
        ];
    }

    /**
     * Send test SMS (for development)
     */
    protected function sendTestSms($to, $message)
    {
        Log::info('Test SMS would be sent', [
            'to' => $to,
            'message' => $message,
            'provider' => $this->provider
        ]);

        return [
            'success' => true,
            'message' => 'Test SMS logged (not actually sent)',
            'test' => true,
            'to' => $to,
            'message_content' => $message
        ];
    }

    /**
     * Check if SMS service is enabled
     */
    protected function isEnabled()
    {
        return $this->config['enabled'] ?? false;
    }

    /**
     * Send verification code
     */
    public function sendVerificationCode($phone, $code)
    {
        $message = "Your Bakery Shop verification code is: {$code}. Valid for 10 minutes.";
        return $this->send($phone, $message);
    }
}
