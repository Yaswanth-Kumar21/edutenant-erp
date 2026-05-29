<?php

namespace App\Mail;

use App\Models\Student;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AttendanceAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Student $student,
        public readonly Tenant  $tenant,
        public readonly float   $percentage
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Low Attendance Alert — {$this->tenant->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.attendance-alert',
        );
    }
}
