<?php

namespace App\Mail;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * CollegeAdminWelcomeMail
 *
 * Sent to the newly created College Admin with their login credentials.
 * The temporary password is passed as a plain string (never stored in DB).
 *
 * NOTE: This mailable does NOT use the Queueable trait intentionally.
 * Credentials must be delivered synchronously so that:
 *  - The email_logs table reflects real delivery status immediately.
 *  - No queue worker dependency is introduced for critical onboarding emails.
 *  - MailLogService.send() can catch and log SMTP failures in real-time.
 */
class CollegeAdminWelcomeMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public readonly User   $admin,
        public readonly Tenant $tenant,
        public readonly string $temporaryPassword
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Welcome to EduTenant ERP — Your Admin Credentials",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.college-admin-welcome',
        );
    }
}
