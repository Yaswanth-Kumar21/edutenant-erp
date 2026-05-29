<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * WhatsAppService
 *
 * Integration-ready WhatsApp messaging service.
 * Uses Twilio WhatsApp API (sandbox or production).
 *
 * Configure in .env:
 *   WHATSAPP_ENABLED=false
 *   TWILIO_SID=ACxxxx
 *   TWILIO_TOKEN=xxxx
 *   TWILIO_WHATSAPP_FROM=whatsapp:+14155238886  (Twilio sandbox number)
 *
 * For production, use your approved WhatsApp Business number.
 */
class WhatsAppService
{
    private bool $enabled;
    private string $sid;
    private string $token;
    private string $from;

    public function __construct()
    {
        $this->enabled = config('services.whatsapp.enabled', false);
        $this->sid     = config('services.twilio.sid', '');
        $this->token   = config('services.twilio.token', '');
        $this->from    = config('services.whatsapp.from', 'whatsapp:+14155238886');
    }

    /**
     * Send a WhatsApp message via Twilio.
     */
    public function send(string $to, string $body): bool
    {
        if (!$this->enabled) {
            Log::info('[WhatsApp] Disabled — would send to ' . $to . ': ' . $body);
            return true;
        }

        $toFormatted = 'whatsapp:' . $this->formatPhone($to);

        try {
            $response = Http::withBasicAuth($this->sid, $this->token)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$this->sid}/Messages.json", [
                    'From' => $this->from,
                    'To'   => $toFormatted,
                    'Body' => $body,
                ]);

            if ($response->successful()) {
                Log::info('[WhatsApp] Sent to ' . $to);
                return true;
            }

            Log::error('[WhatsApp] Failed: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('[WhatsApp] Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send admission confirmation via WhatsApp.
     */
    public function sendAdmissionConfirmation(string $phone, string $studentName, string $admissionNo, string $collegeName): bool
    {
        $body = "🎓 *Admission Confirmed!*\n\nDear {$studentName},\n\nYour admission to *{$collegeName}* has been confirmed.\n\n*Admission No:* {$admissionNo}\n\nWelcome to the college! 🎉";
        return $this->send($phone, $body);
    }

    /**
     * Send fee payment receipt via WhatsApp.
     */
    public function sendFeeReceipt(string $phone, string $studentName, float $amount, string $receiptNo, string $collegeName): bool
    {
        $body = "✅ *Fee Payment Confirmed*\n\nDear {$studentName},\n\nYour fee payment has been received.\n\n*Amount:* ₹" . number_format($amount, 2) . "\n*Receipt No:* {$receiptNo}\n*College:* {$collegeName}\n\nThank you!";
        return $this->send($phone, $body);
    }

    /**
     * Send low attendance alert via WhatsApp.
     */
    public function sendAttendanceAlert(string $phone, string $studentName, float $percentage, string $collegeName): bool
    {
        $body = "⚠️ *Low Attendance Alert*\n\nDear Parent/Guardian,\n\n{$studentName}'s attendance at *{$collegeName}* is *{$percentage}%*.\n\nMinimum 75% attendance is required. Please ensure regular attendance.\n\nFor queries, contact the college office.";
        return $this->send($phone, $body);
    }

    /**
     * Format phone to E.164.
     */
    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) === 10) return '+91' . $phone;
        if (strlen($phone) === 12 && str_starts_with($phone, '91')) return '+' . $phone;
        return '+' . $phone;
    }
}
