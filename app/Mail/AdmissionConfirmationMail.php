<?php

namespace App\Mail;

use App\Models\Student;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdmissionConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Student $student,
        public readonly Tenant  $tenant
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Admission Confirmed — {$this->tenant->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admission-confirmation',
        );
    }
}
