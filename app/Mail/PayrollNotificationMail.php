<?php

namespace App\Mail;

use App\Models\Payroll;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PayrollNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Payroll $payroll,
        public readonly Tenant  $tenant
    ) {}

    public function envelope(): Envelope
    {
        $month = date('F Y', mktime(0, 0, 0, $this->payroll->month, 1, $this->payroll->year));
        return new Envelope(
            subject: "Salary Slip for {$month} — {$this->tenant->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payroll-notification',
        );
    }
}
