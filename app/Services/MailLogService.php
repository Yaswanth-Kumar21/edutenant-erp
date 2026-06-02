<?php

namespace App\Services;

use App\Models\EmailLog;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * MailLogService
 *
 * Wraps Laravel's Mail facade with automatic logging to email_logs table.
 * All outbound emails should be sent through this service.
 *
 * All emails sent via this service are delivered SYNCHRONOUSLY (no queue).
 * This ensures the email_log status reflects real delivery outcome immediately.
 */
class MailLogService
{
    /**
     * Send a mailable and log the result.
     *
     * @param  string    $to        Recipient email address
     * @param  Mailable  $mailable  The mailable instance
     * @param  int|null  $tenantId  Tenant context (nullable for system emails)
     * @return EmailLog
     */
    public static function send(string $to, Mailable $mailable, ?int $tenantId = null): EmailLog
    {
        $subject   = self::resolveSubject($mailable);
        $className = get_class($mailable);

        // ── Create a pending log entry ────────────────────────────────────────
        $log = EmailLog::create([
            'tenant_id'  => $tenantId,
            'recipient'  => $to,
            'subject'    => $subject,
            'mailable'   => $className,
            'status'     => 'queued',
        ]);

        // ── Log attempt details for debugging ─────────────────────────────────
        Log::info('MailLogService: Attempting to send email', [
            'log_id'    => $log->id,
            'to'        => $to,
            'subject'   => $subject,
            'mailable'  => $className,
            'mailer'    => config('mail.default'),
            'host'      => config('mail.mailers.smtp.host'),
            'port'      => config('mail.mailers.smtp.port'),
            'from'      => config('mail.from.address'),
            'tenant_id' => $tenantId,
        ]);

        try {
            Mail::to($to)->send($mailable);

            $log->update([
                'status'  => 'sent',
                'sent_at' => now(),
            ]);

            Log::info('MailLogService: Email sent successfully', [
                'log_id' => $log->id,
                'to'     => $to,
                'subject' => $subject,
            ]);

        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();

            $log->update([
                'status'        => 'failed',
                'error_message' => $errorMessage,
            ]);

            Log::error('MailLogService: Failed to send email', [
                'log_id'    => $log->id,
                'to'        => $to,
                'mailable'  => $className,
                'subject'   => $subject,
                'mailer'    => config('mail.default'),
                'host'      => config('mail.mailers.smtp.host'),
                'port'      => config('mail.mailers.smtp.port'),
                'from'      => config('mail.from.address'),
                'error'     => $errorMessage,
                'trace'     => $e->getTraceAsString(),
            ]);
        }

        return $log;
    }

    /**
     * Resolve the subject from the mailable's envelope.
     */
    private static function resolveSubject(Mailable $mailable): string
    {
        try {
            return $mailable->envelope()->subject ?? class_basename($mailable);
        } catch (\Throwable) {
            return class_basename($mailable);
        }
    }
}
