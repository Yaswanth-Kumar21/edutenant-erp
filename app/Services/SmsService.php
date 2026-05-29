<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * SmsService
 *
 * Integration-ready SMS service using Twilio.
 * Configure in .env:
 *   TWILIO_SID=ACxxxx
 *   TWILIO_TOKEN=xxxx
 *   TWILIO_FROM=+1xxxxxxxxxx
 *   SMS_ENABLED=true
 *
 * Install Twilio SDK when ready:
 *   composer require twilio/sdk
 */
class SmsService
{
    private bool $enabled;
    private string $sid;
    private string $token;
    private string $from;

    public function __construct()
    {
        $this->enabled = config('services.twilio.enabled', false);
        $this->sid     = config('services.twilio.sid', '');
        $this->token   = config('services.twilio.token', '');
        $this->from    = config('services.twilio.from', '');
    }

    /**
     * Send an SMS message.
     *
     * @param string $to   Phone number with country code (e.g. +919876543210)
     * @param string $body Message body (max 160 chars for single SMS)
     */
    public function send(string $to, string $body): bool
    {
        if (!$this->enabled) {
            Log::info('[SMS] Disabled — would send to ' . $to . ': ' . $body);
            return true;
        }

        if (!class_exists(\Twilio\Rest\Client::class)) {
            Log::warning('[SMS] Twilio SDK not installed. Run: composer require twilio/sdk');
            return false;
        }

        try {
            $client = new \Twilio\Rest\Client($this->sid, $this->token);
            $client->messages->create($to, [
                'from' => $this->from,
                'body' => $body,
            ]);
            Log::info('[SMS] Sent to ' . $to);
            return true;
        } catch (\Exception $e) {
            Log::error('[SMS] Failed to send to ' . $to . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send admission confirmation SMS.
     */
    public function sendAdmissionConfirmation(string $phone, string $studentName, string $admissionNo, string $collegeName): bool
    {
        $body = "Dear {$studentName}, your admission to {$collegeName} is confirmed. Admission No: {$admissionNo}. Welcome!";
        return $this->send($this->formatPhone($phone), $body);
    }

    /**
     * Send fee payment confirmation SMS.
     */
    public function sendFeePaymentConfirmation(string $phone, string $studentName, float $amount, string $receiptNo): bool
    {
        $body = "Dear {$studentName}, fee payment of Rs." . number_format($amount, 2) . " received. Receipt: {$receiptNo}. Thank you.";
        return $this->send($this->formatPhone($phone), $body);
    }

    /**
     * Send low attendance alert SMS.
     */
    public function sendAttendanceAlert(string $phone, string $studentName, float $percentage): bool
    {
        $body = "Alert: {$studentName}'s attendance is {$percentage}%. Minimum 75% required. Please attend regularly.";
        return $this->send($this->formatPhone($phone), $body);
    }

    /**
     * Format phone number to E.164 format for India.
     */
    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) === 10) {
            return '+91' . $phone;
        }
        if (strlen($phone) === 12 && str_starts_with($phone, '91')) {
            return '+' . $phone;
        }
        return '+' . $phone;
    }
}
