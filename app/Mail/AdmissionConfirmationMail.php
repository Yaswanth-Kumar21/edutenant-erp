<?php

namespace App\Mail;

use App\Models\Student;
use App\Models\Tenant;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * AdmissionConfirmationMail
 *
 * Sent to the student's email immediately after admission is completed.
 * Contains:
 *  - Admission confirmation details (number, branch, academic year)
 *  - Student portal login credentials (email + password)
 *  - Login URL
 *
 * NOTE: No Queueable trait — sent synchronously so delivery status is known immediately.
 */
class AdmissionConfirmationMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public readonly Student $student,
        public readonly Tenant  $tenant,
        public readonly string  $loginEmail,
        public readonly string  $loginPassword
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Welcome to {$this->tenant->name} — Your Admission is Confirmed",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admission-confirmation',
        );
    }
}
